<?php
namespace verbb\scheduler\jobs;

use Craft;
use craft\base\NestedElementInterface;

use Throwable;

class SchedulerReSaveElementJob extends BaseSchedulerJob
{
    // Public Methods
    // =========================================================================

    public function run(): bool
    {
        // Get the model
        $job = $this->model;

        // Get the elementId from the model settings
        $elementId = $job->settings['elementId'];

        try {
            // Get the element model
            $element = Craft::$app->getElements()->getElementById((int)$elementId);

            // Check there was one - if not then do nothing and return true, so it is removed from the queue
            if (!$element) {
                return true;
            }

            // Re-save the element using the Element Types save method
            // Now save it
            if (Craft::$app->getElements()->saveElement($element, false)) {
                // Check if the element has an owner
                if ($element instanceof NestedElementInterface) {
                    if ($owner = $element->getOwner()) {
                        Craft::$app->getElements()->saveElement($owner, false);
                    }
                }

                return true;
            }
        } catch (Throwable $e) {
            Craft::error(Craft::t('scheduler', 'An exception was thrown while trying to save the element with the ID “' . $elementId . '”: ' . $e->getMessage()));
        }

        return false;
    }

}
