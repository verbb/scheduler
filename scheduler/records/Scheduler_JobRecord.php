<?php
namespace Craft;

/**
 * Scheduler by Supercool
 *
 * @package   Scheduler
 * @author    Josh Angell
 * @copyright Copyright (c) 2017, Supercool Ltd
 * @link      https://github.com/supercool/Scheduler
 */

class Scheduler_JobRecord extends BaseRecord
{
	// Public Methods
	// =========================================================================

	/**
	 * @inheritDoc BaseRecord::getTableName()
	 *
	 * @return string
	 */
	public function getTableName()
	{
		return 'scheduler_jobs';
	}

	// Protected Methods
	// =========================================================================

	/**
	 * @inheritDoc BaseRecord::defineAttributes()
	 *
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'type'     => array(AttributeType::String, 'required' => true),
			'date'     => array(AttributeType::DateTime, 'required' => true),
			'context'  => array(AttributeType::String, 'default' => 'global'),
			'settings' => AttributeType::Mixed,
		);
	}

}
