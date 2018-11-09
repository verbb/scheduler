<?php

/**
 * Scheduler plugin for Craft CMS 3.x
 *
 * Scheduler
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2018 Supercool
 */

namespace supercool\scheduler\console\controllers;

use Craft;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use craft\helpers\DateTimeHelper;

use supercool\scheduler\Scheduler;

class CommandController extends Controller
{
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

        // Check if we know there is nothing to run from the cache and
        // so don’t need to check the db
        $nextJobDate = Craft::$app->getCache()->get('scheduler_nextjobdate');

        // If it is actually a date, then work out if we need to exit
        if ($nextJobDate instanceof \DateTime)
        {
            if ($nextJobDate->getTimestamp() > DateTimeHelper::currentTimeStamp())
            {
                $this->_end("The next job is at ".$nextJobDate->format('c'));
            }
            else
            {
                Craft::$app->getCache()->delete('scheduler_nextjobdate');
            }
        }
        // If there are no dates then bail - that will be busted when a new one is
        // added or the cache expires
        else if ($nextJobDate == 'nodate')
        {
            $this->_end();
        }
        // If we got this far then we need to check the next job
        else
        {
            // Get the date of the next job
            $nextJobDate = Scheduler::$plugin->jobs->getNextJobDate();

            // If that was false, then there is no jobs at all so set the cache
            // to true - it will be busted if a job is ever saved
            if (!$nextJobDate) {
                Craft::$app->getCache()->set('scheduler_nextjobdate', 'nodate');
                $this->_end();
            }

            // If the next job date is in the future, then set the cache and end
            if ($nextJobDate->getTimestamp() > DateTimeHelper::currentTimeStamp())
            {
                Craft::$app->getCache()->set('scheduler_nextjobdate', $nextJobDate);
                $this->_end("The next job is at ".$nextJobDate->format('c'));
            }
        }

        // If we got this far then there must be overdue jobs so get and loop them
        $jobs = Scheduler::$plugin->jobs->getOverdueJobs();
        if ( $jobs )
        {
            echo $sep;
            foreach ($jobs as $job)
            {
                // Get the job type
                $jobType = $job->getJobType();

                // Run it
                echo "Running job #{$job->id} ...".PHP_EOL;
                $result = $jobType->run();

                // If the job ran ok, then delete it
                if ($result)
                {
                    Scheduler::$plugin->jobs->deleteJobById($job->id);
                    echo "Job #{$job->id} exited ok.";
                }
                // It didn’t run ok, so feed something back
                else
                {
                  echo "Job #{$job->id} failed to run.";
                }
                echo $sep;
            }
            $this->_end('Schedule complete.');
        }
        else
        {
            $this->_end();
        }
    }


    /**
     * [_end description]
     * @param  [type] $noJobs [description]
     * @return [type]         [description]
     */
    private function _end($msg = false)
    {
        if (!$msg) {
            $msg = "No jobs are due to run.";
        }
        echo PHP_EOL.$msg.PHP_EOL;
        return ExitCode::OK;
    }

}
