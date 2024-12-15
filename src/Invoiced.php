<?php
namespace nethaven\invoiced;

use Craft;
use craft\base\Plugin;
use craft\base\Event as Event;
use craft\events\RegisterUrlRulesEvent;
use craft\services\ProjectConfig;
use craft\web\UrlManager;
use craft\events\RebuildConfigEvent;

use nethaven\invoiced\base\PluginTrait;
use nethaven\invoiced\base\ProjectConfigHelper;
use nethaven\invoiced\models\Settings;
use nethaven\invoiced\services\Invoices as InvoicesService;
use nethaven\invoiced\services\InvoiceTemplates as TemplatesService;


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
                'invoiceTemplates' => TemplatesService::class,
                'invoices' => InvoicesService::class,
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
            $this->_registerProjectConfigEventHandlers();
        });
        
    }


    // Public Methods
    // =========================================================================

    public function getInvoiceTemplates(): TemplatesService
    {
        return $this->get('invoiceTemplates');
    }

    public function getInvoices(): InvoicesService
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

    private function _registerCpRoutes(): void
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

                $event->rules['invoiced/invoice-templates/save'] = 'invoiced/invoice-template/save';
            }
        );
    }

    private function _registerProjectConfigEventHandlers(): void
    {
        $projectConfigService = Craft::$app->getProjectConfig();
        $invoiceTemplatesService = $this->getInvoiceTemplates();

        $projectConfigService
            ->onAdd(TemplatesService::CONFIG_TEMPLATES_KEY . '.{uid}', [$invoiceTemplatesService, 'handleChangedTemplate'])
            ->onUpdate(TemplatesService::CONFIG_TEMPLATES_KEY . '.{uid}', [$invoiceTemplatesService, 'handleChangedTemplate'])
            ->onRemove(TemplatesService::CONFIG_TEMPLATES_KEY . '.{uid}', [$invoiceTemplatesService, 'handleDeletedTemplate']);


        Event::on(ProjectConfig::class, ProjectConfig::EVENT_REBUILD, function(RebuildConfigEvent $event) {
            $event->config['invoiced'] = ProjectConfigHelper::rebuildProjectConfig();
        });
    }

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}