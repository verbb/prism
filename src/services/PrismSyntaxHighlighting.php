<?php

namespace thejoshsmith\prismsyntaxhighlighting\services;

use Craft;
use craft\base\Component;
use thejoshsmith\prismsyntaxhighlighting\Plugin;

/**
 * Prism Syntax Highlighting
 *
 * @link      https://www.joshsmith.dev
 * @copyright Copyright (c) 2019 Josh Smith
 */
class PrismSyntaxHighlighting extends Component
{
	/**
	 * Define the config file that holds the language definitions
	 * @var string
	 */
	public static $langConfigFile = 'prismlanguages';

	/**
	 * Caches lang configs
	 * @var array
	 */
	protected $langConfig = [];

	public function getEditorThemes($customThemesDir = '')
    {
    	$prismFilesService = Plugin::$plugin->prismFilesService;

        $regexp = '/\.css/';
        $baseFiles = $prismFilesService->getFiles($prismFilesService::PRISM_THEMES_DIR, ['only'=>['*.css']], $regexp);
        $customFiles = (empty($customThemesDir) ? [] : $prismFilesService->getFiles($customThemesDir, [], $regexp));

        return array_merge($baseFiles, $customFiles);
    }

    public function getEditorLanguages()
    {
    	return $this->getLangConfig();
    }

    public function getLangConfig()
    {
    	if( empty($this->config) ){
			$defaultConfig = $this->loadDefaultLangConfig();
			$userConfig = $this->loadUserLangConfig();
			$this->langConfig = array_merge($defaultConfig, $userConfig);
    	}

    	return $this->langConfig;
    }

    protected function loadDefaultLangConfig()
    {
    	return require Craft::getAlias('@thejoshsmith/prismsyntaxhighlighting/config/').self::$langConfigFile.'.php';
    }

    protected function loadUserLangConfig()
    {
    	$configFile = CRAFT_BASE_PATH.'/config/'.self::$langConfigFile.'.php';

    	if( file_exists($configFile) ){
    		return require $configFile;
    	}

    	return [];
    }
}