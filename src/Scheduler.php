<?php

/**
 * Scheduler plugin for Craft CMS 3.x
 *
 * Scheduler
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2018 Supercool
 */

namespace supercool\scheduler;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

class Scheduler extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Scheduler::$plugin
     *
     * @var Scheduler
     */
    public static $plugin;

    // Public Methods
    // =========================================================================
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'jobs' => \supercool\scheduler\services\Jobs::class,
        ]);

        // Do something after we're installed
        Event::on(
            Plugins::className(),
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        Craft::info(Craft::t('scheduler', '{name} plugin loaded', ['name' => $this->name]), __METHOD__);
    }

    // Protected Methods
    // =========================================================================

}
