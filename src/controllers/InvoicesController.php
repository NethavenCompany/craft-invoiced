<?php

namespace nethaven\invoiced\controllers;

use Craft;
use craft\web\Controller;
use nethaven\invoiced\elements\Invoice;
use nethaven\invoiced\Invoiced;
use yii\web\Response;

/**
 * Invoices controller
 */
class InvoicesController extends Controller
{
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;


    public function actionIndex(): Response
    {
        return $this->renderTemplate('invoiced/invoices/index', []);
    }

    public function actionEdit(): Response
    {
        return $this->redirect('invoiced/invoices');
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $invoice = $this->_buildInvoice();
        
        if (Craft::$app->getElements()->saveElement($invoice)) {
            Craft::$app->getSession()->setSuccess('Invoice saved');
        } else {
            Craft::$app->getSession()->setError('Could not save invoice.');
        }

        return $this->redirect('invoiced/invoices');
    }

    public function actionPreview(): Response
    {
        $this->requireAcceptsJson();

        $invoice = $this->_buildInvoice();
        $template = Invoiced::$plugin->getInvoiceTemplates()->getTemplateById($invoice->templateId);

        return $this->asJson([
            'html' => $invoice->getPdfHtml(false),
            'css' => $template->css
        ]);
    }

    public function actionValidate(): Response
    {
        $this->requireAcceptsJson();

        $numberTaken = Invoiced::$plugin->getInvoices()->getInvoiceByNumber($this->request->getParam("invoiceNumber"));

        if($numberTaken) {
            return $this->asJson(true);
        }

        return $this->asJson(false);
    }

    private function _buildInvoice(): Invoice
    {
        $invoice = new Invoice();
        $invoice->templateId = $this->request->getRequiredParam("templateId");

        $invoice->invoiceNumber = $this->request->getParam("invoiceNumber");
        $invoice->invoiceDate = $this->request->getParam("invoiceDate")["date"] ?? $this->request->getParam("invoiceDate");
        $invoice->expirationDate = $this->request->getParam("expirationDate")["date"] ?? $this->request->getParam("expirationDate");

        $itemsParam = $this->request->getParam("items");

        if (is_array($itemsParam)) {
            $invoice->items = $itemsParam;
        } else if (is_string($itemsParam)) {
            $invoice->items = json_decode($itemsParam);
        } else {
            $invoice->items = [];
        }

        if($invoice->items) {
            foreach ($invoice->items as $item) {
                $qty = json_decode($item[0]) ?? 0;
                $unitPrice = json_decode($item[1]) ?? 0;
                $invoice->subTotal += ($qty * $unitPrice);
            }
        }
        
        $invoice->vat = $this->request->getParam("vat");
        $invoice->vatAmount = round($invoice->subTotal * ($invoice->vat / 100), 2);
        $invoice->total = round($invoice->subTotal + $invoice->vatAmount, 2);

        $invoice->phone = $this->request->getParam("phone");
        $invoice->email = $this->request->getParam("email");

        return $invoice;
    }
}
