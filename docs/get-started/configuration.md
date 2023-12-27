# Configuration
Create a `scheduler.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Scheduler, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'enableReSaveElementOnElementSave' => true,
    ]
];
```

## Configuration options
- `enableReSaveElementOnElementSave` - Enables the Re-save Element Job to be scheduled every time an element is saved.

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Scheduler.
