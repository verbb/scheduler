<?php

namespace supercool\scheduler\fields\feedme;

use Cake\Utility\Hash;
use craft\feedme\base\FieldInterface;
use craft\feedme\helpers\DateHelper;

/**
 * Class used to define how feed-me handles our custom field.
 *
 * Class ScheduleJobDataField
 * @package supercool\scheduler\fields\feedme
 */
class ScheduleJobDataField implements FieldInterface {

    // Define the actual field class so feed me knows what to use this Data field for
    public static $class = 'supercool\scheduler\fields\ScheduleJob';

    /**
     * Defines the template feed me uses on the mapping template.
     * We use a date field as that's what this custom field is built on top of.
     *
     * @return string
     */
    public function getMappingTemplate()
    {
        return 'feed-me/_includes/fields/date';
    }


    /**
     * What feed me does with the data to get a DB ready value.
     * Again we use the default for the date field as that's what this custom field is built on.
     *
     * @return mixed
     */
    public function parseField()
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