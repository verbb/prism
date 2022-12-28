<?php
namespace verbb\prism\assetbundles\field;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PrismAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/prism/resources/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            // Core Prism Scripts
            'js/prism/components/prism-core.min.js',

            // Keypress management
            'js/bililiteRange/bililiteRange.js',
            'js/bililiteRange/bililiteRange.fancytext.js',
            'js/bililiteRange/bililiteRange.undo.js',
            'js/bililiteRange/bililiteRange.util.js',
            'js/bililiteRange/jquery.sendkeys.js',

            // Main Script
            'js/prism.js',
        ];

        $this->css = [
            'css/prism.css',
        ];

        parent::init();
    }
}
