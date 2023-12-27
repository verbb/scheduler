<?php
namespace verbb\scheduler\fields\feedme;

use Craft;

use craft\feedme\base\Field;
use craft\feedme\base\FieldInterface;
use craft\feedme\helpers\DateHelper;

use Cake\Utility\Hash;
use verbb\scheduler\fields\ScheduleJob;

class ScheduleJobDataField extends Field implements FieldInterface
{
    // Properties
    // =========================================================================

    public static $class = ScheduleJob::class;


    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('scheduler', 'Schedule Job');
    }


    // Public Methods
    // =========================================================================

    public function getMappingTemplate(): string
    {
        return 'feed-me/_includes/fields/date';
    }

    public function parseField(): mixed
    {
        $value = $this->fetchValue();

        $formatting = Hash::get($this->fieldInfo, 'options.match');

        $dateValue = DateHelper::parseString($value, $formatting);

        if ($dateValue) {
            return $dateValue;
        }

        return $value;
    }
}