<?php

namespace supercool\scheduler\controllers;

use Craft;
use craft\web\Controller;

use supercool\Scheduler\Scheduler;

class SchedulerController extends Controller
{

	protected $allowAnonymous = true;

    public function actionTest()
    {
        Craft::dd( Scheduler::$plugin->jobs->getNextJobDate() );
    }

}
