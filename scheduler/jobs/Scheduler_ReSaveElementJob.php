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

class Scheduler_ReSaveElementJob extends BaseScheduler_Job
{

	// Properties
	// =========================================================================

	/**
	 * Set this to true to allow the Job to be used in the ScheduleJob Field Type
	 *
	 * @var bool
	 */
	protected $allowedInFieldType = true;

	// Public Methods
	// =========================================================================

	/**
	 * @inheritDoc IScheduler_Job::run()
	 *
	 * @return bool
	 */
	public function run()
	{
		// Get the model
		$job = $this->model;

		// Get the elementId from the model settings
		$elementId = $job->settings['elementId'];

		try
		{
			// Get the element model
			$element = craft()->elements->getElementById($elementId);

			// Check there was one
			if (!$element) {
				return false;
			}

			// Re-save the element
			if (craft()->elements->saveElement($element, false))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		catch (\Exception $e)
		{
			SchedulerPlugin::log(Craft::t('An exception was thrown while trying to save the '.$this->_elementType.' with the ID “'.$this->_elementIds[$step].'”: '.$e->getMessage()), LogLevel::Error);
			return false;
		}

		return false;
	}

}
