<?php
namespace verbb\scheduler;

use verbb\scheduler\base\PluginTrait;
use verbb\scheduler\models\Settings;
use verbb\scheduler\fields\ScheduleJob as ScheduleJobField;
use verbb\scheduler\fields\feedme\ScheduleJobDataField;
use verbb\scheduler\jobs\SchedulerReSaveElementJob;

use Craft;
use craft\base\Plugin;
use craft\helpers\DateTimeHelper;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Elements;
use craft\services\Fields;

use yii\base\Event;

use DateTime;

use craft\feedme\events\RegisterFeedMeFieldsEvent;
use craft\feedme\services\Fields as feedMeFields;

class Scheduler extends Plugin
{
    // Properties
    // =========================================================================
    
    public string $schemaVersion = '1.1.0';


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_setPluginComponents();
        $this->_setLogging();
        $this->_registerFieldTypes();
        $this->_registerEventListeners();
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerFieldTypes(): void
    {
        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = ScheduleJobField::class;
        });
    }

    private function _registerEventListeners(): void
    {
        if ($this->getSettings()->enableReSaveElementOnElementSave) {
            Event::on(Elements::class, Elements::EVENT_AFTER_SAVE_ELEMENT, function(Event $event) {
                $element = $event->element;

                if (!$element || $element->getIsDraft() || $element->getIsRevision()) {
                    return true;
                }

                // Work out the date the element should be re-saved due to its post or expiry date
                $currentTime = DateTimeHelper::currentTimeStamp();

                $date = null;
                $postDate = null;
                $expiryDate = null;

                if (isset($element['postDate']) && $element['postDate']) {
                    $postDate = $element->postDate->getTimestamp();
                }

                if (isset($element['expiryDate']) && $element['expiryDate']) {
                    $expiryDate = $element->expiryDate->getTimestamp();
                }

                if ($postDate && $postDate > $currentTime) {
                    $date = $postDate;
                } else if ($expiryDate && $expiryDate > $currentTime) {
                    $date = $expiryDate;
                }

                // If we have a date then add the job
                if (!is_null($date)) {
                    $context = 'programmatic:' . $date;

                    $this->getJobs()->addJob(SchedulerReSaveElementJob::class, (new DateTime())->setTimestamp($date), $context, [
                        'elementId' => $element->id,
                    ]);
                }
            });
        }

        if (Craft::$app->getPlugins()->isPluginInstalled('feed-me')) {
            Event::on(feedMeFields::class, feedMeFields::EVENT_REGISTER_FEED_ME_FIELDS, function(RegisterFeedMeFieldsEvent $e) {
                $e->fields[] = ScheduleJobDataField::class;
            });
        }
    }
}
