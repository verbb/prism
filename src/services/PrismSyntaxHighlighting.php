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
    const CONFIG_FILENAME = 'prismsyntaxhighlighting';

    /**
     * The intenral path to the plugin config file
     * @var string
     */
    public static $configFile = '@thejoshsmith/prismsyntaxhighlighting/config/'.self::CONFIG_FILENAME.'.php';

    /**
     * Path to the Prism components definition file
     * @var string
     */
	public static $componentsDefinitionFile = '@thejoshsmith/prismsyntaxhighlighting/assetbundles/prismsyntaxhighlighting/dist/js/prism/components.json';

    /**
     * Static cache to hold the loaded definitions
     * @var object
     */
    protected $prismDefinitions;

	/**
	 * Caches lang configs
	 * @var array
	 */
	protected $config = [];

    /**
     * Returns parsed Prism definitions for the given type
     *
     * Allowed types are:
     *     - 'core'
     *     - 'themes'
     *     - 'languages'
     *     - 'plugins'
     *
     * Data is returned in the following format:
     *     {{Internal Definition}} => {{Display Title}}
     *
     * @author Josh Smith <josh.smith@platocreative.co.nz>
     * @param  string $type Type of definition to return
     * @return array        An array of Prism definitions
     */
    public function getDefinitions($type = '')
    {
        if( empty($type) ) return [];

        $config = $this->getConfig($type);
        $definitions = $this->getPrismDefinitions($type);

        return $this->parseDefinitions($config, $definitions);
    }

    /**
     * Returns the prism syntax highlighting config
     * Optionally returns a segment of the config, specified by a key
     * User config settings are automatically merged into the default config
     *
     * @author Josh Smith <josh.smith@platocreative.co.nz>
     * @param  string $key Key of a segment to return
     * @return array       Config array
     */
    public function getConfig($key = '')
    {
    	if( empty($this->config) ){
			$defaultConfig = $this->loadDefaultConfig();
			$userConfig = $this->loadUserConfig();
			$this->config = array_merge($defaultConfig, $userConfig);
    	}

        if( empty($key) ) return $this->config;
        if( array_key_exists($key, $this->config) ) return $this->config[$key];

        return [];
    }

    /**
     * Returns the prism JSON definitions
     * @author Josh Smith <josh.smith@platocreative.co.nz>
     * @return Object
     */
    public function getPrismDefinitions($key = '')
    {
        if( empty($this->prismDefinitions) ){
            try {
                $this->prismDefinitions = $this->loadPrimDefinitions();
            } catch (\Exception $e) {
                return [];
            }
        }

        if( empty($key) ) return $this->prismDefinitions;
        if( property_exists($this->prismDefinitions, $key) ) return $this->prismDefinitions->{$key};

        return [];
    }

    public function getLanguageDefinitionRequirements(string $definition = '', &$definitions = [])
    {
        $languagesDefinitions = $this->getPrismDefinitions('languages');
        $languageDefinitions = $languagesDefinitions->$definition ?? [];
        $requirements = $languageDefinitions->require ?? [];

        // Add the definition to the array
        $definitions[] = $definition;

        if( empty($requirements) ) return $definitions;
        if( is_string($requirements) ) $requirements = explode(',', $requirements);

        // Recursively parse out other requirements
        foreach ($requirements as $requirement) {
            $this->getLanguageDefinitionRequirements($requirement, $definitions);
        }

        // Load dependencies in reverse order
        return array_reverse($definitions);
    }

    protected function loadPrimDefinitions()
    {
        return json_decode(file_get_contents(Craft::getAlias(self::$componentsDefinitionFile)));
    }

    protected function loadDefaultConfig()
    {
    	return require Craft::getAlias(self::$configFile);
    }

    protected function loadUserConfig()
    {
    	$configFile = CRAFT_BASE_PATH.'/config/'.self::CONFIG_FILENAME.'.php';

    	if( file_exists($configFile) ){
    		return require $configFile;
    	}

    	return [];
    }

    protected function parseDefinitions(array $config, object $themeDefinitions)
    {
        // Remove the meta property, it's not required.
        unset($themeDefinitions->meta);

        $definitions = [];
        foreach ($config as $value) {
            if( $value === '*' ){ // Load all definitions
                $definitions = (array) $themeDefinitions;
                break;
            } else if( !empty($themeDefinitions->{$value}) ){
                $definitions[$value] = $themeDefinitions->{$value};
            }
        }

        return $this->_parseDefinitions($definitions);
    }

    private function _parseDefinitions(array $definitions = [])
    {
        $parsedDefinitions = [];
        foreach ($definitions as $key => $value) {
            if( is_string($value) ){
                $parsedDefinitions[$key] = $value;
            }
            else if( is_object($value) && !empty($value->title) ){
                $parsedDefinitions[$key] = $value->title;
            } else {
                throw new \Exception('Definition is missing a title.');
            }
        }

        return $parsedDefinitions;
    }
}
