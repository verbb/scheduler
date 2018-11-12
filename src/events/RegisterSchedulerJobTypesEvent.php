<?php

namespace supercool\scheduler\events;

use yii\base\Event;

class RegisterSchedulerJobTypesEvent extends Event
{
    // Properties
    // =========================================================================
    public $types = [];
}
