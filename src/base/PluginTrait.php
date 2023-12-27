<?php
namespace verbb\scheduler\base;

use verbb\scheduler\Scheduler;
use verbb\scheduler\services\Jobs;

use Craft;

use yii\log\Logger;

use verbb\base\BaseHelper;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static Scheduler $plugin;


    // Public Methods
    // =========================================================================

    public static function log($message, $attributes = []): void
    {
        if ($attributes) {
            $message = Craft::t('scheduler', $message, $attributes);
        }

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'scheduler');
    }

    public static function error($message, $attributes = []): void
    {
        if ($attributes) {
            $message = Craft::t('scheduler', $message, $attributes);
        }

        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'scheduler');
    }


    // Public Methods
    // =========================================================================

    public function getJobs(): Jobs
    {
        return $this->get('jobs');
    }


    // Private Methods
    // =========================================================================

    private function _setPluginComponents(): void
    {
        $this->setComponents([
            'jobs' => Jobs::class,
        ]);

        BaseHelper::registerModule();
    }

    private function _setLogging(): void
    {
        BaseHelper::setFileLogging('scheduler');
    }

}