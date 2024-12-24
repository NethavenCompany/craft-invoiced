<?php
namespace nethaven\invoiced\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $pluginName = 'Invoiced';
    public string $defaultInvoiceTemplate = '';
    public string $phoneFieldDefault = '';
    public string $emailFieldDefault = '';
    public int $vatFieldDefault = 0;


    // Public Methods
    // =========================================================================

    public function __construct($config = [])
    {
        unset($config['enableGatsbyCompatibility']);
        parent::__construct($config);
    }

}
