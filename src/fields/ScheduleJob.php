<?php
namespace verbb\scheduler\fields;

use verbb\scheduler\Scheduler;

use Craft;
use craft\base\ElementInterface;
use craft\fields\Date;

class ScheduleJob extends Date
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('scheduler', 'Schedule Job');
    }

    public static function icon(): string
    {
        return '@verbb/scheduler/icon-mask.svg';
    }


    // Properties
    // =========================================================================

    public bool $showDate = true;
    public bool $showTime = true;
    public ?string $type = null;


    // Public Methods
    // =========================================================================

    public function getSettingsHtml(): ?string
    {
        $dateTimeValue = null;

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

        return Craft::$app->getView()->renderTemplate('scheduler/field/settings', [
            'value' => $dateTimeValue,
            'incrementOptions' => $options,
            'field' => $this,
            'typeOptions' => $typeOptions,
        ]);
    }

    public function afterElementSave(ElementInterface $element, bool $isNew): void
    {
        $date = $element->{$this->handle};

        $jobType = $this->type;

        if (!is_null($date)) {
            Scheduler::$plugin->getJobs()->addJob($jobType, $date, 'field', [
                'elementId' => $element->id
            ]);
        }
    }


    // Private Methods
    // =========================================================================

    private function _getTypeOptions(): array
    {
        return Scheduler::$plugin->getJobs()->getAvailableJobTypes();
    }

}
