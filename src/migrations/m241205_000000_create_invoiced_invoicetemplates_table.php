<?php

namespace nethaven\invoiced\migrations;

use Craft;
use craft\db\Migration;
use nethaven\invoiced\base\Table;

class m241205_000000_create_invoiced_invoicetemplates_table extends Migration
{
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
    }

    public function safeDown()
    {
        $this->dropTable(Table::INVOICE_TEMPLATES);
    }
}
