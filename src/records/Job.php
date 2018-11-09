<?php

/**
 * Scheduler plugin for Craft CMS 3.x
 *
 * Scheduler
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2018 Supercool
 */

namespace supercool\scheduler\records;

use craft\db\ActiveRecord;
use yii\db\ActiveQueryInterface;
use craft\records\Element;

class Job extends ActiveRecord
{

  /**
   * @inheritdoc
   *
   * @return string
   */
  public static function tableName(): string
  {
    return '{{%scheduler_jobs}}';
  }

}
