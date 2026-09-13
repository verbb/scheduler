# Usage
Scheduler stores jobs with the date they should run. A recurring console command checks for jobs whose date has arrived and executes them. Saving a scheduled job does not run it immediately, so both the scheduled date and the recurring command are part of setup.

For example, use the Scheduler field on an entry to schedule a future re-save. Select the built-in re-save job in the field settings, add the field to the entry layout, enter a future date and save the entry. Configure your server to run the following command every minute, replacing the path with your Craft project's actual console script:

```shell
* * * * * /var/www/my-awesome-site/craft scheduler/command/run
```

What that command does is check if there are any Jobs to run and then runs them if there are. It also outputs what it is doing every time it runs, if you don’t want this emailed to you when using cron, then simply append `>/dev/null 2>&1` to the command.

After the scheduled time, run the command manually once if necessary and inspect its output. Confirm that the intended entry was re-saved. If nothing runs, check the server's time zone, the scheduled date and whether the recurring command uses the correct project path and PHP runtime.

## Anatomy of a Job
A Job is a bit like a simple version of a Task with a date. It is essentially a class that must extend the `BaseSchedulerJob` and then would typically do something in its `run()` method. Take a look at the built in `SchedulerReSaveElementJob` Job if you want to create your own.

## Scheduler Job Model

## Properties
Scheduler Job model objects have the following properties:

### `id`
The Job’s ID.

### `type`
The name of Job’s class, e.g. `SchedulerReSaveElementJob`.

### `date`
A `DateTime` object of the date the Job should be run on.

### `context`
The context the job was created from e.g. 'field' or 'programmatic'

### `settings`
An array of settings that can be used by the Job’s class.

## Methods
Scheduler Job model objects have the following methods:

### `getJobType()`
Returns the Job type this Job is using, which will be the class initialized and prepped with a Job model or `false` if it couldn’t be loaded for whatever reason.


## Scheduling Jobs
You can schedule a Job one of two ways - in PHP via the internal API or via the field type. The following service method is available to do just that:

### `Scheduler::$plugin->jobs->addJob($type, $date, $context = 'global', $settings = [])`
This method takes the job details, makes a model and passes it on to be saved unless there is a job with the same type, context and settings, in which case it just updates that jobs’ date. The parameters map to the properties of the Job model, the only difference being that `$date` can take a string as well as a `DateTime` object.


## Built-in Jobs
There is currently one built-in Job the sole purpose of which is to re-save an element. It can be scheduled from two places: the `Elements::EVENT_AFTER_SAVE_ELEMENT` event or the supplied field type.

When the `enableReSaveElementOnElementSave` config variable is set to `true` then every time an element is saved a Job will get scheduled to re-save that element if it has a `postDate` or `expiryDate` property that is set to the future.

When used in the field type the date will be set from whatever is entered into the field.

When the element implements Craft’s nested-element interface and has an owner, the built-in job also re-saves that owner after saving the nested element.


## Field Type
The field type allows users to select a date on which a Job should run, you set the Job type in the settings of the field. It can be used anywhere a normal field can and simply saves a `DateTime` object as its value - so you can even use it when fetching elements.

## Custom Job
Use the following event you can add your custom Job types to be accessed from the field type. It should return an array in the following format:

```php
use Craft;
use yii\base\Event;

use verbb\scheduler\services\Jobs as SchedulerJobs;
use verbb\scheduler\events\RegisterSchedulerJobTypesEvent;

Event::on(SchedulerJobs::class, SchedulerJobs::EVENT_REGISTER_SCHEDULER_JOB_TYPES, function (RegisterSchedulerJobTypesEvent $event) {
    $event->types[] = [
        'label' => 'Custom job title',
        'value' => CustomSchedulerJob::class,
    ];
});
```