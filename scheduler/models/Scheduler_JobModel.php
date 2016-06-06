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

class Scheduler_JobModel extends BaseModel
{

	// Properties
	// =========================================================================

	// Public Methods
	// =========================================================================

	public function __toString()
	{
		return Craft::t($this->id);
	}

	// Protected Methods
	// =========================================================================

	/**
	 * Defines this model's attributes.
	 *
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'id'       => AttributeType::Number,
			'type'     => AttributeType::String,
			'date'     => AttributeType::DateTime,
			'settings' => AttributeType::Mixed,
		);
	}

}
