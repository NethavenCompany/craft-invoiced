<?php
namespace nethaven\invoiced\variables;

use Craft;

use nethaven\invoiced\invoiced as invoicedPlugin;
use nethaven\invoiced\elements\Invoice;
use nethaven\invoiced\elements\db\InvoiceQuery;
use nethaven\invoiced\models\Settings;


class Invoiced
{
    /**
     * @return array
     */
    public function getInvoiceTemplates(): array
    {
        return InvoicedPlugin::$plugin->getInvoiceTemplates()->getAllTemplates();
    }

    // /**
    //  * @param null $criteria
    //  * @return InvoiceQuery
    //  */
    // public function invoices($criteria = null): InvoiceQuery
    // {
    //     $query = Invoice::find();

    //     if ($criteria) {
    //         Craft::configure($query, $criteria);
    //     }

    //     /* @var FormQuery $query */
    //     return $query;
    // }

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
     * @return array
     */
    public function getSettingsNavItems(): array
    {
        $navItems = [
            'invoices' => ['title' => 'Invoices'],

            'appearance-heading' => ['heading' => 'Appearance'],
            'invoice-templates' => ['title' => 'Invoice Templates'],
        ];

        return $navItems;
    }
}
