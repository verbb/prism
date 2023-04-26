<?php
namespace verbb\prism;

use verbb\prism\base\PluginTrait;
use verbb\prism\fields\PrismField;
use verbb\prism\models\Settings;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\TemplateEvent;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\web\assets\prismjs\PrismJsAsset;
use craft\web\UrlManager;
use craft\web\View;

use yii\base\Event;

class Prism extends Plugin
{
    // Properties
    // =========================================================================

    public string $schemaVersion = '3.0.0';
    public bool $hasCpSettings = true;


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_setPluginComponents();
        $this->_setLogging();
        $this->_registerCpRoutes();
        $this->_registerFieldTypes();

        Event::on(View::class, View::EVENT_BEFORE_RENDER_TEMPLATE, function(TemplateEvent $event) {
            if (!Craft::$app->getRequest()->getIsCpRequest()) {
                return;
            }

            $assetBundles =& $event->sender->assetBundles;
            $prismJsAsset = PrismJsAsset::class;

            // Prevent Craft from loading the CMS version of PrismJS.
            // We make sure to load the languages Craft requires in the Plugin.
            if (array_key_exists($prismJsAsset, $assetBundles)) {
                $assetBundles[$prismJsAsset]->js = [];
            }
        });
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('prism/settings'));
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'prism/settings' => 'prism/base/settings',
            ]);
        });
    }

    private function _registerFieldTypes(): void
    {
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = PrismField::class;
        });
    }
}
