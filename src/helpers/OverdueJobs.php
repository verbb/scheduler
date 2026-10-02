<?php
namespace verbb\scheduler\helpers;

use verbb\scheduler\models\Job;
use verbb\scheduler\records\Job as JobRecord;

use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\helpers\Json;

final class OverdueJobs
{
    // Static Methods
    // =========================================================================

    public static function get(?int $limit = null): ?array
    {
        $currentTime = DateTimeHelper::currentTimeStamp();
        $currentTimeDb = Db::prepareDateForDb($currentTime);

        $query = JobRecord::find()
            ->where('date <= :now', [':now' => $currentTimeDb])
            ->orderBy(['date' => SORT_ASC, 'id' => SORT_ASC]);

        if ($limit !== null) {
            $query->limit($limit);
        }

        $jobRecords = $query->all();

        if ($jobRecords) {
            $jobModels = [];

            foreach ($jobRecords as $jobRecord) {
                $jobModels[] = self::_createJobFromRecord($jobRecord);
            }

            return $jobModels;
        }

        return null;
    }


    // Private Methods
    // =========================================================================

    private static function _createJobFromRecord(JobRecord $jobRecord): Job
    {
        $job = new Job($jobRecord->toArray([
            'id',
            'type',
            'date',
            'context',
            'settings',
        ]));

        if ($job->settings) {
            $job->settings = Json::decode($job->settings);
        }

        return $job;
    }
}
