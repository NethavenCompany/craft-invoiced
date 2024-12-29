<?php

namespace nethaven\invoiced\controllers;

use Craft;
use craft\web\Controller;
use DateTime;
use nethaven\invoiced\elements\Invoice;
use nethaven\invoiced\Invoiced;
use yii\web\Response;

/**
 * Invoices controller
 */
class InvoicesController extends Controller
{
    // Properties
    // =========================================================================

    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;


    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        return $this->renderTemplate('invoiced/invoices/index', []);
    }

    public function actionEdit(): Response
    {
        $this->requirePostRequest();

        $id = $this->request->getRequiredParam("elementId");

        $invoice = Invoiced::$plugin->getInvoices()->getInvoiceById($id);
        $invoice = $this->_buildInvoice($invoice);

        if (Craft::$app->getElements()->saveElement($invoice)) {
            Craft::$app->getSession()->setSuccess('Invoice saved');
        } else {
            Craft::$app->getSession()->setError('Could not save invoice.');
        }
        
        return $this->redirect('invoiced/invoices');
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $invoice = $this->_buildInvoice();

        if (!$invoice->validate()) {
            Craft::$app->session->setError('Failed to save invoice. Missing required fields.');
            return null;
        }
        
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
        
        $errors = [];
        $invoiceNumber = $this->request->getParam("invoiceNumber");
        $invoiceDate = $this->request->getParam("invoiceDate");
        $expirationDate = $this->request->getParam("expirationDate");
    
        // Check if the invoice number already exists
        $numberTaken = Invoiced::$plugin->getInvoices()->getInvoiceByNumber($invoiceNumber);
        
        if ($numberTaken) {
            $errors[] = [
                'id' => 'invoiceNumber',
                'message' => Craft::t('invoiced', 'Invoice number is already taken.'),
                'status' => 'error'
            ];
        }
    
        // Validate expiration date is greater than invoice date
        if ($invoiceDate && $expirationDate) {
            $invoiceDateTime = new DateTime($invoiceDate);
            $expirationDateTime = new DateTime($expirationDate);
    
            if ($expirationDateTime <= $invoiceDateTime) {
                $errors[] = [
                    'id' => 'expirationDate-date',
                    'message' => Craft::t('invoiced', 'Expiration date must be after the invoice date.'),
                ];
            }

            if ($invoiceDateTime >= $expirationDateTime) {
                $errors[] = [
                    'id' => 'invoiceDate-date',
                    'message' => Craft::t('invoiced', 'Invoice date must be before the expiration date.'),
                ];
            }
        }
    
        // Return errors if any exist
        if (!empty($errors)) {
            return $this->asJson([
                'success' => false,
                'errors' => $errors
            ]);
        }
    
        // If no errors, return success
        return $this->asJson([
            'success' => true,
            'message' => Craft::t('invoiced', 'Validation passed.')
        ]);
    }


    // Private Methods
    // =========================================================================

    private function _buildInvoice($invoice = null): Invoice
    {
        if(!$invoice) $invoice = new Invoice();

        $invoice->templateId = $this->request->getRequiredParam("templateId");
        $invoice->invoiceNumber = $this->request->getParam("invoiceNumber");
        $invoice->invoiceDate = $this->request->getParam("invoiceDate")["date"] ?? $this->request->getParam("invoiceDate");
        $invoice->expirationDate = $this->request->getParam("expirationDate")["date"] ?? $this->request->getParam("expirationDate");
        
        $itemsParam = $this->request->getParam("items");
        
        if (is_array($itemsParam)) {
            $invoice->items = $itemsParam;
        } else if (is_string($itemsParam)) {
            $invoice->items = json_decode($itemsParam);
        }
        
        if($invoice->items) {
            // set subTotal to zero incase this is not a new invoice.
            $invoice->subTotal = 0.00;
            $cleanItemArray = [];

            foreach ($invoice->items as $row => $value) {
                $qty = (float) json_decode($value[0]) ?? 0;
                $unitPrice = (float) json_decode($value[1]) ?? 0.00;
                $description = $value[2];

                if($qty <= 0 || $qty === '') continue;
                if($unitPrice === '') continue;
                
                $invoice->subTotal += ($qty * $unitPrice);

                $cleanItemArray[$row] = [$qty, $unitPrice, $description];
            }

            $invoice->items = $cleanItemArray;
        }
        
        $invoice->vat = $this->request->getParam("vat");
        $invoice->vatAmount = round($invoice->subTotal * ($invoice->vat / 100), 2);
        $invoice->total = round($invoice->subTotal + $invoice->vatAmount, 2);

        $invoice->phone = $this->request->getParam("phone");
        $invoice->email = $this->request->getParam("email");

        return $invoice;
    }
}
