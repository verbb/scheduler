<?php

/**
 * Scheduler plugin for Craft CMS 3.x
 *
 * Scheduler
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2018 Supercool
 */

namespace supercool\scheduler\services;

use Craft;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\App;
use craft\helpers\Json;
use craft\helpers\DateTimeHelper;
use yii\base\Component;
use yii\base\Exception;

use supercool\scheduler\models\Job;
use supercool\scheduler\records\Job as JobRecord;
use supercool\scheduler\events\RegisterSchedulerJobTypesEvent;

class Jobs extends Component
{

  // Constants
  // =========================================================================
  /**
   * @event RegisterComponentTypesEvent The event that is triggered when registering field types.
   */
  const EVENT_REGISTER_SCHEDULER_JOB_TYPES = 'registerSchedulerJobTypes';

  private $_allJobIds;
  private $_jobsById;
  private $_fetchedAllJobs = false;


  /**
   * Get available job types for our custom field
   */
  public function getAvailableJobTypes()
  {
      $jobTypes = [];

      $jobTypes[] = [
        'label' => 'Re-save element',
        'value' => 'supercool\scheduler\jobs\SchedulerReSaveElementJob',
        'default' => true
      ];

      // Third Party
      $event = new RegisterSchedulerJobTypesEvent([
          'types' => $jobTypes
      ]);

      $this->trigger(self::EVENT_REGISTER_SCHEDULER_JOB_TYPES, $event);

      return $event->types;
  }


  /**
   * Returns all of the Job IDs.
   *
   * @return array
   */
  public function getAllJobIds()
  {
    if (!isset($this->_allJobIds))
    {
      if ($this->_fetchedAllJobs)
      {
        $this->_allJobIds = array_keys($this->_jobsById);
      }
      else
      {
        $this->_allJobIds = (new Query())
          ->select(['id'])
          ->from(['{{%scheduler_jobs}}'])
          ->column();
      }
    }

    return $this->_allJobIds;
  }


  /**
   * Returns all Jobs.
   *
   * @param string|null $indexBy
   * @return array
   */
  public function getAllJobs($indexBy = null)
  {
    if (!$this->_fetchedAllJobs)
    {
      $jobRecords = JobRecord::find()->All();

      if( !$jobRecords )
      {
        return [];
      }

      foreach ($jobRecords as $jobRecord)
      {
        $this->_jobsById[$jobRecord->id] = $this->_createJobFromRecord($jobRecord);
      }

      $this->_fetchedAllJobs = true;
    }

    if ($indexBy == 'id')
    {
      return $this->_jobsById;
    }
    else if (!$indexBy)
    {
      return array_values($this->_jobsById);
    }
    else
    {
      $jobs = array();
      foreach ($this->_jobsById as $job)
      {
        $jobs[$job->$indexBy] = $job;
      }
      return $jobs;
    }
  }


  /**
   * Returns a Job by its ID.
   *
   * @param $jobId
   * @return Job|null
   */
  public function getJobById($jobId)
  {
    if (!isset($this->_jobsById) || !array_key_exists($jobId, $this->_jobsById))
    {
      $jobRecord = JobRecord::find()->where(['id' => $jobId])->one();

      if ($jobRecord)
      {
        $this->_jobsById[$jobId] = $this->_createJobFromRecord($jobRecord);
      }
      else
      {
        $this->_jobsById[$jobId] = null;
      }
    }

    return $this->_jobsById[$jobId];
  }


  /**
   * Returns an array of Jobs that are due to be executed (i.e. their date is
   * now or in the past)
   *
   * @return null|array
   */
  public function getOverdueJobs()
  {
    $currentTime = DateTimeHelper::currentTimeStamp();
    $currentTimeDb = Db::prepareDateForDb($currentTime);

    $jobRecords = JobRecord::find()->where('date <= :now', [':now' => $currentTimeDb])
      ->orderBy('date')
      ->all();

    if ( $jobRecords )
    {
      $jobModels = [];

      foreach ($jobRecords as $jobRecord)
      {
        $jobModels[] = $this->_createJobFromRecord($jobRecord);
      }

      return $jobModels;
    }
    else
    {
      return null;
    }
  }


  /**
   * [getNextJobDate description]
   * @return [type] [description]
   */
  public function getNextJobDate()
  {
    $nextJob = JobRecord::find()->orderBy('date')->one();

    if ($nextJob)
    {
      $job = $this->_createJobFromRecord($nextJob);
      return $job->date;
    }
    else
    {
      return false;
    }
  }


  /**
   * Simply takes the job details, makes a model and passes it to save unless
   * there is a job with the same type, context and settings, in which case it
   * just updates that jobs’ date
   */
  public function addJob($type, $date, $context = 'global', $settings = array())
  {
    // Make the model
    $job = new Job();
    $job->type     = $type;
    $job->date     = $date;
    $job->context  = $context;
    $job->settings = $settings;

    // Try and find an existing job
    $existingJob = JobRecord::find()->where([
      'type'     => $job->type,
      'context'  => $context,
      'settings' => Json::encode($job->settings),
    ])->one();

    // If there is an existing job, update the model with its id
    if ($existingJob)
    {
      $job->id = $existingJob->id;
      $this->saveJob($job);
    // Other wise just save the new one
    }
    else
    {
      $this->saveJob($job);
    }
  }


  /**
   * Saves a Job
   *
   * @param Job $job
   * @throws \Exception
   * @return bool
   */
  public function saveJob(Job $job)
  {
    /**
     * Don’t bother saving the job if the date has actually passed
     *
     * We allow a tolerance of 60 seconds here to cope with situations where
     * the Job has taken a while to get here.
     */
    if ($job->date->getTimestamp() < (DateTimeHelper::currentTimeStamp() - 60))
    {
      return false;
    }

    if ($job->id)
    {
      $jobRecord = JobRecord::find()->where(['id' => $job->id])->one();

      if (!$jobRecord)
      {
        throw new Exception(Craft::t('scheduler', 'No Job exists with the ID “{id}”', ['id' => $job->id]));
      }

      $oldJob = $this->_createJobFromRecord($jobRecord);
      $isNewJob = false;
    }
    else
    {
      $jobRecord = new JobRecord();
      $isNewJob = true;
    }

    $jobRecord->type     = $job->type;
    $jobRecord->date     = $job->date;
    $jobRecord->context  = $job->context;
    $jobRecord->settings = $job->settings;

    $jobRecord->validate();
    $job->addErrors($jobRecord->getErrors());

    if ( !$job->hasErrors() )
    {
      $db = Craft::$app->getDb();
      $transaction = $db->beginTransaction();

      try
      {
        // Save it!
        $jobRecord->save(false);

        // Now that we have an Job ID, save it on the model
        if (!$job->id)
        {
          $job->id = $jobRecord->id;
        }

        // Might as well update our cache of the Job while we have it.
        $this->_jobsById[$job->id] = $job;

        // Bust the cache of the next job date
        Craft::$app->getCache()->delete('scheduler_nextjobdate');

        if ($transaction !== null)
        {
          $transaction->commit();
        }
      }
      catch (\Exception $e)
      {
        if ($transaction !== null)
        {
          $transaction->rollback();
        }
        throw $e;
      }
      return true;
    }
    else
    {
      return false;
    }
  }


  /**
   * Deletes a Job
   *
   * @param int $jobId
   * @throws \Exception
   * @return bool
   */
  public function deleteJobById($jobId)
  {
    if (!$jobId)
    {
      return false;
    }

    $db = Craft::$app->getDb();
    $transaction = $db->beginTransaction();

    try
    {
      $affectedRows = Craft::$app->getDb()->createCommand()->delete(
        '{{%scheduler_jobs}}',
        ['id' => $jobId]
      )->execute();

      if ($transaction !== null)
      {
        $transaction->commit();
      }

      return (bool) $affectedRows;
    }
    catch (\Exception $e)
    {
      if ($transaction !== null)
      {
        $transaction->rollback();
      }
      throw $e;
    }
  }


  // Private Methods
  // =========================================================================

  /**
   * Creates a Job model with attributes from a JobRecord.
   *
   * @param JobRecord|null $jobRecord
   * @return Job|null
   */
  private function _createJobFromRecord(JobRecord $jobRecord = null)
  {
    if ( !$jobRecord )
    {
      return null;
    }

    $job = new Job($jobRecord->toArray([
      'id',
      'type',
      'date',
      'context',
      'settings'
    ]));

    if ( $job->settings )
    {
      $job->settings = Json::decode($job->settings);
    }

    return $job;
  }

}
