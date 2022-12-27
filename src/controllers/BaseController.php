<?php
namespace verbb\prism\controllers;

use verbb\prism\Prism;

use craft\web\Controller;

use yii\web\Response;

class BaseController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response
    {
        $settings = Prism::$plugin->getSettings();

        return $this->renderTemplate('prism/settings', [
            'settings' => $settings,
        ]);
    }

}