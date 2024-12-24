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

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        $invoice = new Invoice();
        $invoice->templateId = $request->getParam("templateId");

        if (!Invoiced::$plugin->getInvoiceTemplates()->getTemplateById($invoice->templateId))
        {
            Craft::$app->getSession()->setError('Invalid template ID.');
            return $this->goBack();
        }

        $invoice->invoiceNumber = $request->getParam("invoiceNumber");
        $invoice->invoiceDate = $request->getParam("invoiceDate")["date"];
        $invoice->expirationDate = $request->getParam("expirationDate")["date"];

        if (is_array($request->getParam("items"))) {
            $invoice->items = $request->getParam("items");
        } else {
            $invoice->items = [];
        }

        foreach ($invoice->items as $item) {
            $qty = json_decode($item[0]);
            $unitPrice = json_decode($item[1]);
            $invoice->subTotal += ($qty * $unitPrice);
        }
        
        $invoice->vat = $request->getParam("vat");
        $invoice->total = $invoice->subTotal * (1 + ($invoice->vat / 100));

        $invoice->phone = $request->getParam("phone");
        $invoice->email = $request->getParam("email");
        
        if (Craft::$app->getElements()->saveElement($invoice)) {
            Craft::$app->getSession()->setSuccess('Invoice saved');
        } else {
            Craft::$app->getSession()->setError('Could not save invoice.');
        }

        return $this->redirect('invoiced/invoices');
    }

    public function actionPreview()
    {
        $this->requireAcceptsJson();

        $invoice = new Invoice();
        $invoice->templateId = $this->request->getRequiredParam("templateId");

        $invoice->invoiceNumber = $this->request->getParam("invoiceNumber");
        // $invoice->invoiceDate = $this->request->getParam("invoiceDate")["date"];
        // $invoice->expirationDate = $this->request->getParam("expirationDate")["date"];

        // if (is_array($this->request->getParam("items"))) {
        //     $invoice->items = $this->request->getParam("items");
        // } else {
        //     $invoice->items = [];
        // }

        // foreach ($invoice->items as $item) {
        //     $qty = json_decode($item[0]);
        //     $unitPrice = json_decode($item[1]);
        //     $invoice->subTotal += ($qty * $unitPrice);
        // }
        
        $invoice->vat = $this->request->getParam("vat");
        $invoice->total = $invoice->subTotal * (1 + ($invoice->vat / 100));

        $invoice->phone = $this->request->getParam("phone");
        $invoice->email = $this->request->getParam("email");

        return $this->asJson([
            'html' => $invoice->getPdfHtml(),
        ]);
    }
}
