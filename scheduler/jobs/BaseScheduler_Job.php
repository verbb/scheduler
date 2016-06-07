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

}
