<?php
/**
 * Prism Syntax Highlighting
 *
 * @link      https://www.joshsmith.dev
 * @copyright Copyright (c) 2019 Josh Smith
 */

namespace thejoshsmith\prismsyntaxhighlighting\services;

use Craft;
use craft\base\Component;
use yii\helpers\FileHelper;
use yii\web\AssetBundle;
use thejoshsmith\prismsyntaxhighlighting\assetbundles\PrismSyntaxHighlighting\PrismJsThemeAsset;
use thejoshsmith\prismsyntaxhighlighting\assetbundles\PrismSyntaxHighlighting\PrismJsLanguageAsset;
use thejoshsmith\prismsyntaxhighlighting\Plugin;

/**
 * Prism Syntax highlighting Files Service
 * @author    Josh Smith
 * @package   Prism Syntax Highlighting
 * @since     1.0.0
 */
class Files extends Component
{
    const PRISM_THEMES_DIR = '@thejoshsmith/prismsyntaxhighlighting/assetbundles/prismsyntaxhighlighting/dist/css/prism/themes';
    const PRISM_LANGUAGES_DIR = '@thejoshsmith/prismsyntaxhighlighting/assetbundles/prismsyntaxhighlighting/dist/js/prism/components';

    public function getEditorFile($filename, $dir,  $customDir = '')
    {
        // Define a file filter
        $filter = function($file) use($filename){ return basename($file) === $filename; };

        // Attempt to get a vendor file
        $files = $this->getEditorFiles($dir, ['filter' => $filter]);
        $files = $this->_convertToAliasedPaths($dir, $files);

        if( !empty($files) || empty($customDir) ) return $files[0] ?? '';

        // Check custom directories...
        $customFiles = $this->getEditorFiles($customDir, ['filter' => $filter]);
        if( empty($customFiles) ) return '';

        return $customFiles[0];
    }

    public function registerEditorThemeAssetBundle($filename)
    {
        $am = Craft::$app->getAssetManager();

        $themeAssetBundle = Craft::$app->getView()->registerAssetBundle(PrismJsThemeAsset::class);
        $themeAssetBundle->sourcePath = str_replace(basename($filename), '', $filename);
        $themeAssetBundle->css[] = basename($filename);
        $themeAssetBundle->init();
        $themeAssetBundle->publish($am);

        return $themeAssetBundle;
    }

    public function registerEditorLanguageAssetBundle()
    {
        $am = Craft::$app->getAssetManager();
        $prismService = Plugin::$plugin->prismService;

        $assetBundle = Craft::$app->getView()->registerAssetBundle(PrismJsLanguageAsset::class);
        $assetBundle->sourcePath = self::PRISM_LANGUAGES_DIR;

        foreach ($prismService->getLangConfig() as $file => $displayName) {
            $assetBundle->js[] = $file.'.min.js';
        }

        $assetBundle->init();
        $assetBundle->publish($am);

        return $assetBundle;
    }

    protected function parsePrismName($name)
    {
        $name = explode('-', $name);

        if( count($name) > 1 ){
            array_shift($name);
        }

        return ucwords(implode(' ', $name));
    }

    public function getFiles($dir, $options = [], $regexp = '')
    {
        try {
            $baseFiles = $this->getEditorFiles($dir, $options);
        } catch (\yii\base\InvalidArgumentException $iae) {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t load files in \''.$dir.'\'.'));
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

    protected function getEditorFiles($filepath, $options = [])
    {
        $files = FileHelper::findFiles(Craft::getAlias($filepath), $options);
        return $files ?? [];
    }

    private function _convertToAliasedPaths(string $alias, array $files)
    {
        foreach ($files as $i => $file) {

            // Determine if the aliased filepath is different
            $aliasPath = Craft::getAlias($alias);
            $replacedFile = str_replace($aliasPath, '', $file);

            // If so, replace the filepath with the alias
            if( strlen($replacedFile) !== $file ){
                $files[$i] = $alias.$replacedFile;
            }
        }

        return $files;
    }
}
