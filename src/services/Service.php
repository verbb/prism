<?php
namespace verbb\prism\services;

use verbb\prism\Prism;

use Craft;
use craft\base\Component;
use craft\helpers\Json;

class Service extends Component
{
    // Constants
    // =========================================================================

    /**
     * @var string
     */
    const CONFIG_FILENAME = 'prism';

    /**
     * @var array
     */
    const CRAFTCMS_CP_LANGUAGES = ['clike', 'markup', 'javascript', 'json'];


    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public static $componentsDefinitionFile = '@verbb/prism/resources/dist/js/prism/components.json';

    /**
     * @var object
     */
    protected $prismDefinitions;


    // Public Methods
    // =========================================================================

    public function getDefinitions(string $type): array
    {
        $definitions = [];

        foreach ($this->getPrismDefinitions($type) as $key => $value) {
            if ($key !== 'meta') {
                $definitions[$key] = $value['title'] ?? $value;
            }
        }

        return $definitions;
    }

    public function getPrismDefinitions(string $key = '')
    {
        if (empty($this->prismDefinitions)) {
            try {
                $this->prismDefinitions = $this->loadPrismDefinitions();
            } catch (\Exception $e) {
                return [];
            }
        }

        if (empty($key)) {
            return $this->prismDefinitions;
        }

        return $this->prismDefinitions[$key] ?? [];
    }

    public function parseCustomThemeDefinitions(array $definitions = []): array
    {
        // Auto set the title if only a handle is defined
        foreach ($definitions as $name => $title) {
            if ($title === '*') {
                unset($definitions[$name]);
            }

            if (is_numeric($name) && $title !== '*') {
                unset($definitions[$name]);
                $definitions[$title] = ucwords(implode(' ', explode('-', $title)));
            }
        }

        return $definitions;
    }

    public function getLanguageDefinitionRequirements(string $definition = ''): array
    {
        $languagesDefinitions = $this->getPrismDefinitions('languages');
        $requirements = $languagesDefinitions[$definition]['require'] ?? [];

        if (is_string($requirements)) {
            $requirements = explode(',', $requirements);
        }

        // Recursively parse out other requirements
        foreach ($requirements as $requirement) {
            $requirements = array_merge($requirements, $this->getLanguageDefinitionRequirements($requirement));
        }

        // Load dependencies in reverse order
        return array_reverse($requirements);
    }


    // Protected Methods
    // =========================================================================

    protected function loadPrismDefinitions()
    {
        return Json::decode(file_get_contents(Craft::getAlias(self::$componentsDefinitionFile)));
    }
}
