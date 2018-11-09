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

use Craft;
use craft\base\Model;
use craft\helpers\ArrayHelper;
use craft\behaviors\FieldLayoutBehavior;
use craft\validators\HandleValidator;
use craft\validators\UniqueValidator;
use craft\helpers\Component as ComponentHelper;

use supercool\scheduler\Scheduler;
use supercool\scheduler\jobs\ISchedulerJob;

class Job extends Model
{

  // Properties
  // =========================================================================

  /**
   * @var int|null ID
   */
  public $id;

  /**
   * @var string|null Type
   */
  public $type;

  /**
   * @var \DateTime|null Date
   */
  public $date;

  /**
   * @var string|null Context
   */
  public $context;

  /**
   * @var string|null Settings
   */
  public $settings;

  /**
   * @var
   */
  private $_jobType;

  // Public Methods
  // =========================================================================

  /**
   * @inheritdoc
   */
  public function datetimeAttributes(): array
  {
    $attributes = parent::datetimeAttributes();
    $attributes[] = 'date';
    return $attributes;
  }

  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['id'], 'number', 'integerOnly' => true],
      [['type', 'date'], 'required'],
      [['type', 'context'], 'string', 'max' => 255],
    ];
  }


  /**
   * Use the translated category group's name as the string representation.
   *
   * @return string
   */
  public function __toString(): string
  {
    return Craft::t('scheduler', $this->id);
  }


  /**
   * Returns the Job type this Job is using.
   *
   * @return BaseSchedulerJob|null
   */
  public function getJobType()
  {
    if ( !isset($this->_jobType) )
    {
      $component = ComponentHelper::createComponent($this->type);

      if ($component)
      {
        $component->model = $this;
      }

      $this->_jobType = $component;

      // Might not actually exist
      if (!$this->_jobType)
      {
        $this->_jobType = false;
      }
    }

    // Return 'null' instead of 'false' if it doesn't exist
    if ($this->_jobType)
    {
      return $this->_jobType;
    }
  }

}
