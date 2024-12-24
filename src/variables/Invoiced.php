<?php
namespace nethaven\invoiced\variables;

use Craft;

use nethaven\invoiced\invoiced as invoicedPlugin;
use nethaven\invoiced\elements\Invoice;
use nethaven\invoiced\elements\db\InvoiceQuery;
use nethaven\invoiced\models\Settings;
use nethaven\invoiced\models\InvoiceTemplate as InvoiceTemplateModel;


class Invoiced
{
    /**
     * @return array
     */
    public function getInvoiceTemplates(): array
    {
        return InvoicedPlugin::$plugin->getInvoiceTemplates()->getAllTemplates();
    }

    public function getInvoiceTemplateById(int $id): InvoiceTemplateModel
    {
        return InvoicedPlugin::$plugin->getInvoiceTemplates()->getTemplateById($id);
    }

    /**
     * Returns plugin class.
     * @return InvoicedPlugin
     */
    public function getPlugin(): InvoicedPlugin
    {
        return InvoicedPlugin::$plugin;
    }

    /**
     * Returns current plugin name.
     * @return string
     */
    public function getPluginName(): string
    {
        return InvoicedPlugin::$plugin->getSettings()->pluginName;
    }

    /**
     * Returns current plugin settings.
     * @return string
     */
    public function getPluginSettings(): Settings
    {
        return InvoicedPlugin::$plugin->getSettings();
    }

    /**
     * Returns a invoice number suggestion based on current year.
     * @return string
     */
    public function newInvoiceNumber(): string
    {
        return invoicedPlugin::$plugin->getInvoices()->newInvoiceNumber();
    }

    /**
     * @return array
     */
    public function getSettingsNavItems(): array
    {
        $navItems = [
            'general' => ['title' => 'General'],

            'appearance-heading' => ['heading' => 'Appearance'],
            'invoice-templates' => ['title' => 'Invoice Templates'],
        ];

        return $navItems;
    }
}
