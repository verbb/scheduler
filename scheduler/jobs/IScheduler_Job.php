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

interface IScheduler_Job
{

	// Public Methods
	// =========================================================================

	/**
	 * Run the specified job.
	 *
	 * @return bool
	 */
	public function run();

}
