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
use yii\helpers\Console;

use supercool\scheduler\Scheduler;

class CommandController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle scheduler console commands
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'something';

        echo "Welcome to the console SchedulerController actionIndex() method\n";

        return $result;
    }

}
