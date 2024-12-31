<?php

namespace nethaven\invoiced\jobs;

use Craft;
use craft\queue\BaseJob;
use nethaven\invoiced\Invoiced;

class ReapplyTemplate extends BaseJob
{
    public $templateId;

    public function execute($queue): void
    {
        $invoicesWithTemplate = Invoiced::$plugin->getInvoices()->getInvoicesByTemplate($this->templateId);
        $totalInvoices = count($invoicesWithTemplate);

        foreach($invoicesWithTemplate as $i => $invoice) {
            $this->setProgress(
                $queue,
                $i / $totalInvoices,
                Craft::t('app', '{step, number} of {total, number}', [
                    'step' => $i + 1,
                    'total' => $totalInvoices,
                    ])
            );

            try {
                $invoice->reapplyTemplate();
            } catch (\Throwable $e) {
                Craft::warning("Something went wrong: {$e->getMessage()}", __METHOD__);
            }
        }
    }

    protected function defaultDescription(): ?string
    {
        return Craft::t('app', 'Re-applying edited template to related invoices');
    }
}