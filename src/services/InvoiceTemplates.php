<?php

namespace nethaven\invoiced\services;

use Craft;
use craft\base\Component;
use craft\base\MemoizableArray;
use craft\db\Query;
use craft\events\ConfigEvent;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use nethaven\invoiced\base\Table;
use nethaven\invoiced\events\InvoiceTemplateEvent;
use nethaven\invoiced\Invoiced;
use nethaven\invoiced\models\InvoiceTemplate as TemplateModel;
use nethaven\invoiced\records\InvoiceTemplate as TemplateRecord;
use Throwable;

class InvoiceTemplates extends Component
{
    // Constants
    // =========================================================================
    
    public const EVENT_BEFORE_SAVE_INVOICE_TEMPLATE = 'beforeSaveInvoiceTemplate';
    public const EVENT_AFTER_SAVE_INVOICE_TEMPLATE = 'afterSaveInvoiceTemplate';
    public const EVENT_BEFORE_DELETE_INVOICE_TEMPLATE = 'beforeDeleteInvoiceTemplate';
    public const EVENT_BEFORE_APPLY_INVOICE_TEMPLATE_DELETE = 'beforeApplyInvoiceTemplateDelete';
    public const EVENT_AFTER_DELETE_INVOICE_TEMPLATE = 'afterDeleteInvoiceTemplate';
    public const CONFIG_TEMPLATES_KEY = 'invoiced.invoiceTemplates';


    // Properties
    // =========================================================================
    
    private ?MemoizableArray $_templates = null;


    // Public Methods
    // =========================================================================

    public function getAllTemplates(): array
    {
        return $this->_templates()->all();
    }

    public function getTemplateById($id): ?TemplateModel
    {
        return $this->_templates()->firstWhere('id', $id);
    }

    public function getTemplateByHandle($handle): ?TemplateModel
    {
        return $this->_templates()->firstWhere('handle', $handle, true);
    }

    public function saveTemplate(TemplateModel $template, bool $runValidation = true): bool
    {
        $isNewTemplate = !(bool)$template->id;

        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_INVOICE_TEMPLATE)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_INVOICE_TEMPLATE, new InvoiceTemplateEvent([
                'template' => $template,
                'isNew' => $isNewTemplate,
            ]));
        }

        if ($runValidation && !$template->validate()) {
            Invoiced::log('Template not saved due to validation error.');
            return false;
        }

        if ($isNewTemplate) {
            $template->uid = StringHelper::UUID();

            $template->sortOrder = (new Query())
                ->from([Table::INVOICE_TEMPLATES])
                ->max('[[sortOrder]]') + 1;
        } else if (!$template->uid) {
            $template->uid = Db::uidById(Table::INVOICE_TEMPLATES, $template->id);
        }

        $existingTemplate = $this->getTemplateByHandle($template->handle);

        if ($existingTemplate && (!$template->id || $template->id != $existingTemplate->id)) {
            $template->addError('handle', Craft::t('invoiced', 'That handle is already in use'));
            return false;
        }

        $configPath = self::CONFIG_TEMPLATES_KEY . '.' . $template->uid;
        Craft::$app->getProjectConfig()->set($configPath, $template->getConfig(), "Save the “{$template->handle}” form template");

        if ($isNewTemplate) {
            $template->id = Db::idByUid(Table::INVOICE_TEMPLATES, $template->uid);
        }

        return true;
    }

    public function handleChangedTemplate(ConfigEvent $event): void
    {
        $templateUid = $event->tokenMatches[0];
        $data = $event->newValue;

        if (!$data) {
            return;
        }

        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            $templateRecord = $this->_getTemplateRecord($templateUid, true);
            $isNewTemplate = $templateRecord->getIsNewRecord();

            $templateRecord->name = $data['name'];
            $templateRecord->handle = $data['handle'];
            $templateRecord->html = $data['html'];
            $templateRecord->css = $data['css'];
            $templateRecord->sortOrder = $data['sortOrder'];
            $templateRecord->uid = $templateUid;

            // if (!empty($data['fieldLayouts'])) {
            //     // Save the field layout
            //     $layout = FieldLayout::createFromConfig(reset($data['fieldLayouts']));
            //     $layout->id = $templateRecord->fieldLayoutId;
            //     $layout->type = Invoice::class;
            //     $layout->uid = key($data['fieldLayouts']);
                
            //     Craft::$app->getFields()->saveLayout($layout, false);
                
            //     $templateRecord->fieldLayoutId = $layout->id;
            // } else if ($templateRecord->fieldLayoutId) {
            //     // Delete the main field layout
            //     Craft::$app->getFields()->deleteLayoutById($templateRecord->fieldLayoutId);
            //     $templateRecord->fieldLayoutId = null;
            // }

            if ($wasTrashed = (bool)$templateRecord->dateDeleted) {
                $templateRecord->restore();
            } else {
                $templateRecord->save(false);
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        // Clear caches
        $this->_templates = null;

        // Fire an 'afterSaveFormTemplate' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_INVOICE_TEMPLATE)) {
            $this->trigger(self::EVENT_AFTER_SAVE_INVOICE_TEMPLATE, new InvoiceTemplateEvent([
                'template' => $this->getTemplateById($templateRecord->id),
                'isNew' => $isNewTemplate,
            ]));
        }
    }



    // Private Methods
    // =========================================================================

    private function _templates() {
        if (!isset($this->_templates)) {
            $templates = [];

            foreach ($this->_createTemplatesQuery()->all() as $result) {
                $templates[] = new TemplateModel($result);
            }

            $this->_templates = new MemoizableArray($templates);
        }

        return $this->_templates;
    }

    private function _createTemplatesQuery(): Query
    {
        $query = (new Query())
            ->select([
                'id',
                'name',
                'handle',
                'html',
                'css',
                'sortOrder',
                'dateDeleted',
                'uid',
            ])
            ->from([Table::INVOICE_TEMPLATES])
            ->where(['dateDeleted' => null])
            ->orderBy(['sortOrder' => SORT_ASC]);

        return $query;
    }

    private function _getTemplateRecord(string $uid, bool $withTrashed = false): TemplateRecord
    {
        $query = $withTrashed ? TemplateRecord::findWithTrashed() : TemplateRecord::find();
        $query->andWhere(['uid' => $uid]);

        return $query->one() ?? new TemplateRecord();
    }
}
