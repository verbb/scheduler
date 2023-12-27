<?php
namespace verbb\scheduler\console\controllers;

use verbb\scheduler\Scheduler;

use Craft;
use craft\helpers\DateTimeHelper;

use yii\console\Controller;
use yii\console\ExitCode;

use DateTime;

class CommandController extends Controller
{
    // Properties
    // =========================================================================

    public $defaultAction = 'run';


    // Public Methods
    // =========================================================================

    public function actionRun(): void
    {
        $sep = PHP_EOL . "------------------------------" . PHP_EOL;
        echo PHP_EOL . "Checking scheduled jobs ..." . PHP_EOL;

        // Check if we know there is nothing to run from the cache and
        // so don’t need to check the db
        $nextJobDate = Craft::$app->getCache()->get('scheduler_nextjobdate');

        // If it is actually a date, then work out if we need to exit
        if ($nextJobDate instanceof DateTime) {
            if ($nextJobDate->getTimestamp() > DateTimeHelper::currentTimeStamp()) {
                $this->_end("The next job is at " . $nextJobDate->format('c'));
            } else {
                Craft::$app->getCache()->delete('scheduler_nextjobdate');
            }
        } else if ($nextJobDate == 'nodate') {
            // If there are no dates then bail - that will be busted when a new one is
            // added or the cache expires
            $this->_end();
        } else {
            // If we got this far then we need to check the next job
            // Get the date of the next job
            $nextJobDate = Scheduler::$plugin->getJobs()->getNextJobDate();

            // If that was false, then there is no jobs at all so set the cache
            // to true - it will be busted if a job is ever saved
            if (!$nextJobDate) {
                Craft::$app->getCache()->set('scheduler_nextjobdate', 'nodate');
                $this->_end();
            }

            // If the next job date is in the future, then set the cache and end
            if ($nextJobDate->getTimestamp() > DateTimeHelper::currentTimeStamp()) {
                Craft::$app->getCache()->set('scheduler_nextjobdate', $nextJobDate);
                $this->_end("The next job is at " . $nextJobDate->format('c'));
            }
        }

        // If we got this far then there must be overdue jobs so get and loop them
        $jobs = Scheduler::$plugin->getJobs()->getOverdueJobs();
        
        if ($jobs) {
            echo $sep;

            foreach ($jobs as $job) {
                // Get the job type
                $jobType = $job->getJobType();

                // Run it
                echo "Running job #{$job->id} ...".PHP_EOL;
                $result = $jobType->run();

                // If the job ran ok, then delete it
                if ($result) {
                    Scheduler::$plugin->getJobs()->deleteJobById($job->id);
                    echo "Job #{$job->id} exited ok.";
                } else {
                    // It didn’t run ok, so feed something back
                    echo "Job #{$job->id} failed to run.";
                }

                echo $sep;
            }

            $this->_end('Schedule complete.');
        } else {
            $this->_end();
        }
    }


    // Private Methods
    // =========================================================================

    private function _end($msg = false): int
    {
        if (!$msg) {
            $msg = "No jobs are due to run.";
        }

        echo PHP_EOL . $msg . PHP_EOL;

        return ExitCode::OK;
    }

}
