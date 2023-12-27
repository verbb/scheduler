<?php
namespace verbb\scheduler\jobs;

use craft\base\SavableComponent;

class BaseSchedulerJob extends SavableComponent implements ISchedulerJob
{
    // Properties
    // =========================================================================

    public mixed $model;


    // Public Methods
    // =========================================================================

    public function run(): bool
    {
        return true;
    }

}
