<?php
namespace verbb\scheduler\jobs;

use Craft;
use craft\elements\MatrixBlock;

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
                // Check if the element has an owner (MatrixBlock, SuperTableBlockElement)
                // and if so, then save that too
                if ($element instanceof MatrixBlock || $element instanceof \verbb\supertable\elements\SuperTableBlockElement) {
                    $owner = $element->getOwner();

                    if ($owner) {
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
