Scheduler gives Craft projects a straightforward way to run application jobs at a chosen date and time. Schedule work from project code and let Craft's queue handle the execution.

Create a scheduled job with the payload your task needs and a date when it should become eligible. Scheduler checks for due work and hands it to Craft's queue rather than holding a web request open.

## Features

- Associate application work with the date and time it should run.
- Execute due work through Craft's established background-processing system.
- Create scheduled jobs from project modules and plugins.
- Power project-owned notification and follow-up workflows.
- Schedule the job class and payload the project requires.
- Control the mechanism that finds and releases due work.
