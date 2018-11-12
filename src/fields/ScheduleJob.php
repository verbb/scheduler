<?php

/**
 * Scheduler plugin for Craft CMS 3.x
 *
 * Scheduler
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2018 Supercool
 */

namespace supercool\scheduler\fields;

use supercool\scheduler\Scheduler;
use supercool\scheduler\assetbundles\scheduler\SchedulerAsset;

use Craft;
use craft\base\ElementInterface;
use craft\fields\Date;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;
use craft\helpers\Template;

class ScheduleJob extends Date
{
    // Constants
    // =========================================================================
    /**
     * @event RegisterComponentTypesEvent The event that is triggered when registering field types.
     */
    const EVENT_REGISTER_SCHEDULER_JOB_TYPES = 'registerSchedulerJobTypes';

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('scheduler', 'Schedule Job');
    }

    // Properties
    // =========================================================================

    /**
     * @var bool Whether a datepicker should be shown as part of the input
     */
    public $showDate = true;

    /**
     * @var bool Whether a timepicker should be shown as part of the input
     */
    public $showTime = true;

    /**
     * @var job type
     */
    public $type;


    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        // If they are both selected or nothing is selected, the select showBoth.
        if ($this->showDate && $this->showTime) {
            $dateTimeValue = 'showBoth';
        } else if ($this->showDate) {
            $dateTimeValue = 'showDate';
        } else if ($this->showTime) {
            $dateTimeValue = 'showTime';
        }

        $options = [15, 30, 60];
        $options = array_combine($options, $options);

        $typeOptions = $this->_getTypeOptions();

        /** @noinspection PhpUndefinedVariableInspection */
        return Craft::$app->getView()->renderTemplate('scheduler/_components/fields/ScheduleJob/settings',
            [
                'value' => $dateTimeValue,
                'incrementOptions' => $options,
                'field' => $this,
                'typeOptions' => $typeOptions,
            ]);
    }


    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        $date = $element->{$this->handle};

        $jobType = $this->type;

        if (!is_null($date)) {
            Scheduler::$plugin->jobs->addJob($jobType, $date, 'field', [
                'elementId' => $element->id
            ]);
        }
    }


    // Private Methods
    // =========================================================================
    /**
     * Prep the Job type options
     *
     * @return array
     */
    private function _getTypeOptions()
    {
        $typeOptions = Scheduler::$plugin->jobs->getAvailableJobTypes();
        return $typeOptions;
    }

}
