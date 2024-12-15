<?php
namespace nethaven\invoiced\events;

use yii\base\Event;
use nethaven\invoiced\models\InvoiceTemplate;

class InvoiceTemplateEvent extends Event
{
    // Properties
    // =========================================================================

    public InvoiceTemplate|null $template = null;
    public bool $isNew = false;
    
}