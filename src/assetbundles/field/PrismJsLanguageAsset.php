<?php
namespace verbb\prism\assetbundles\field;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class PrismJsLanguageAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->depends = [
            CpAsset::class,
        ];

        parent::init();
    }
}
