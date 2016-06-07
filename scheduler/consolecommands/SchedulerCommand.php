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

class SchedulerCommand extends BaseCommand
{

	// Properties
	// =========================================================================

	/**
	 * @var string
	 */
	public $defaultAction = 'run';

	// Public Methods
	// =========================================================================

	/**
	 * Runs any pending jobs
	 */
	public function actionRun()
	{
		$sep = PHP_EOL."------------------------------".PHP_EOL;

		echo PHP_EOL."Checking scheduled jobs ...".PHP_EOL;
		echo $sep;

		// TODO: Check cache for flag to make db call or not
		//       If its there, then proceed, if not then just bail.

		$jobs = craft()->scheduler_jobs->getOverdueJobs();

		if ($jobs) {

			foreach ($jobs as $job) {

				// Get the job type
				$jobType = $job->getJobType();

				// Run it
				echo "Running job #{$job->id} ...".PHP_EOL;
				$result = $jobType->run();

				// If the job ran ok, then delete it
				if ($result)
				{
					craft()->scheduler_jobs->deleteJobById($job->id);
					echo "Job #{$job->id} exited ok.";
				}
				// It didn’t run ok, so feed something back
				else
				{
					echo "Job #{$job->id} failed to run.";
				}

				echo $sep;

			}

			echo PHP_EOL."Schedule complete.";
		}
		else
		{
			echo PHP_EOL."No jobs are due to run.";
		}

		echo PHP_EOL;

		craft()->end();
	}

}
