<?php
/**
 * Prism Syntax Highlighting plugin for Craft CMS 3.x
 *
 * Adds a new field type that provides syntax highlighting capabilities using PrismJS.
 *
 * @link      https://www.joshsmith.dev
 * @copyright Copyright (c) 2019 Josh Smith <me@joshsmith.dev>
 */

namespace thejoshsmith\prismsyntaxhighlighting\assetbundles\PrismSyntaxHighlighting;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

use thejoshsmith\prismsyntaxhighlighting\assetbundles\PrismSyntaxHighlighting\PrismJsAsset;

/**
 * @author    Josh Smith <me@joshsmith.dev>
 * @package   PrismSyntaxHighlighting
 * @since     1.0.0
 */
class PrismSyntaxHighlightingAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@thejoshsmith/prismsyntaxhighlighting/assetbundles/prismsyntaxhighlighting/dist";

        $this->depends = [
            CpAsset::class,
            PrismJsAsset::class,
        ];

        $this->js = [
            'js/bililiteRange/bililiteRange.js',
            'js/bililiteRange/bililiteRange.fancytext.js',
            'js/bililiteRange/bililiteRange.undo.js',
            'js/bililiteRange/bililiteRange.util.js',
            'js/bililiteRange/jquery.sendkeys.js',
            'js/PrismSyntaxHighlighting.js'
        ];

        $this->css = [
            'css/PrismSyntaxHighlighting.css',
        ];

        parent::init();
    }
}
