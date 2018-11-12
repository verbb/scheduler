<?php

/**
 * Scheduler plugin for Craft CMS 3.x
 *
 * Scheduler
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2018 Supercool
 */

namespace supercool\scheduler\models;

use craft\base\Model;

class Settings extends Model
{
    public $enableReSaveElementOnElementSave = true;

    public function rules()
    {
        return [
            [['enableReSaveElementOnElementSave'], 'required'],
        ];
    }
}
