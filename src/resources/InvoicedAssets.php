<?php
namespace nethaven\invoiced\resources;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class InvoicedAssets extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@nethaven/invoiced/resources/invoiced';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [

        ];

        $this->css = [
            'style.css',
        ];

        parent::init();
    }
}