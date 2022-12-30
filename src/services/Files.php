<?php
namespace verbb\prism\services;

use verbb\prism\assetbundles\field\PrismJsThemeAsset;
use verbb\prism\assetbundles\field\PrismJsLanguageAsset;

use Craft;
use craft\base\Component;

use yii\base\InvalidArgumentException;
use yii\helpers\FileHelper;
use yii\web\AssetBundle;

class Files extends Component
{
    // Constants
    // =========================================================================

    public const PRISM_THEMES_DIR = '@verbb/prism/resources/dist/css/prism/themes';
    public const PRISM_LANGUAGES_DIR = '@verbb/prism/resources/dist/js/prism/components';


    // Public Methods
    // =========================================================================

    public function getEditorFile(string $filename, string $dir, string $customDir = ''): string
    {
        // Define a file filter
        $filter = function($file) use ($filename) {
            return basename($file) === $filename;
        };

        // Attempt to get a vendor file
        $files = $this->getEditorFiles($dir, ['filter' => $filter]);
        $files = $this->_convertToAliasedPaths($dir, $files);

        if (!empty($files) || empty($customDir)) {
            return $files[0] ?? '';
        }

        // Check custom directories...
        $customFiles = $this->getEditorFiles($customDir, ['filter' => $filter]);
        if (empty($customFiles)) {
            return '';
        }

        return $customFiles[0];
    }

    public function registerEditorThemeAssetBundle(string $filename): AssetBundle
    {
        $assetManager = Craft::$app->getAssetManager();

        $themeAssetBundle = Craft::$app->getView()->registerAssetBundle(PrismJsThemeAsset::class);
        $themeAssetBundle->sourcePath = str_replace(basename($filename), '', $filename);
        $themeAssetBundle->css[] = basename($filename);
        $themeAssetBundle->init();
        $themeAssetBundle->publish($assetManager);

        return $themeAssetBundle;
    }

    public function registerEditorLanguageAssetBundle(array $files): AssetBundle
    {
        $assetManager = Craft::$app->getAssetManager();

        $assetBundle = Craft::$app->getView()->registerAssetBundle(PrismJsLanguageAsset::class);
        $assetBundle->sourcePath = self::PRISM_LANGUAGES_DIR;

        // Load Craft required CP languages
        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $craftCpLanguages = [];

            foreach (Service::CRAFTCMS_CP_LANGUAGES as $file) {
                $filename = 'prism-' . $file . '.min.js';
                
                $craftCpLanguages[] = $this->getEditorFile($filename, self::PRISM_LANGUAGES_DIR);
            }

            $files = array_unique(array_merge($files, $craftCpLanguages));
        }

        foreach ($files as $filepath) {
            $assetBundle->js[] = basename($filepath);
        }

        $assetBundle->init();
        $assetBundle->publish($assetManager);

        return $assetBundle;
    }

    public function getFiles(string $dir, array $options = [], string $regexp = ''): array
    {
        try {
            $baseFiles = $this->getEditorFiles($dir, $options);
        } catch (InvalidArgumentException $e) {
            Craft::$app->getSession()->setError(Craft::t('app', "Couldn’t load files in $dir."));
            $baseFiles = [];
        }

        $files = [];

        foreach ($baseFiles as $file) {
            $baseFile = basename($file);

            // Parse the theme name
            $name = preg_replace($regexp, '', $baseFile);
            $name = $this->parsePrismName($name);

            $files[$baseFile] = $name;
        }

        ksort($files);

        return $files;
    }


    // Protected Methods
    // =========================================================================

    protected function parsePrismName(string $name): string
    {
        $name = explode('-', $name);

        if (count($name) > 1) {
            array_shift($name);
        }

        return ucwords(implode(' ', $name));
    }

    protected function getEditorFiles(string $filepath, array $options = []): array
    {
        $files = FileHelper::findFiles(Craft::getAlias($filepath), $options);

        return $files ?? [];
    }


    // Private Methods
    // =========================================================================

    private function _convertToAliasedPaths(string $alias, array $files): array
    {
        foreach ($files as $i => $file) {
            // Determine if the aliased filepath is different
            $aliasPath = Craft::getAlias($alias);
            $replacedFile = str_replace($aliasPath, '', $file);

            // If so, replace the filepath with the alias
            if (strlen($replacedFile) !== $file) {
                $files[$i] = $alias . $replacedFile;
            }
        }

        return $files;
    }
}
