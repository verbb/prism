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

use thejoshsmith\prismsyntaxhighlighting\Plugin;

/**
 * @author    Josh Smith <me@joshsmith.dev>
 * @package   PrismSyntaxHighlighting
 * @since     1.0.0
 */
class PrismJsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@prismjs/prism";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'prism.js'
        ];

        parent::init();
    }
}
