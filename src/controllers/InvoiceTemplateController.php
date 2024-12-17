<?php

namespace nethaven\invoiced\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

use nethaven\invoiced\Invoiced;
use nethaven\invoiced\models\InvoiceTemplate as TemplateModel;

class InvoiceTemplateController extends Controller
{
    public function actionIndex(): Response
    {
        $invoiceTemplates = Invoiced::$plugin->getInvoiceTemplates()->getAllTemplates();

        return $this->renderTemplate('invoiced/settings/invoice-templates', compact('invoiceTemplates'));
    }

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        $template = new TemplateModel();
        $template->id = $request->getBodyParam('id');
        $template->name = $request->getBodyParam('name');
        $template->handle = $request->getBodyParam('handle');
        $template->html = preg_replace('/\/index(?:\.html|\.twig)?$/', '', $request->getBodyParam('templateHtml'));
        $template->css = $request->getBodyParam('templateCss');

        if(Invoiced::$plugin->getInvoiceTemplates()->saveTemplate($template)) {
            Craft::$app->getSession()->setSuccess('Template saved');
        }   else {
            Craft::$app->getSession()->setError('Could not save the template.');
        }
        
        return $this->redirect('invoiced/settings/invoice-templates');
    }

    public function actionDelete(): Response
    {
        $this->requireAcceptsJson();

        $templateId = $this->request->getRequiredParam('id');

        if (Invoiced::$plugin->getInvoiceTemplates()->deleteTemplateById($templateId)) {
            return $this->asJson(['success' => true]);
        }

        return $this->asJson(['error' => Craft::t('invoiced', 'Couldn’t archive template.')]);
    }
}