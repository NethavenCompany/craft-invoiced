<?php
namespace nethaven\invoiced;

use Craft;
use craft\base\Plugin;
use craft\base\Event as Event;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;

use nethaven\invoiced\base\PluginTrait;
use nethaven\invoiced\base\Routes;
use nethaven\invoiced\models\Settings;
use nethaven\invoiced\services\InvoiceTemplates;
use nethaven\invoiced\services\Invoices;


class Invoiced extends Plugin
{
    // Properties
    // =========================================================================

    public static Invoiced $plugin;
    public bool $hasCpSection = true;
    public string $schemaVersion = '1.0.0';
    public string $pluginName = "Invoiced";


    // Traits
    // =========================================================================

    use PluginTrait;


    // Initialize
    // =========================================================================

    public static function config(): array
    {
        return [
            'components' => [
                'invoiceTemplates' => InvoiceTemplates::class,
                'invoices' => Invoices::class,
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        Craft::$app->onInit(function () {
            $this->_registerVariables();
            $this->_registerCpRoutes();
        });
        
    }


    // Public Methods
    // =========================================================================

    public function getInvoiceTemplates(): InvoiceTemplates
    {
        return $this->get('invoiceTemplates');
    }

    public function getInvoices(): Invoices
    {
        return $this->get('invoices');
    }

    public function getCpNavItem(): ?array
    {
        $nav = parent::getCpNavItem();

        $nav['label'] = $this->getPluginName();

        $nav['subnav']['invoices'] = [
            'label' => 'Invoices',
            'url' => 'invoiced/invoices',
        ];

        $nav['subnav']['settings'] = [
            'label' => 'Settings',
            'url' => 'invoiced/settings',
        ];

        return $nav;
    }

    public function getPluginName(): string
    {
        return Invoiced::$plugin->getSettings()->pluginName;
    }


    // Protected / Private Methods
    // =========================================================================

    public function _registerCpRoutes(): void
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['invoiced'] = ['template' =>'invoiced/index'];
                $event->rules['invoiced/invoices'] = [ 'template' => 'invoiced/invoices'];
                $event->rules['invoiced/invoices/new'] = [ 'template' => 'invoiced/invoices/_edit'];

                $event->rules['invoiced/settings/invoice-templates/new'] = [ 'template' => 'invoiced/settings/invoice-templates/_edit'];
                $event->rules['invoiced/settings/invoice-templates/edit/<id:\d+>'] = [ 'template' => 'invoiced/settings/invoice-templates/_edit'];

                $event->rules['invoiced/invoice-template/save-template'] = 'invoiced/invoice-template/save';
                $event->rules['invoiced/invoice-template/save-template/<id:\d+>'] = 'invoiced/invoice-template/edit';
            }
        );
    }

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}