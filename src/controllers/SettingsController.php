<?php
namespace verbb\invoiced\controllers;


use nethaven\invoiced\Invoiced;
use nethaven\invoiced\models\Settings;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class SettingsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSaveSettings(): ?Response
    {
        $this->requirePostRequest();

        $request = $this->request;

        /** @var Settings $settings */
        $settings = Invoiced::$plugin->getSettings();
        $settings->setAttributes($request->getParam('settings'), false);

        if (!$settings->validate()) {
            $this->setFailFlash(Craft::t('invoiced', 'Couldn’t save settings.'));

            Craft::$app->getUrlManager()->setRouteParams(['settings' => $settings,]);

            return null;
        }

        $pluginSettingsSaved = Craft::$app->getPlugins()->savePluginSettings(Invoiced::$plugin, $settings->toArray());

        if (!$pluginSettingsSaved) {
            $this->setFailFlash(Craft::t('invoiced', 'Couldn’t save settings.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'settings' => $settings,
            ]);

            return null;
        }

        $this->setSuccessFlash(Craft::t('invoiced', 'Settings saved.'));

        return $this->redirectToPostedUrl();
    }

}