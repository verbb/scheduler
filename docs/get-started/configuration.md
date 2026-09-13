# Configuration

You can customise Scheduler’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `scheduler.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will disable automatic scheduling when elements are saved:

```php
<?php

return [
    'enableReSaveElementOnElementSave' => false,
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `enableReSaveElementOnElementSave`

**Type:** `bool` · **Default:** `true`

Enables the Re-save Element Job to be scheduled every time an element is saved.
:::


## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Scheduler.
