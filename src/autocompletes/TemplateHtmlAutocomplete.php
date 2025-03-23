<?php

namespace nethaven\invoiced\autocompletes;

use nystudio107\codeeditor\base\Autocomplete;
use nystudio107\codeeditor\models\CompleteItem;
use nystudio107\codeeditor\types\AutocompleteTypes;
use nystudio107\codeeditor\types\CompleteItemKind;

class TemplateHtmlAutocomplete extends Autocomplete
{
    public $name = 'TemplateHtmlAutocomplete';
    public $type = AutocompleteTypes::GeneralAutocomplete;
    public $hasSubProperties = false;

    public function generateCompleteItems(): void
    {
        CompleteItem::create()
            ->label('InvoiceItemsTable')
            ->insertText('<table class="invoice-items">
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
            {% for item in invoice.items %}
            <tr>
                <td>{{ item[0] }}</td>
                <td>{{ item[2] }}</td>
                <td>{{ item[1] }}</td>
                <td>{{ item[0] * item[1] }}</td>
            </tr>
            {% endfor %}
        </tbody>
    </table>')
            ->detail('A simple table showing the price, quantity and description of each invoice item')
            ->documentation('This detailed documentation of my autocomplete')
            ->kind(CompleteItemKind::ConstantKind)
            ->add($this);
    }
}
