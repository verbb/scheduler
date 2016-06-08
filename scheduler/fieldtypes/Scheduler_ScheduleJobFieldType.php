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

class Scheduler_ScheduleJobFieldType extends DateFieldType
{

	// Public Methods
	// =========================================================================

	/**
	 * @inheritDoc IComponentType::getName()
	 *
	 * @return string
	 */
	public function getName()
	{
		return Craft::t('Schedule Job');
	}

	/**
	 * @inheritDoc ISavableComponentType::getSettingsHtml()
	 *
	 * @return string|null
	 */
	public function getSettingsHtml()
	{
		$typeOptions = $this->_getTypeOptions();

		$incrementOptions = array(15, 30, 60);
		$incrementOptions = array_combine($incrementOptions, $incrementOptions);

		return craft()->templates->render('scheduler/fieldtypes/ScheduleJob/settings', array(
			'typeOptions' => $typeOptions,
			'incrementOptions' => $incrementOptions,
			'settings' => $this->getSettings(),
		));
	}

	/**
	 * @inheritDoc IFieldType::getInputHtml()
	 *
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return string
	 */
	public function getInputHtml($name, $value)
	{
		$variables = array(
			'id'              => craft()->templates->formatInputId($name),
			'name'            => $name,
			'value'           => $value,
			'minuteIncrement' => $this->getSettings()->minuteIncrement
		);

		$input = '<div class="datetimewrapper">';
		$input .= craft()->templates->render('_includes/forms/date', $variables);
		$input .= ' '.craft()->templates->render('_includes/forms/time', $variables);
		$input .= '</div>';


		return $input;
	}

	/**
	 * @inheritDoc ISavableComponentType::prepSettings()
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	public function prepSettings($settings)
	{
		return $settings;
	}

	/**
	 * @inheritDoc IFieldType::onAfterElementSave()
	 *
	 * @return null
	 */
	public function onAfterElementSave()
	{
		$date = $this->element->getContent()->getAttribute($this->model->handle);
		$jobType = $this->getSettings()->type;

		if (!is_null($date)) {
			craft()->scheduler_jobs->addJob($jobType, $date, 'field', array(
				'elementId' => $this->element->id
			));
		}
	}


	// Private Methods
	// =========================================================================

	/**
	 * Prep the Job type options
	 *
	 * @return array
	 */
	private function _getTypeOptions()
	{
		$typeOptions = array(
			array(
				'label' => 'Re-save element',
				'value' => 'Scheduler_ReSaveElementJob',
				'default' => true
			)
		);

		// TODO: HOOK
		// TODO: add another job for saving parent element if the current one doesn’t pick it up

		return $typeOptions;
	}

	// Protected Methods
	// =========================================================================

	/**
	 * @inheritDoc BaseSavableComponentType::defineSettings()
	 *
	 * @return array
	 */
	protected function defineSettings()
	{
		$options = array();
		$default = '';
		$typeOptions = $this->_getTypeOptions();

		foreach ($typeOptions as $typeOption) {
			$options[] = $typeOption['value'];

			if (isset($typeOption['default']) && $typeOption['default'])
			{
				$default = $typeOption['value'];
			}
		}

		return array(
			'type' => array(AttributeType::Enum, 'values' => $options, 'default' => $default),
			'minuteIncrement' => array(AttributeType::Number, 'default' => 30, 'min' => 1, 'max' => 60),
		);
	}

}
