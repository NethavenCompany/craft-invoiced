<?php

namespace nethaven\invoiced\base;


use Craft;
use nethaven\invoiced\Invoiced;


class ProjectConfigHelper
{
    // Static Methods
    // =========================================================================

    public static function rebuildProjectConfig(): array
    {
        $configData = [];

        $configData['invoiceTemplates'] = self::_getInvoiceTemplatesData();

        return array_filter($configData);
    }

    
    // Private Methods
    // =========================================================================

    private static function _getInvoiceTemplatesData(): array
    {
        $data = [];

        foreach (Invoiced::$plugin->getInvoiceTemplates()->getAllTemplates() as $template) {
            $data[$template->uid] = $template->getConfig();
        }

        return $data;
    }
}