<?php
/**
 * Prism Syntax Highlighting - Files Service
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
    /**
     * Constant filepaths
     * @var string
     */
    const PRISM_THEMES_DIR = '@thejoshsmith/prismsyntaxhighlighting/assetbundles/prismsyntaxhighlighting/dist/css/prism/themes';
    const PRISM_LANGUAGES_DIR = '@thejoshsmith/prismsyntaxhighlighting/assetbundles/prismsyntaxhighlighting/dist/js/prism/components';

    /**
     * Returns a fully qualified filepath for the passed filename
     * Optionally searches in a custom specified directory (for user files)
     *
     * @author Josh Smith <me@joshsmith.dev>
     * @param  string $filename
     * @param  string $dir
     * @param  string $customDir
     * @return string
     */
    public function getEditorFile(string $filename, string $dir,  string $customDir = ''): string
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

    /**
     * Retunrs a theme asset bundle
     * @author Josh Smith <me@joshsmith.dev>
     * @param  string $filename
     * @return AssetBundle
     */
    public function registerEditorThemeAssetBundle(string $filename)
    {
        $am = Craft::$app->getAssetManager();

        $themeAssetBundle = Craft::$app->getView()->registerAssetBundle(PrismJsThemeAsset::class);
        $themeAssetBundle->sourcePath = str_replace(basename($filename), '', $filename);
        $themeAssetBundle->css[] = basename($filename);
        $themeAssetBundle->init();
        $themeAssetBundle->publish($am);

        return $themeAssetBundle;
    }

    /**
     * Retunrs a languages asset bundle
     * @author Josh Smith <me@joshsmith.dev>
     * @param  array $files
     * @return AssetBundle
     */
    public function registerEditorLanguageAssetBundle(array $files)
    {
        $am = Craft::$app->getAssetManager();

        $assetBundle = Craft::$app->getView()->registerAssetBundle(PrismJsLanguageAsset::class);
        $assetBundle->sourcePath = self::PRISM_LANGUAGES_DIR;

        foreach ($files as $filepath) {
            $assetBundle->js[] = basename($filepath);
        }

        $assetBundle->init();
        $assetBundle->publish($am);

        return $assetBundle;
    }

    /**
     * Parses out a prism name into a human readable name
     * @author Josh Smith <me@joshsmith.dev>
     * @param  string $name
     * @return string
     */
    protected function parsePrismName(string $name): string
    {
        $name = explode('-', $name);

        if( count($name) > 1 ){
            array_shift($name);
        }

        return ucwords(implode(' ', $name));
    }

    /**
     * Returns all files matching the passed filters
     * @author Josh Smith <me@joshsmith.dev>
     * @param  string $dir     Directory to search
     * @param  array  $options Options including closure filters
     * @param  string $regexp  Regexp to filter files on
     * @return array           An array of found files
     */
    public function getFiles(string $dir, array $options = [], string $regexp = ''): array
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

    /**
     * Returns editor files
     * @author Josh Smith <me@joshsmith.dev>
     * @param  string $filepath
     * @param  array  $options
     * @return array
     */
    protected function getEditorFiles(string $filepath, array $options = []): array
    {
        $files = FileHelper::findFiles(Craft::getAlias($filepath), $options);
        return $files ?? [];
    }

    /**
     * Converts paths to Yii aliases
     * @author Josh Smith <me@joshsmith.dev>
     * @param  string $alias
     * @param  array  $files
     * @return array
     */
    private function _convertToAliasedPaths(string $alias, array $files): array
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
