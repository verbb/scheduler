<?php
namespace verbb\scheduler\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public bool $enableReSaveElementOnElementSave = true;


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [['enableReSaveElementOnElementSave'], 'required'];

        return $rules;
    }
}
