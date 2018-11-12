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

}
