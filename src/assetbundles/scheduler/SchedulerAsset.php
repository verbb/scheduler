<?php

/**
 * Scheduler plugin for Craft CMS 3.x
 *
 * Scheduler
 *
 * @link      http://supercooldesig.co.uk
 * @copyright Copyright (c) 2018 Supercool
 */

namespace supercool\scheduler\assetbundles\scheduler;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SchedulerAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@supercool/scheduler/assetbundles/scheduler/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/scheduler.js',
        ];

        $this->css = [
            'css/scheduler.css',
        ];

        parent::init();
    }
}
