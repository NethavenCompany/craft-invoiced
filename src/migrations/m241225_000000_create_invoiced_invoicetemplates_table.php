<?php

namespace nethaven\invoiced\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\StringHelper;
use nethaven\invoiced\base\Table;
use nethaven\invoiced\Invoiced;
use nethaven\invoiced\models\InvoiceTemplate;


class m241225_000000_create_invoiced_invoicetemplates_table extends Migration
{
    public $exampleTemplateHtml = '
    <html>
    <body>
        <ul class="details">
            <li class="heading">Address</li>
            <li>Streetname 11</li>
            <li>1322VK Cityname</li>
            <li class="heading">CoC Number</li>
            <li>90653516</li>
            <li class="heading">VAT Number</li>
            <li>UK004839864B22</li>
            <li class="heading">Bank Details</li>
            <li>Bankname</li>
            <li>UK24 BANK 0381 4086 15</li>
            <li>BANKUK2U</li>
            <li class="heading">Contact</li>
            <li>
                <a href="mailto:{{ invoice.email }}">{{ invoice.email }}</a>
            </li>
            <li>
                <a href="tel:{{ invoice.phone }}">{{ invoice.phone }}</a>
            </li>
        </ul>

        <div class="title">
            <h1>Invoice</h1>
            <p>Thank you for your purchase!</p>
        </div>

        <span class="invoice-details">
            <div>
                <p class="heading">Invoice Date</p>
                <p>{{ invoice.invoiceDate }}</p>
            </div>
            <div class="expirationDate">
                <p class="heading">Expiration Date</p>
                <p>{{ invoice.expirationDate }}</p>
            </div>
            <div class="invoiceNumber">
                <p class="heading">Invoice Number</p>
                <p>{{ invoice.invoiceNumber }}</p>
            </div>
        </span>

        <table width="100%" class="invoice-items">
            <thead>
                <tr>
                    <th>QTY</th>
                    <th>Description</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tr>
                <td colspan="4"><hr></td>
            </tr>
            <tbody>
                {% for item in items %}
                <tr>
                    <td>{{ item[0] }}</td>
                    <td>{{ item[2] }}</td>
                    <td>{{ item[1] }}</td>
                    <td>{{ item[0] * item[1] }}</td>
                </tr>
                {% endfor %}
            </tbody>
        </table>

        <footer>
            <p>We kindly ask you to transfer the due amount within 30 days
    stating the invoice number</p>
        </footer>
    </body>
    </html>';

    public $exmapleTemplateCss = '* {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
    }
    
    body, html {
        font-family: Figtree, sans-serif;
        padding: 5rem;
    }
    
    .invoice-details {
        margin-top: 8rem;
        display: block;
        position: relative;
        width: 100%;
        height: 3rem;
    }
    
    .invoice-details div {
        position: absolute;
        width: 10rem;
        top: 0;
        left: 0;
    }
    
    .expirationDate {
        left: 10rem !important;
    }
    
    .invoiceNumber {
        left: 21rem !important;
    }
    
    a {
        color: #5E12FF;
    }
    
    ul {
        list-style: none;
    }
    
    li, p {
        font-size: 16px;
    }
    
    h1 {
        font-size: 60px;
    }
    
    .heading {
        font-weight: bold;
    }
    
    .details {
        padding: 1rem 4rem 1rem 1rem;
        position: absolute;
        right: 0;
        top: 3rem;
        border-top: 1px solid black;
        border-bottom: 1px solid black;
    }
    
    .details .heading {
        margin-top: 0.5rem;
    }
    
    .details .heading:first-of-type {
        margin: 0;
    }
    
    .title {
        margin-top: 8rem;
    }
    
    .invoice-items {
        margin-top: 2rem;
    }
    
    
    table hr {
        margin: 0.5rem 0;
    }
    
    table td:first-of-type, table th:first-of-type {
        width: auto;
        padding-right: 3rem;
    }
    
    table td, table th {
        text-align: left;
        width: 100%;
    }
    
    table td {
        padding: 0.4rem 0;
    }
    
    footer {
        bottom: 3rem;
        text-align: center;
        position: absolute;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    
    footer p {
        width: 25rem;
    }';


    public function safeUp()
    {
        $this->createTable(Table::INVOICE_TEMPLATES, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'handle' => $this->string()->notNull(),
            'html' => $this->text(),
            'css' => $this->text(),
            'sortOrder' => $this->integer(),
            'uid' => $this->uid(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'dateDeleted' => $this->dateTime(),
        ]);

        $this->createIndex(null, Table::INVOICE_TEMPLATES, 'handle', true);
        $this->createIndex(null, Table::INVOICE_TEMPLATES, 'uid', true);

        $defaultTemplate = new InvoiceTemplate(); 
        $defaultTemplate->name = 'Example Template';
        $defaultTemplate->handle = 'exampleTemplate';
        $defaultTemplate->html = $this->exampleTemplateHtml;
        $defaultTemplate->css = $this->exmapleTemplateCss;
        $defaultTemplate->sortOrder = 1;

        Invoiced::$plugin->getInvoiceTemplates()->saveTemplate($defaultTemplate);
    }

    public function safeDown()
    {
        $this->dropTable(Table::INVOICE_TEMPLATES);
    }
}
