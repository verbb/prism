# Changelog

## 2.0.0 - 2022-12-28

> {note} The plugin’s package name has changed to `verbb/prism`. Prism will need be updated to 2.0 from a terminal, by running `composer require verbb/prism && composer remove thejoshsmith/craft-prism-syntax-highlighting`.

### Changed
- Migration to `verbb/prism`.
- Now requires Craft 3.7+.

## 1.0.5 - 2020-09-14

### Fixed
- Fixed an issue where the clike dependency wasn't loaded by default.

## 1.0.4 - 2020-06-14

### Fixed
- Fixed a compatibility issue with Craft's own version of PrismJS.

## 1.0.3 - 2020-06-11

### Fixed
- Fixed a composer PSR-4 autoload deprecation warning.

## 1.0.2 - 2020-06-05

### Fixed
- Fixed a styling issue with tokens that was introduced in Craft 3.4.x.

## 1.0.1 - 2019-11-19

### Fixed
- Fixed an issue affecting PHP versions <= 7.1.

## 1.0.0 - 2019-06-26

### Added
- Initial release.
