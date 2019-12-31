# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.2.2 - 2016-07-27

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-content-negotiation#75](https://github.com/zfcampus/zf-content-negotiation/pull/75) updates the
  `JsonModel` to test discovered `Laminas\ApiTools\Hal\Entity` instances for a `getEntity()`
  method; if found, that method will be used to pull the entity data, but if
  not, property overloading to its `$entity` property will be used instead. This
  change ensures the component works with versions of api-tools-hal prior to 1.4.

## 1.2.1 - 2016-07-07

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Updates the `RequestFactory` to remove the `implements FactoryInterface`
  declaration (as it does not, and this was unable to resolve to a valid
  interface).

## 1.2.0 - 2016-07-07

### Added

- [zfcampus/zf-content-negotiation#71](https://github.com/zfcampus/zf-content-negotiation/pull/71) and
  [zfcampus/zf-content-negotiation#73](https://github.com/zfcampus/zf-content-negotiation/pull/73) provide
  support for v3 releases of the various Laminas components on which it
  depends, including laminas-eventmanager, laminas-json, laminas-mvc,
  laminas-servicemanager, and laminas-stdlib; all code continues to work with v2
  releases as well.
- [zfcampus/zf-content-negotiation#70](https://github.com/zfcampus/zf-content-negotiation/pull/70) adds support
  for PHP 7.

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-content-negotiation#70](https://github.com/zfcampus/zf-content-negotiation/pull/70) removes
  support for PHP 5.5.

### Fixed

- [zfcampus/zf-content-negotiation#72](https://github.com/zfcampus/zf-content-negotiation/pull/72) fixes a
  situation with the `RenameUpload` filter and `UploadFile` validator overrides
  whereby they triggered cyclic alias detection in laminas-servicemanager.

## 1.1.2 - 2016-05-26

### Added

- [zfcampus/zf-content-negotiation#50](https://github.com/zfcampus/zf-content-negotiation/pull/50) adds support
  for parsing `application/hal+json` bodies; `_embedded` properties are now
  merged with the top-level object following parsing.
- [zfcampus/zf-content-negotiation#66](https://github.com/zfcampus/zf-content-negotiation/pull/66) adds suport
  in the `ContentTypeFilterListener` to allow for request bodies to be objects
  that are castable to strings, such as occurs when using laminas-psr7bridge to
  convert from PSR-7 to laminas-http request instances (the message body is then a
  `StreamInterface` implementation, which may be cast to a string).

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-content-negotiation#68](https://github.com/zfcampus/zf-content-negotiation/pull/68) fixes
  parsing of urlencoded data within PUT requests.
- [zfcampus/zf-content-negotiation#52](https://github.com/zfcampus/zf-content-negotiation/pull/52) updates the
  `ContentTypeListener` to raise an error for non-object/non-array JSON payloads.
- [zfcampus/zf-content-negotiation#58](https://github.com/zfcampus/zf-content-negotiation/pull/58) updates the
  `AcceptFilterListener` to validate payloads without an `Accept` header.
- [zfcampus/zf-content-negotiation#63](https://github.com/zfcampus/zf-content-negotiation/pull/63) fixes the
  `ContentTypeListener` behavior when the request body does not contain a MIME
  boundary; the method now catches the exception and returns a 400 response.
