<?php
namespace nethaven\invoiced\models;

use craft\helpers\UrlHelper;

use nethaven\invoiced\records\InvoiceTemplate as InvoiceTemplateRecord;

class InvoiceTemplate extends BaseTemplate
{
    // Properties
    // =========================================================================

    public bool $hasSingleTemplate = true;


    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = ['template', 'required'];

        return $rules;
    }

    /**
     * Returns the CP URL for editing the template.
     *
     * @return string
     */
    public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl('invoiced/settings/invoice-templates/edit/' . $this->id);
    }

    /**
     * Returns the template’s config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'template' => $this->template,
            'sortOrder' => $this->sortOrder,
        ];
    }

    protected function getRecordClass(): string
    {
        return InvoiceTemplateRecord::class;
    }
}
