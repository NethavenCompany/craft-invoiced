<?php

namespace nethaven\invoiced\resources;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class InvoiceTemplateAssets extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@nethaven/invoiced/resources/template';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'template.js'
        ];

        $this->css = [
            'template.css'
        ];

        parent::init();
    }
}
