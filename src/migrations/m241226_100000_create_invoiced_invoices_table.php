<?php

namespace nethaven\invoiced\migrations;

use Craft;
use craft\db\Migration;
use nethaven\invoiced\base\Table;

class m241226_100000_create_invoiced_invoices_table extends Migration
{
    public function safeUp()
    {
        $this->createTable(Table::INVOICES, [
            'id' => $this->primaryKey(),
            'templateId' => $this->integer()->notNull(),
            'invoiceNumber' => $this->string()->notNull(),
            'invoiceDate' => $this->date()->notNull(),
            'expirationDate' => $this->date()->notNull(),

            'items' => $this->text(),
            'subtotal' => $this->decimal(10, 2),
            'vat' => $this->decimal(10, 2),
            'total' => $this->decimal(10, 2),
            'phone' => $this->string(),
            'email' => $this->string(),
            'pdf' => $this->string(),

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
            null
        );

        $this->addForeignKey(
            null,
            Table::INVOICES,
            'id',
            '{{%elements}}',
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
