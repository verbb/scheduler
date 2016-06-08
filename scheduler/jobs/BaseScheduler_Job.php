<?php
namespace Craft;

/**
 * Scheduler by Supercool
 *
 * @package   Scheduler
 * @author    Josh Angell
 * @copyright Copyright (c) 2016, Supercool Ltd
 * @link      http://plugins.supercooldesign.co.uk
 */

class BaseScheduler_Job extends BaseApplicationComponent implements IScheduler_Job
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
	 * @inheritDoc IScheduler_Job::run()
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
