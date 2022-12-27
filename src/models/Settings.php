<?php
namespace verbb\prism\models;

use verbb\prism\Prism;

use Craft;
use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string
     */
    public $editorTheme = '';

    /**
     * @var string
     */
    public $editorLanguage = '';

    /**
     * @var array
     */
    public $editorThemes = [];

    /**
     * @var array
     */
    public $editorLanguages = [];

    /**
     * @var string
     */
    public $editorHeight = '4';

    /**
     * @var string
     */
    public $editorTabWidth = '4';

    /**
     * @var string
     */
    public $editorLineNumbers = false;

    /**
     * @var string
     */
    public $customThemesDir = '';


    // Public Methods
    // =========================================================================

    public function getEditorThemes()
    {
        $userThemes = [];
        $themes = Prism::$plugin->getService()->getDefinitions('themes');

        if (!empty($this->customThemesDir)) {
            $prismConfig = Prism::$plugin->getService()->getConfig('themes');
            $userThemes = array_diff_key($prismConfig, $themes);
            $userThemes = Prism::$plugin->getService()->parseCustomThemeDefinitions($userThemes);
        }

        return array_merge($themes, $userThemes);
    }

    public function getEditorLanguages()
    {
        return Prism::$plugin->getService()->getDefinitions('languages');
    }


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules = array_merge($rules, [
            ['editorTheme', 'string'],
            ['editorLanguage', 'string'],
            ['editorHeight', 'string'],
            ['editorHeight', 'default', 'value' => '4'],
            ['editorTabWidth', 'string'],
            ['editorTabWidth', 'default', 'value' => '4'],
            ['editorLineNumbers', 'boolean'],
            ['editorLineNumbers', 'default', 'value' => false],
            ['customThemesDir', 'string'],
        ]);

        return $rules;
    }

}
