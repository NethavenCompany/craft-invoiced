<?php

namespace nethaven\invoiced\records;

use Craft;
use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;
use nethaven\invoiced\base\Table;

/**
 * Invoice record
 *
 * @property int $id ID
 * @property int $templateId Template ID
 * @property string $invoiceNumber Invoice number
 * @property string $invoiceDate Invoice date
 * @property string $expirationDate Expiration date
 * @property array $items Items
 * @property string $subtotal Subtotal
 * @property string $vat Vat
 * @property string $vatAmount VatAmount
 * @property string $total Total
 * @property string|null $phone Phone
 * @property string|null $email Email
 * @property string $pdf Pdf
 * @property string $uid Uid
 * @property string $dateCreated Date created
 * @property string $dateUpdated Date updated
 * @property string|null $dateDeleted Date deleted
 */
class Invoice extends ActiveRecord
{
    use SoftDeleteTrait;

    public static function tableName()
    {
        return Table::INVOICES;
    }
}
