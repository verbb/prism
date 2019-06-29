<?php
/**
 * Prism Syntax Highlighting plugin for Craft CMS 3.x
 *
 * Adds a new field type that provides syntax highlighting capabilities using PrismJS.
 *
 * @link      https://www.joshsmith.dev
 * @copyright Copyright (c) 2019 Josh Smith <me@joshsmith.dev>
 */

namespace thejoshsmith\prismsyntaxhighlighting\fields;

use thejoshsmith\prismsyntaxhighlighting\Plugin;
use thejoshsmith\prismsyntaxhighlighting\assetbundles\prismsyntaxhighlighting\PrismSyntaxHighlightingAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;
use yii\web\AssetBundle;

/**
 * @author    Josh Smith <me@joshsmith.dev>
 * @package   PrismSyntaxHighlighting
 * @since     1.0.0
 */
class PrismSyntaxHighlightingField extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $editorTheme = '';

    /**
     * @var string
     */
    public $editorThemeFile = '';

    /**
     * @var string
     */
    public $editorLanguage = '';

    /**
     * @var string
     */
    public $editorLanguageFiles = [];

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

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('craft-prism-syntax-highlighting', 'Prism Syntax Highlighting');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
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

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        $settings = Plugin::$plugin->getSettings();
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'craft-prism-syntax-highlighting/_components/fields/PrismSyntaxHighlightingField_settings',
            [
                'field' => $this,
                'defaults' => $settings
            ]
        );
    }

    /**
     * Resolves filepaths for this field's themes and languages
     * It's more efficient to do this here, rather than dynamically processing on load of each field
     *
     * @author Josh Smith <me@joshsmith.dev>
     * @param  bool   $isNew
     * @return void
     */
    public function beforeSave(bool $isNew): bool
    {
        $prismService = Plugin::$plugin->prismService;
        $prismFilesService = Plugin::$plugin->prismFilesService;
        $prismSettings = Plugin::$plugin->getSettings();

        // Store the fully qualified path
        $this->editorThemeFile = $prismFilesService->getEditorFile(
            $this->editorTheme.'.css', // Ok to hardcode here, it's the only place it's used.
            $prismFilesService::PRISM_THEMES_DIR,
            $prismSettings->customThemesDir
        );

        // Load the language requirements
        $editorLanguageFileRequirements = $prismService->getLanguageDefinitionRequirements($this->editorLanguage);
        $editorLanguageFileDefinitions = array_merge($editorLanguageFileRequirements, [$this->editorLanguage]);

        // Loop all language requirements and resolve the filepaths
        foreach ($editorLanguageFileDefinitions as $file) {
            $filename = 'prism-'.$file.'.min.js'; // Ok to hardcode here, it's the only place it's used.
            $this->editorLanguageFiles[] = $prismFilesService->getEditorFile(
                $filename,
                $prismFilesService::PRISM_LANGUAGES_DIR
            );
        }

        return parent::beforeSave($isNew);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $settings = Plugin::$plugin->getSettings();
        $prismFilesService = Plugin::$plugin->prismFilesService;

        // Load asset bundles
        $prismSyntaxHighlightingAsset = Craft::$app->getView()->registerAssetBundle(PrismSyntaxHighlightingAsset::class);
        $themeAssetBundle = $prismFilesService->registerEditorThemeAssetBundle($this->editorThemeFile);
        $languageAssetBundle = $prismFilesService->registerEditorLanguageAssetBundle($this->editorLanguageFiles);

        // Register the line numbers plugin js and css
        if( $this->editorLineNumbers === '1' ){
            $prismSyntaxHighlightingAsset->js[] = 'js/prism/plugins/line-numbers/prism-line-numbers.min.js';
            $prismSyntaxHighlightingAsset->css[] = 'js/prism/plugins/line-numbers/prism-line-numbers.css';
        }

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
        ];

        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').PrismSyntaxHighlightingField(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'craft-prism-syntax-highlighting/_components/fields/PrismSyntaxHighlightingField_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'editorLineNumbers' => $this->editorLineNumbers,
                'editorLanguageClass' => $this->getLanguageClass(),
                'editorHeight' => $this->editorHeight,
                'editorTabWidth' => $this->editorTabWidth
            ]
        );
    }

    public function getThemeName()
    {
        return $this->editorTheme;
    }

    protected function getLanguageClass()
    {
        return 'language-'.$this->editorLanguage;
    }
}
