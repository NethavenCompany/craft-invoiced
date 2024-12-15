<?php

namespace nethaven\invoiced\records;

use Craft;
use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;

use nethaven\invoiced\base\Table;

/**
 * Invoice Template record
 *
 * @property int $id ID
 * @property string $name Name
 * @property string $handle Handle
 * @property string|null $html Html
 * @property string|null $css Css
 * @property int|null $sortOrder Sort order
 * @property string $uid Uid
 * @property string $dateCreated Date created
 * @property string $dateUpdated Date updated
 * @property string|null $dateDeleted Date deleted
 */
class InvoiceTemplate extends ActiveRecord
{
    use SoftDeleteTrait;

    public static function tableName()
    {
        return Table::INVOICE_TEMPLATES;
    }
}
