<?php
namespace verbb\scheduler\jobs;

interface ISchedulerJob
{
    // Public Methods
    // =========================================================================

    public function run(): bool;
}
