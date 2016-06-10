# Scheduler
A plugin for Craft CMS that allows you to schedule Jobs to be executed on a given date.

Includes a field type to allow your users to schedule Jobs from an element and [one built-in Job](#built-in-jobs) to re-save an element. This Job comes pre-loaded into the [`element.onSaveElement`](https://craftcms.com/docs/plugins/events-reference#elements-onSaveElement) event.

# Installation
1. Copy the `scheduler/` folder to your `craft/plugins/` folder.
2. Go to Settings > Plugins from your Craft control panel and hit install.
3. Set up a cron entry to hit the console command every minute:

```
* * * * * /var/www/my-awesome-site/craft/app/etc/console/yiic Scheduler
```

What that command does is check if there are any Jobs to run and then run them if there are. It also outputs what it is doing every time it runs, if you don’t want this emailed to you when using cron, then simply append `>/dev/null 2>&1` to the command.


# History
The initial problem that gave birth to this plugin was the need to bust the template caches of an Entry when it changes status from pending to live or from live to expired. Now I could have built that into the dependency part of the `{% cache %}` tag by getting the Entry first before caching its output, but I wanted to save the overhead of having to make all those database calls each time there is a request.

Equally as part of my work on [CacheMonster](https://github.com/supercool/Cache-Monster/) I needed to purge the cache of an external service such as Varnish. If I didn’t send that purge request then Varnish wouldn’t let the request through to Craft until its own cache of it had expired, regardless of if that Entry had actually already expired.

Finally, there are a lot of other plugins and services that rely on the element being saved to keep everything in sync such as Search Plus. To that end I decided I needed to schedule a given element to be re-saved at a particular point in time and so built the framework for that around a console command that could be hit with cron. I then abstracted a few things to make it easier to add other kinds of tasks and it turned into a task scheduler similar in principal to the [Laravel Task Scheduler](https://laravel.com/docs/master/scheduling).


# Anatomy of a Job
A Job is a bit like a simple version of a Task with a date. It is essentially a class that must extend the `BaseScheduler_Job` and then would typically do something in its `run()` method. Take a look at the built in [Re-save Element](scheduler/jobs/Scheduler_ReSaveElementJob.php) Job if you want to create your own.

Make sure to set the protected property `$allowedInFieldType` to `true` if you want it to be used in the field type.


# Scheduler_JobModel

## Properties
Scheduler_JobModel objects have the following properties:

### `id`
The Job’s ID.

### `type`
The name of Job’s class, e.g. `Scheduler_ReSaveElementJob`.

### `date`
A [DateTime](https://craftcms.com/docs/templating/datetime) object of the date the Job should be run on.

### `context`
The context the job was created from e.g. 'field' or 'programmatic'

### `settings`
An array of settings that can be used by the Job’s class.

## Methods
Scheduler_JobModel objects have the following methods:

### `getJobType()`
Returns the Job type this Job is using, which will be the class initialized and prepped with a Job model or `false` if it couldn’t be loaded for whatever reason.


# Scheduling Jobs
You can schedule a Job one of two ways - in PHP via the internal API or via the field type. The following service method is available to do just that:

### `Scheduler_JobsService::addJob($type, $date, $context = 'global', $settings = array())`
This method takes the job details, makes a model and passes it on to be saved unless there is a job with the same type, context and settings, in which case it just updates that jobs’ date. The parameters map to the properties of the Scheduler_JobModel, the only difference being that `$date` can take a string as well as a [DateTime](https://craftcms.com/docs/templating/datetime) object.


# Built-in Jobs
There is currently one built-in Job the sole purpose of which is to re-save an element. It can be scheduled from two places: the [`elements.onSaveElement`](https://craftcms.com/docs/plugins/events-reference#elements-onSaveElement) event or the supplied field type.

When the `enableReSaveElementOnElementSave` config variable is set to `true` then every time an element is saved a Job will get scheduled to re-save that element if it has a `postDate` or `expiryDate` property that is set to the future.

When used in the field type the date will be set from whatever is entered into the field.

Currently when determining which element to save Scheduler will also check if the element is a MatrixBlock, SuperTable_Block or Commerce_Variant and in each case save the parent element as well.


# Field type
The field type allows users to select a date on which a Job should run, you set the Job type in the settings of the field. It can be used anywhere a normal field can and simply saves a [DateTime](https://craftcms.com/docs/templating/datetime) object as its value - so you can even use it when fetching elements using the [ElementCriteriaModel](https://craftcms.com/docs/templating/elementcriteriamodel).


# Config variables

### `enableReSaveElementOnElementSave`
Enables the Re-save Element Job to be scheduled every time an element is saved. Defaults to `true`.


# Hooks

### `scheduler_registerJobTypes`
Use this hook to allow your custom Job types to be accessed from the field type. It should return an array in the following format:

```
return array(
 array(
   'name' => 'Some Custom Jobby',
   'class' => 'MyPlugin_MyCustomJob'
 )
);
```


# Changelog

### 0.1.0
Initial pre-release.
