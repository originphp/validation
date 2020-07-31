# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [unreleased]

### Added

- Added `invalidate` to `ValidateTrait`

### Changed

- Changed `ValidateTrait::$validationError` to protected property from private to be more flexible
- Changed `ValidateTrait::validator()` to public property so that this can be called from the outside

## [1.3.0] - 2020-07-31

### Added

- Added `ValidateTrait`

### Fixed

- Fixed boolean validating string as boolean

## [1.2.0] - 2020-07-30

## Added

- Added `Validator` object to validate arrays of data (this is being separated from the Model thus allowing it be be completely reusable)
- Added `Validate::notEmpty`

## [1.1.0] - 2020-07-15

## Changed

- Changed `minLength` to work with non strings such as integers
- Changed `maxLength` to work with non strings such as integers

## [1.0.2] - 2020-07-15

### Fixed

- Fixed operators

## [1.0.0] - 2020-05-14

## [0.1.0] - 2019-12-13

This is a new component for the [OriginPHP framework](https://www.originphp.com/).