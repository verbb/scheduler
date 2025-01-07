<?php
namespace verbb\scheduler\models;

use verbb\scheduler\jobs\BaseSchedulerJob;

use Craft;
use craft\base\Model;
use craft\helpers\Component as ComponentHelper;

use DateTime;

class Job extends Model
{
    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?string $type = null;
    public ?DateTime $date = null;
    public ?string $context = null;
    public null|string|array $settings = null;

    private mixed $_jobType = null;


    // Public Methods
    // =========================================================================

    public function __toString(): string
    {
        return Craft::t('scheduler', $this->id);
    }

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [['id'], 'number', 'integerOnly' => true];
        $rules[] = [['type', 'date'], 'required'];
        $rules[] = [['type', 'context'], 'string', 'max' => 255];

        return $rules;
    }

    public function getJobType(): ?BaseSchedulerJob
    {
        if (!isset($this->_jobType)) {
            $component = ComponentHelper::createComponent($this->type);

            if ($component) {
                $component->model = $this;
            }

            $this->_jobType = $component;

            // Might not actually exist
            if (!$this->_jobType) {
                $this->_jobType = false;
            }
        }

        // Return 'null' instead of 'false' if it doesn't exist
        if ($this->_jobType) {
            return $this->_jobType;
        }

        return null;
    }

}
