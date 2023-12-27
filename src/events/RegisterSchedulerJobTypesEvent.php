<?php
namespace verbb\scheduler\events;

use yii\base\Event;

class RegisterSchedulerJobTypesEvent extends Event
{
    // Properties
    // =========================================================================
    
    public array $types = [];
}
