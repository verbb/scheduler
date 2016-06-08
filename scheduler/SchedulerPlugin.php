<?php
namespace Craft;

/**
 * Scheduler by Supercool
 *
 * @package   Scheduler
 * @author    Josh Angell
 * @copyright Copyright (c) 2016, Supercool Ltd
 * @link      http://plugins.supercooldesign.co.uk
 */

class SchedulerPlugin extends BasePlugin
{

	// Properties
	// =========================================================================


	// Public Methods
	// =========================================================================

	public function getName()
	{
		return Craft::t('Scheduler');
	}

	public function getVersion()
	{
		return '0.0.1';
	}

	public function getDeveloper()
	{
		return 'Supercool';
	}

	public function getDeveloperUrl()
	{
		return 'http://plugins.supercooldesign.co.uk';
	}

	public function hasCpSection()
	{
		return true;
	}

	public function init()
	{

		// Load some classes
		Craft::import('plugins.scheduler.jobs.*');

		// Check if we want to bind to elements.onSaveElement and run the ReSaveElement Job
		if (craft()->config->get('enableReSaveElementOnElementSave', 'scheduler'))
		{
			craft()->on('elements.onSaveElement', function(Event $event)
			{
				$element = $event->params['element'];
				if ($element) {

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
					if (!is_null($date))
					{
						craft()->scheduler_jobs->addJob('Scheduler_ReSaveElementJob', $date, 'programmatic', array('elementId' => $element->id));
					}

				}
			});
		}

	}


	// Protected Methods
	// =========================================================================

	protected function defineSettings()
	{
		return array(
			//
		);
	}

	// Private Methods
	// =========================================================================

}
