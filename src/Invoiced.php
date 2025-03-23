<?php
namespace nethaven\invoiced;


use nethaven\invoiced\base\PluginTrait;
use nethaven\invoiced\base\ProjectConfigHelper;
use nethaven\invoiced\elements\Invoice;
use nethaven\invoiced\models\Settings;
use nethaven\invoiced\services\InvoiceTemplates as TemplatesService;
use nethaven\invoiced\services\Invoices as InvoicesService;
use nethaven\invoiced\autocompletes\TemplateHtmlAutocomplete;
use nethaven\invoiced\autocompletes\TemplateCssAutocomplete;

use nystudio107\codeeditor\events\RegisterCodeEditorAutocompletesEvent;
use nystudio107\codeeditor\services\AutocompleteService;

use Craft;
use craft\base\Event as Event;
use craft\base\Plugin;
use craft\events\RebuildConfigEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Elements;
use craft\services\ProjectConfig;
use craft\web\UrlManager;
use yii\base\Event as EventAlias;



class Invoiced extends Plugin
{
    // Properties
    // =========================================================================

    public static Invoiced $plugin;
    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;
    public string $schemaVersion = '1.1.0';
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
            $this->_registerProjectConfigEventHandlers();

            if (Craft::$app->getRequest()->getIsCpRequest()) {
                $this->_registerCpRoutes();
                $this->_reigsterAutoCompletes();
            }
        });
        
        EventAlias::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Invoice::class;
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

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->controller->redirect(UrlHelper::cpUrl('invoiced/settings'));
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
                $event->rules['invoiced/invoices/new'] = [ 'template' => 'invoiced/invoices/_new'];
                $event->rules['invoiced/invoices/edit/<elementId:\d+>'] = [ 'template' => 'invoiced/invoices/_edit'];
                $event->rules['invoiced/invoices/save'] = 'invoiced/invoices/save';
                $event->rules['invoiced/invoices/edit'] = 'invoiced/invoices/edit';
                $event->rules['invoiced/invoices/preview'] = 'invoiced/invoices/preview';
                $event->rules['invoiced/invoices/validate'] = 'invoiced/invoices/validate';

                $event->rules['invoiced/settings/invoice-templates/new'] = [ 'template' => 'invoiced/settings/invoice-templates/_edit'];
                $event->rules['invoiced/settings/invoice-templates/edit/<id:\d+>'] = [ 'template' => 'invoiced/settings/invoice-templates/_edit'];

                $event->rules['invoiced/invoice-templates/save'] = 'invoiced/invoice-template/save';
            }
        );
    }

    private function _reigsterAutoCompletes() {
        Event::on(
            AutocompleteService::class,
            AutocompleteService::EVENT_REGISTER_CODEEDITOR_AUTOCOMPLETES,
            function (RegisterCodeEditorAutocompletesEvent $event) {   
                $event->types[] = TemplateHtmlAutocomplete::class;
                $event->types[] = TemplateCssAutocomplete::class;
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