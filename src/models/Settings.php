<?php
/**
 * Prism Syntax Highlighting plugin for Craft CMS 3.x
 *
 * Adds a new field type that provides syntax highlighting capabilities using PrismJS.
 *
 * @link      https://www.joshsmith.dev
 * @copyright Copyright (c) 2019 Josh Smith <me@joshsmith.dev>
 */

namespace thejoshsmith\prismsyntaxhighlighting\models;

use thejoshsmith\prismsyntaxhighlighting\Plugin;

use Craft;
use craft\base\Model;

/**
 * @author    Josh Smith <me@joshsmith.dev>
 * @package   PrismSyntaxHighlighting
 * @since     1.0.0
 */
class Settings extends Model
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
       return Plugin::$plugin->prismService->getDefinitions('themes');
    }

    public function getEditorLanguages()
    {
        return Plugin::$plugin->prismService->getDefinitions('languages');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['editorTheme', 'string'],
            ['editorLanguage', 'string'],
            ['editorHeight', 'string'],
            ['editorHeight', 'default', 'value' => '4'],
            ['editorTabWidth', 'string'],
            ['editorTabWidth', 'default', 'value' => '4'],
            ['editorLineNumbers', 'boolean'],
            ['editorLineNumbers', 'default', 'value' => false],
            ['customThemesDir', 'string']
        ];
    }
}
