<?php
namespace verbb\prism\fields;

use verbb\prism\Prism;
use verbb\prism\assetbundles\field\PrismAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Html;
use craft\helpers\Json;

use yii\db\Schema;

class PrismField extends Field
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('prism', 'Prism Syntax Highlighting');
    }

    public static function dbType(): string
    {
        return Schema::TYPE_TEXT;
    }


    // Properties
    // =========================================================================

    public string $editorTheme = '';
    public string $editorThemeFile = '';
    public string $editorLanguage = '';
    public array $editorLanguageFiles = [];
    public string $editorHeight = '4';
    public string $editorTabWidth = '4';
    public bool $editorLineNumbers = false;


    // Public Methods
    // =========================================================================

    public function getSettingsHtml(): ?string
    {
        $settings = Prism::$plugin->getSettings();

        // Set defaults for the field, from the plugin settings
        if (!$this->id) {
            $this->editorTheme = $settings->editorTheme;
            $this->editorLanguage = $settings->editorLanguage;
            $this->editorHeight = $settings->editorHeight;
            $this->editorTabWidth = $settings->editorTabWidth;
            $this->editorLineNumbers = $settings->editorLineNumbers;
        }

        return Craft::$app->getView()->renderTemplate('prism/_field/settings', [
            'field' => $this,
            'settings' => $settings,
        ]);
    }

    public function beforeSave(bool $isNew): bool
    {
        $service = Prism::$plugin->getService();
        $filesService = Prism::$plugin->getFiles();
        $prismSettings = Prism::$plugin->getSettings();

        // Store the fully qualified path
        $this->editorThemeFile = $filesService->getEditorFile($this->editorTheme . '.css', $filesService::PRISM_THEMES_DIR, $prismSettings->customThemesDir);

        // Load the language requirements
        $editorLanguageFileRequirements = $service->getLanguageDefinitionRequirements($this->editorLanguage);
        $editorLanguageFileDefinitions = array_merge($editorLanguageFileRequirements, [$this->editorLanguage]);

        // Loop all language requirements and resolve the filepaths
        foreach ($editorLanguageFileDefinitions as $file) {
            $filename = 'prism-' . $file . '.min.js';

            $this->editorLanguageFiles[] = $filesService->getEditorFile($filename, $filesService::PRISM_LANGUAGES_DIR);
        }

        return parent::beforeSave($isNew);
    }

    protected function inputHtml(mixed $value, ?ElementInterface $element, bool $inline): string
    {
        $filesService = Prism::$plugin->getFiles();
        $view = Craft::$app->getView();

        // Load asset bundles
        $prismAsset = $view->registerAssetBundle(PrismAsset::class);
        $filesService->registerEditorThemeAssetBundle($this->editorThemeFile);
        $filesService->registerEditorLanguageAssetBundle($this->editorLanguageFiles);

        // Register the line numbers plugin js and css
        if ($this->editorLineNumbers) {
            $prismAsset->js[] = 'js/prism/plugins/line-numbers/prism-line-numbers.min.js';
            $prismAsset->css[] = 'js/prism/plugins/line-numbers/prism-line-numbers.css';
        }

        // Get our id and namespace
        $id = Html::id($this->handle);
        $namespacedId = $view->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => $view->namespaceInputId(''),
        ];

        $jsonVars = Json::encode($jsonVars);
        $view->registerJs("$('#{$namespacedId}-field').prismField(" . $jsonVars . ");");

        return $view->renderTemplate('prism/_field/input', [
            'name' => $this->handle,
            'value' => $value,
            'field' => $this,
            'id' => $id,
            'namespacedId' => $namespacedId,
            'editorLineNumbers' => $this->editorLineNumbers,
            'editorLanguageClass' => $this->getLanguageClass(),
            'editorHeight' => $this->editorHeight,
            'editorTabWidth' => $this->editorTabWidth,
        ]);
    }

    public function getThemeName(): string
    {
        return $this->editorTheme;
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
        ]);

        return $rules;
    }

    protected function getLanguageClass(): string
    {
        return 'language-' . $this->editorLanguage;
    }
}
