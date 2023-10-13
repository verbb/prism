<?php
namespace verbb\prism\base;

use verbb\prism\Prism;
use verbb\prism\services\Files;
use verbb\prism\services\Service;

use verbb\base\LogTrait;
use verbb\base\helpers\Plugin;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static ?Prism $plugin = null;


    // Traits
    // =========================================================================

    use LogTrait;
    

    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Plugin::bootstrapPlugin('prism');

        return [
            'components' => [
                'files' => Files::class,
                'service' => Service::class,
            ],
        ];
    }


    // Public Methods
    // =========================================================================

    public function getFiles(): Files
    {
        return $this->get('files');
    }

    public function getService(): Service
    {
        return $this->get('service');
    }

}