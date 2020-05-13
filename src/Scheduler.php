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
use craft\services\Fields;
use craft\services\Plugins;
use craft\services\Elements;
use craft\helpers\DateTimeHelper;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\feedme\events\RegisterFeedMeFieldsEvent;
use craft\feedme\services\Fields as feedMeFields;

use supercool\scheduler\fields\feedme\ScheduleJobDataField;
use yii\base\Event;

use supercool\scheduler\models\Settings;
use supercool\scheduler\fields\ScheduleJob as ScheduleJobField;

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

        // Register our fields
        Event::on(
            Fields::className(),
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ScheduleJobField::class;
            }
        );

        if(Craft::$app->plugins->isPluginInstalled('feed-me')) {
            Event::on(feedMeFields::class,
                feedMeFields::EVENT_REGISTER_FEED_ME_FIELDS,
                function(RegisterFeedMeFieldsEvent $e) {
                    $e->fields[] = ScheduleJobDataField::class;
                });
        }

        if ( $this->getSettings()->enableReSaveElementOnElementSave )
        {
          Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function(Event $event) {

            $element = $event->element;

            if ( !$element )
            {
              return true;
            }

            // Work out the date the element should be re-saved due
            // to its post or expiry date
            $currentTime = DateTimeHelper::currentTimeStamp();

            $date = null;
            $postDate = null;

            if (isset($element['postDate']) && $element['postDate'])
            {
              $postDate = $element->postDate->getTimestamp();
            }

            $expiryDate = null;
            if (isset($element['expiryDate']) && $element['expiryDate'])
            {
              $expiryDate = $element->expiryDate->getTimestamp();
            }

            if ($postDate && $postDate > $currentTime)
            {
              $date = $postDate;
            } else if ($expiryDate && $expiryDate > $currentTime)
            {
              $date = $expiryDate;
            }

            // If we have a date then add the job
            if ( !is_null($date) )
            {
              $context = 'programmatic:'.$date;
              $this->jobs->addJob('supercool\scheduler\jobs\SchedulerReSaveElementJob', (new \DateTime())->setTimestamp($date), $context, ['elementId' => $element->id]);
            }
          });
        }

        Craft::info(Craft::t('scheduler', '{name} plugin loaded', ['name' => $this->name]), __METHOD__);
    }

    // Protected Methods
    // =========================================================================

    protected function createSettingsModel()
    {
      return new Settings();
    }

}
