<?php

namespace nethaven\invoiced\services;


use nethaven\invoiced\elements\Invoice;

use Craft;
use craft\base\Component;
use craft\base\MemoizableArray;


class Invoices extends Component
{   
    // Public Methods
    // =========================================================================

    public function getInvoices(): array
    {
        return Invoice::find()->all();
    }

    public function getInvoicesByTemplate($templateId): array
    {
        return Invoice::find()->where(['templateId' => $templateId])->all();
    }

    public function getInvoiceById($id): ?Invoice
    {
        return Invoice::find()->id($id)->one();
    }

    public function newInvoiceNumber()
    {
        $currentYear = date('Y');
        $highestInvoiceNumber = Invoice::find()->trashed(null)->where(['like', 'invoiceNumber', $currentYear . '%', false])->orderBy(['invoiceNumber' => SORT_DESC])->one();
        $newInvoiceNumber = $highestInvoiceNumber ? (int)substr($highestInvoiceNumber->invoiceNumber, 4) + 1 : 1;
        return $currentYear . sprintf('%04d', $newInvoiceNumber);
    }
}
