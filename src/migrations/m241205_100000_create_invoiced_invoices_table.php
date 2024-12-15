<?php

namespace nethaven\invoiced\migrations;

use Craft;
use craft\db\Migration;
use nethaven\invoiced\base\Table;

class m241205_100000_create_invoiced_invoices_table extends Migration
{
    public function safeUp()
    {
        $this->createTable(Table::INVOICES, [
            'id' => $this->primaryKey(),
            'templateId' => $this->integer()->notNull(),
            'invoiceNumber' => $this->string()->notNull(),
            'invoiceDate' => $this->date()->notNull(),
            'expirationDate' => $this->date()->notNull(),
            'qty' => $this->integer()->notNull(),
            'description' => $this->string()->notNull(),
            'unitPrice' => $this->decimal(10, 2)->notNull(),
            'subtotal' => $this->decimal(10, 2)->notNull(),
            'vat' => $this->decimal(10, 2)->notNull(),
            'total' => $this->decimal(10, 2)->notNull(),
            'phone' => $this->string(),
            'email' => $this->string(),
            'address' => $this->text(),
            'cocnumber' => $this->string(),
            'vatnumber' => $this->string(),
            'uid' => $this->uid(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'dateDeleted' => $this->dateTime(),
        ]);

        $this->createIndex(null, Table::INVOICES, 'invoiceNumber', true);
        $this->createIndex(null, Table::INVOICES, 'uid', true);

        // Add foreign key for templateId
        $this->addForeignKey(
            null,
            Table::INVOICES,
            'templateId',
            '{{%invoiced_invoicetemplates}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropTable(Table::INVOICES);
    }
}
