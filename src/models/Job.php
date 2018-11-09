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

use supercool\scheduler\Scheduler;

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

  // Public Methods
  // =========================================================================


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

}
