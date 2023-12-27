# Usage
You'll likely want to setup a cron job to trigger Scheduler's console command every minute:

```shell
* * * * * /var/www/my-awesome-site/craft scheduler/command/run
```

What that command does is check if there are any Jobs to run and then runs them if there are. It also outputs what it is doing every time it runs, if you don’t want this emailed to you when using cron, then simply append `>/dev/null 2>&1` to the command.

## Anatomy of a Job
A Job is a bit like a simple version of a Task with a date. It is essentially a class that must extend the `BaseSchedulerJob` and then would typically do something in its `run()` method. Take a look at the built in [Re-save Element](https://github.com/verbb/scheduler/blob/craft-4/src/jobs/SchedulerReSaveElementJob.php) Job if you want to create your own.

## Scheduler Job model

## Properties
Scheduler Job model objects have the following properties:

### `id`
The Job’s ID.

### `type`
The name of Job’s class, e.g. `SchedulerReSaveElementJob`.

### `date`
A [DateTime](https://craftcms.com/docs/templating/datetime) object of the date the Job should be run on.

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
This method takes the job details, makes a model and passes it on to be saved unless there is a job with the same type, context and settings, in which case it just updates that jobs’ date. The parameters map to the properties of the Scheduler_JobModel, the only difference being that `$date` can take a string as well as a [DateTime](https://craftcms.com/docs/templating/datetime) object.


## Built-in Jobs
There is currently one built-in Job the sole purpose of which is to re-save an element. It can be scheduled from two places: the [`Elements::EVENT_AFTER_SAVE_ELEMENT`](https://docs.craftcms.com/api/v3/craft-base-savablecomponentinterface.html#public-methods) event or the supplied field type.

When the `enableReSaveElementOnElementSave` config variable is set to `true` then every time an element is saved a Job will get scheduled to re-save that element if it has a `postDate` or `expiryDate` property that is set to the future.

When used in the field type the date will be set from whatever is entered into the field.

Currently when determining which element to save Scheduler will also check if the element is a MatrixBlock, SuperTable_Block and in each case save the parent element as well.


## Field type
The field type allows users to select a date on which a Job should run, you set the Job type in the settings of the field. It can be used anywhere a normal field can and simply saves a [DateTime](https://craftcms.com/docs/templating/datetime) object as its value - so you can even use it when fetching elements.

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