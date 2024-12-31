<?php

namespace nethaven\invoiced\elements\db;

use Craft;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * Invoice query
 */
class InvoiceQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    public mixed $templateId = null;
    public mixed $invoiceNumber = null;
    public mixed $invoiceDate = null;
    public mixed $expirationDate = null;

    protected array $defaultOrderBy = ['invoiced_invoices.invoiceNumber' => SORT_DESC];


    // Public Methods
    // =========================================================================

    public function templateId($value): static
    {
        $this->templateId = $value;
        return $this;
    }

    public function invoiceNumber($value): static
    {
        $this->invoiceNumber = $value;
        return $this;
    }

    public function invoiceDate($value): static
    {
        $this->invoiceNumber = $value;
        return $this;
    }

    public function expirationDate($value): static
    {
        $this->invoiceNumber = $value;
        return $this;
    }


    // Protected Methods
    // =========================================================================

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('invoiced_invoices');
        
        $this->query->select([
            'invoiced_invoices.id',
            'invoiced_invoices.uid',
            'invoiced_invoices.templateId',
            'invoiced_invoices.invoiceNumber',
            'invoiced_invoices.invoiceDate',
            'invoiced_invoices.expirationDate',

            'invoiced_invoices.items',
            
            'invoiced_invoices.subTotal',
            'invoiced_invoices.vat',
            'invoiced_invoices.vatAmount',
            'invoiced_invoices.total',
            'invoiced_invoices.phone',
            'invoiced_invoices.email',
            'invoiced_invoices.pdf',
        ]);

        if($this->invoiceDate) {
            $this->subQuery->andWhere(Db::parseParam('invoiced_invoices.invoiceDate', $this->invoiceDate));
        }

        if($this->expirationDate) {
            $this->subQuery->andWhere(Db::parseParam('invoiced_invoices.expirationDate', $this->expirationDate));
        }

        if ($this->templateId) {
            $this->subQuery->andWhere(Db::parseParam('invoiced_invoices.templateId', $this->templateId));
        }

        if ($this->invoiceNumber) {
            $this->subQuery->andWhere(Db::parseParam('invoiced_invoices.invoiceNumber', $this->invoiceNumber));
        }

        return parent::beforePrepare();
    }
}
