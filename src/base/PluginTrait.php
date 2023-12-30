<?php
namespace verbb\scheduler\base;

use verbb\scheduler\Scheduler;
use verbb\scheduler\services\Jobs;

use verbb\base\LogTrait;
use verbb\base\helpers\Plugin;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static Scheduler $plugin;


    // Traits
    // =========================================================================

    use LogTrait;


    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Plugin::bootstrapPlugin('scheduler');

        return [
            'components' => [
                'jobs' => Jobs::class,
            ],
        ];
    }


    // Public Methods
    // =========================================================================

    public function getJobs(): Jobs
    {
        return $this->get('jobs');
    }

}