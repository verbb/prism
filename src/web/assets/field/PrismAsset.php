<?php
namespace verbb\prism\web\assets\field;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PrismAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/prism/web/assets/field/dist';

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
            'prism.js',
        ];

        $this->css = [
            'prism.css',
        ];

        parent::init();
    }
}
