# Changelog

## 4.0.1 - 2025-07-18

### Changed
- Fix call to `end()`.

### Fixed
- Fix an error when running the re-save job for (outdated) Matrix elements.
- Fix plugin install check.

## 4.0.0 - 2024-05-13

### Changed
- Now requires PHP `8.2.0+`.
- Now requires Craft `5.0.0+`.

## 3.0.2 - 2025-07-18

### Fix
- Fix call to `end()`.

## 3.0.1 - 2025-01-08

### Fixed 
- Fixed an error when passing an array of settings to the `addJob` service method. [#5](https://github.com/verbb/scheduler/issues/5). (thanks @elivz).

## 3.0.0 - 2023-12-28

> {note} The plugin’s package name has changed to `verbb/scheduler`. Scheduler will need be updated to 3.0 from a terminal, by running `composer require verbb/scheduler && composer remove supercool/scheduler`.

### Changed
- Migration to `verbb/scheduler`.
- Now requires Craft 4.0+.

## 2.0.2 - 2020-03-13

### Added
- Feed me support added to field.

## 2.0.1 - 2019-03-12

### Changed
- Removes deleted elements from the queue.

## 2.0.0 - 2018-11-09

### Added
- Initial Craft CMS 3 release.
