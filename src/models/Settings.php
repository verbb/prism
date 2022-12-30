<?php
namespace verbb\prism\models;

use verbb\prism\Prism;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $editorTheme = '';
    public string $editorLanguage = '';
    public array $editorThemes = [];
    public array $editorLanguages = [];
    public string $editorHeight = '4';
    public string $editorTabWidth = '4';
    public bool $editorLineNumbers = false;
    public string $customThemesDir = '';


    // Public Methods
    // =========================================================================

    public function getEditorThemes(): array
    {
        return Prism::$plugin->getService()->getDefinitions('themes');
    }

    public function getEditorLanguages(): array
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
