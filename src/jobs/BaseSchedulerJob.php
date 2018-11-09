<?php

namespace supercool\scheduler\jobs;

/**
 * Scheduler plugin for Craft CMS 3.x
 *
 * Scheduler
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2018 Supercool
 */

use Craft;
use craft\base\SavableComponent;

use supercool\scheduler\jobs\ISchedulerJob;

class BaseSchedulerJob extends SavableComponent implements ISchedulerJob
{

	// Properties
	// =========================================================================

	/**
	 * Set this to true to allow the Job to be used with the ScheduleJob Field Type
	 *
	 * @var bool
	 */
	protected $allowedInFieldType = false;

	/**
	 * The model instance associated with the current component instance.
	 *
	 * @var BaseModel
	 */
	public $model;


	// Public Methods
	// =========================================================================

	/**
	 * @inheritDoc ISchedulerJob::run()
	 *
	 * @return bool
	 */
	public function run()
	{
		return true;
	}

	/**
	 * @inheritDoc IScheduler_Job::checkJobIsAllowedInFieldType()
	 *
	 * @return bool
	 */
	public function checkJobIsAllowedInFieldType()
	{
		return $this->allowedInFieldType;
	}

}
