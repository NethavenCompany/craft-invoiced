<?php
namespace nethaven\invoiced\resources;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class InvoiceAssets extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@nethaven/invoiced/resources/invoice';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'preview.js',
        ];

        $this->css = [
            'preview.css',
        ];

        parent::init();
    }
}