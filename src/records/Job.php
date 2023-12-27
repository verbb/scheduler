<?php
namespace verbb\scheduler\records;

use craft\db\ActiveRecord;

class Job extends ActiveRecord
{
    // Static Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%scheduler_jobs}}';
    }
}
