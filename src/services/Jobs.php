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
use craft\helpers\App;
use craft\helpers\DateTimeHelper;
use yii\base\Component;
use yii\base\Exception;

use supercool\scheduler\models\Job;
use supercool\scheduler\records\Job as JobRecord;

class Jobs extends Component
{

  private $_allJobIds;
  private $_jobsById;
  private $_fetchedAllJobs = false;


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
      Craft::dd($this->_jobsById);
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
    $currentTimeDb = DateTimeHelper::currentTimeStamp();

    $jobs = (new Query())
      ->from(['{{%scheduler_jobs}}'])
      ->where('date <= :now', [':now' => $currentTimeDb])
      ->orderBy('date')
      ->all();

    if ($jobs)
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
    $currentTimeDb = DateTimeHelper::currentTimeStamp();

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

    return $job;
  }

}
