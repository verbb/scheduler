<?php
namespace Craft;

/**
 * A console command that generates the SOAP client classes for a given
 * Tessitura SOAP API.
 *
 * @package   SuperCalIntegrate
 * @author    Josh Angell
 * @copyright Copyright (c) 2015, Supercool Ltd
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
		echo "Checking scheduled jobs ...\n\n";

		$jobs = craft()->scheduler_jobs->getOverdueJobs();

		foreach ($jobs as $job) {
			echo  $job->date . "\n\n";
		}

		echo "Done!";

	}

}
