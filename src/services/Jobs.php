<?php
namespace verbb\scheduler\services;

use verbb\scheduler\events\RegisterSchedulerJobTypesEvent;
use verbb\scheduler\models\Job;
use verbb\scheduler\records\Job as JobRecord;

use Craft;
use craft\db\Query;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\helpers\Json;

use yii\base\Component;
use yii\base\Exception;
use verbb\scheduler\jobs\SchedulerReSaveElementJob;

class Jobs extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_REGISTER_SCHEDULER_JOB_TYPES = 'registerSchedulerJobTypes';


    // Properties
    // =========================================================================

    private array $_allJobIds = [];
    private array $_jobsById = [];
    private bool $_fetchedAllJobs = false;


    // Public Methods
    // =========================================================================

    public function getAvailableJobTypes(): array
    {
        $jobTypes = [];

        $jobTypes[] = [
            'label' => 'Re-save element',
            'value' => SchedulerReSaveElementJob::class,
            'default' => true,
        ];

        $event = new RegisterSchedulerJobTypesEvent([
            'types' => $jobTypes,
        ]);

        $this->trigger(self::EVENT_REGISTER_SCHEDULER_JOB_TYPES, $event);

        return $event->types;
    }

    public function getAllJobIds(): array
    {
        if (!isset($this->_allJobIds)) {
            if ($this->_fetchedAllJobs) {
                $this->_allJobIds = array_keys($this->_jobsById);
            } else {
                $this->_allJobIds = (new Query())
                    ->select(['id'])
                    ->from(['{{%scheduler_jobs}}'])
                    ->column();
            }
        }

        return $this->_allJobIds;
    }

    public function getAllJobs(?string $indexBy = null): array
    {
        if (!$this->_fetchedAllJobs) {
            $jobRecords = JobRecord::find()->All();

            if (!$jobRecords) {
                return [];
            }

            foreach ($jobRecords as $jobRecord) {
                $this->_jobsById[$jobRecord->id] = $this->_createJobFromRecord($jobRecord);
            }

            $this->_fetchedAllJobs = true;
        }

        if ($indexBy == 'id') {
            return $this->_jobsById;
        }

        if (!$indexBy) {
            return array_values($this->_jobsById);
        }

        $jobs = [];

        foreach ($this->_jobsById as $job) {
            $jobs[$job->$indexBy] = $job;
        }

        return $jobs;
    }

    public function getJobById(?int $jobId): ?Job
    {
        if (!isset($this->_jobsById) || !array_key_exists($jobId, $this->_jobsById)) {
            $jobRecord = JobRecord::find()->where(['id' => $jobId])->one();

            if ($jobRecord) {
                $this->_jobsById[$jobId] = $this->_createJobFromRecord($jobRecord);
            } else {
                $this->_jobsById[$jobId] = null;
            }
        }

        return $this->_jobsById[$jobId];
    }

    public function getOverdueJobs(): ?array
    {
        $currentTime = DateTimeHelper::currentTimeStamp();
        $currentTimeDb = Db::prepareDateForDb($currentTime);

        $jobRecords = JobRecord::find()->where('date <= :now', [':now' => $currentTimeDb])
            ->orderBy('date')
            ->all();

        if ($jobRecords) {
            $jobModels = [];

            foreach ($jobRecords as $jobRecord) {
                $jobModels[] = $this->_createJobFromRecord($jobRecord);
            }

            return $jobModels;
        }

        return null;
    }

    public function getNextJobDate(): \DateTime|bool|null
    {
        $nextJob = JobRecord::find()->orderBy('date')->one();

        if ($nextJob) {
            return $this->_createJobFromRecord($nextJob)->date;
        }

        return false;
    }

    public function addJob($type, $date, $context = 'global', $settings = []): void
    {
        $job = new Job();
        $job->type = $type;
        $job->date = $date;
        $job->context = $context;
        $job->settings = $settings;

        // Try and find an existing job
        $existingJob = JobRecord::find()->where([
            'type' => $job->type,
            'context' => $context,
            'settings' => Json::encode($job->settings),
        ])->one();

        // If there is an existing job, update the model with its id
        if ($existingJob) {
            $job->id = $existingJob->id;
            // Otherwise just save the new one
        }

        $this->saveJob($job);
    }

    public function saveJob(Job $job): bool
    {
        // Don’t bother saving the job if the date has actually passed
        // We allow a tolerance of 60 seconds here to cope with situations where
        // the Job has taken a while to get here.
        if ($job->date->getTimestamp() < (DateTimeHelper::currentTimeStamp() - 60)) {
            return false;
        }

        if ($job->id) {
            $jobRecord = JobRecord::find()->where(['id' => $job->id])->one();

            if (!$jobRecord) {
                throw new Exception(Craft::t('scheduler', 'No Job exists with the ID “{id}”', ['id' => $job->id]));
            }

            $oldJob = $this->_createJobFromRecord($jobRecord);
            $isNewJob = false;
        } else {
            $jobRecord = new JobRecord();
            $isNewJob = true;
        }

        $jobRecord->type = $job->type;
        $jobRecord->date = $job->date;
        $jobRecord->context = $job->context;
        $jobRecord->settings = $job->settings;

        $jobRecord->validate();
        $job->addErrors($jobRecord->getErrors());

        if (!$job->hasErrors()) {
            $db = Craft::$app->getDb();
            $transaction = $db->beginTransaction();

            try {
                // Save it!
                $jobRecord->save(false);

                // Now that we have a Job ID, save it on the model
                if (!$job->id) {
                    $job->id = $jobRecord->id;
                }

                // Might as well update our cache of the Job while we have it.
                $this->_jobsById[$job->id] = $job;

                // Bust the cache of the next job date
                Craft::$app->getCache()->delete('scheduler_nextjobdate');

                if ($transaction !== null) {
                    $transaction->commit();
                }
            } catch (\Exception $e) {
                if ($transaction !== null) {
                    $transaction->rollback();
                }
                
                throw $e;
            }

            return true;
        }

        return false;
    }

    public function deleteJobById(int $jobId): bool
    {
        if (!$jobId) {
            return false;
        }

        $db = Craft::$app->getDb();
        $transaction = $db->beginTransaction();

        try {
            $affectedRows = Craft::$app->getDb()->createCommand()->delete('{{%scheduler_jobs}}', ['id' => $jobId])->execute();

            if ($transaction !== null) {
                $transaction->commit();
            }

            return (bool) $affectedRows;
        } catch (\Exception $e) {
            if ($transaction !== null) {
                $transaction->rollback();
            }

            throw $e;
        }
    }


    // Private Methods
    // =========================================================================

    private function _createJobFromRecord(?JobRecord $jobRecord = null): ?Job
    {
        if (!$jobRecord) {
            return null;
        }

        $job = new Job($jobRecord->toArray([
            'id',
            'type',
            'date',
            'context',
            'settings'
        ]));

        if ($job->settings) {
            $job->settings = Json::decode($job->settings);
        }

        return $job;
    }

}
