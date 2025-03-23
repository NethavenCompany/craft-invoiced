<?php

namespace nethaven\invoiced\autocompletes;

use nystudio107\codeeditor\base\Autocomplete;
use nystudio107\codeeditor\models\CompleteItem;
use nystudio107\codeeditor\types\AutocompleteTypes;
use nystudio107\codeeditor\types\CompleteItemKind;

class TemplateCssAutocomplete extends Autocomplete
{
    public $name = 'TemplateCssAutocomplete';
    public $type = AutocompleteTypes::GeneralAutocomplete;
    public $hasSubProperties = false;

    public function generateCompleteItems(): void
    {
        CompleteItem::create()
            ->label('CssAutocomplete')
            ->insertText('CssAutocomplete')
            ->detail('This is my css autocomplete')
            ->documentation('This detailed documentation of my autocomplete')
            ->kind(CompleteItemKind::ConstantKind)
            ->add($this);
    }
}
