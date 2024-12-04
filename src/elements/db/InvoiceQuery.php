<?php
namespace nethaven\invoiced\elements\db;

use nethaven\invoiced\models\InvoiceTemplate;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class InvoiceQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    public mixed $handle = null;
    public mixed $templateId = null;

    protected array $defaultOrderBy = ['elements.dateCreated' => SORT_DESC];


    // Public Methods
    // =========================================================================

    public function handle($value): static
    {
        $this->handle = $value;
        return $this;
    }

    public function template($value): static
    {
        if ($value instanceof InvoiceTemplate) {
            $this->templateId = $value->id;
        } else if ($value !== null) {
            $this->templateId = (new Query())
                ->select(['id'])
                ->from(['{{%invoiced_invoicetemplates}}'])
                ->where(Db::parseParam('handle', $value))
                ->column();
        } else {
            $this->templateId = null;
        }

        return $this;
    }

    public function templateId($value): static
    {
        $this->templateId = $value;
        return $this;
    }


    // Protected Methods
    // =========================================================================

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('invoiced_invoices');

        $this->query->select([
            'invoiced_invoices.id',
            'invoiced_invoices.handle',
            'invoiced_invoices.settings',
            'invoiced_invoices.templateId',
            'invoiced_invoices.uid',
        ]);

        if ($this->handle) {
            $this->subQuery->andWhere(Db::parseParam('invoiced_invoices.handle', $this->handle));
        }

        if ($this->templateId) {
            $this->subQuery->andWhere(Db::parseParam('invoiced_invoices.templateId', $this->templateId));
        }

        return parent::beforePrepare();
    }
}
