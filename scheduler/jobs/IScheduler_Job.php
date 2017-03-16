<?php
namespace Craft;

/**
 * Scheduler by Supercool
 *
 * @package   Scheduler
 * @author    Josh Angell
 * @copyright Copyright (c) 2017, Supercool Ltd
 * @link      https://github.com/supercool/Scheduler
 */

interface IScheduler_Job
{

	// Public Methods
	// =========================================================================

	/**
	 * Run the specified Job.
	 *
	 * @return bool
	 */
	public function run();

	/**
	 * Returns whether the Job can be used with the ScheduleJob Field Type
	 *
	 * @return bool
	 */
	public function checkJobIsAllowedInFieldType();

}
