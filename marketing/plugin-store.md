Scheduler gives Craft projects a straightforward way to run application jobs at a chosen date and time. Schedule work from project code and let Craft’s queue handle the execution.

Create a scheduled job with the payload your task needs and a date when it should become eligible. Scheduler checks for due work and hands it to Craft’s queue rather than holding a web request open.

## Features

- **Dated jobs:** Associate application work with the date and time it should run.
- **Craft queue:** Execute due work through Craft’s established background-processing system.
- **Developer API:** Create scheduled jobs from project modules and plugins.
- **Reminders:** Power project-owned notification and follow-up workflows.
- **Flexible tasks:** Schedule the job class and payload the project requires.
- **Configurable checks:** Control the mechanism that finds and releases due work.
- **Project automation:** Use the plugin from modules or custom plugins for reminders, deferred processing, publishing helpers, or another domain task. Configuration controls how due jobs are discovered and processed.
