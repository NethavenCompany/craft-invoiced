<?php
namespace nethaven\invoiced\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $pluginName = 'Invoiced';


    // Invoices
    // =========================================================================

    public string $defaultInvoiceTemplate = '';


    // Public Methods
    // =========================================================================

    public function __construct($config = [])
    {
        // Remove deprecated settings
        unset($config['enableGatsbyCompatibility']);

        parent::__construct($config);
    }

}
