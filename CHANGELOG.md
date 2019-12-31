# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.4.0 - 2018-05-07

### Added

- [zfcampus/zf-content-negotiation#103](https://github.com/zfcampus/zf-content-negotiation/pull/103) adds support for PHP 7.2.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-content-negotiation#103](https://github.com/zfcampus/zf-content-negotiation/pull/103) removes support for HHVM.

### Fixed

- [zfcampus/zf-content-negotiation#101](https://github.com/zfcampus/zf-content-negotiation/pull/101) fixes how `ContentNegotiationsOptions` handles dash-separated keys,
  ensuring they are always translated to underscore_separated; this fixes issues whereby such
  keys were ignored during execution.

## 1.3.3 - 2017-11-21

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-content-negotiation#100](https://github.com/zfcampus/zf-content-negotiation/pull/100) fixes an
  issue introduced in 1.3.2 whereby the `RequestFactory` was updated to no
  longer depend on laminas-console. Unfortunately, many testing strategies relied
  on laminas-console's ability to override the SAPI detection in order to test HTTP
  request lifecycles. This release now does detection for the presence of the
  laminas-console library, and, if present, uses that for determining whether or
  not the request is console-based.

## 1.3.2 - 2017-11-15

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-content-negotiation#97](https://github.com/zfcampus/zf-content-negotiation/pull/97) fixes an
  issue in the `ContentTypeListener` whereby empty content was leading to an
  uninitialized string offset notice .

## 1.3.1 - 2017-11-14

### Added

- Nothing.

### Changed

- [zfcampus/zf-content-negotiation#86](https://github.com/zfcampus/zf-content-negotiation/pull/86) makes
  laminas-console a suggested dependency.

- [zfcampus/zf-content-negotiation#88](https://github.com/zfcampus/zf-content-negotiation/pull/88) updates how
  the `ContentTypeListener` decides how to parse incoming content when no
  `Content-Type` header is present. If the content begins with a `{` or `[`
  character, it will now parse it as JSON instead of as form data.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-content-negotiation#88](https://github.com/zfcampus/zf-content-negotiation/pull/88) adds a
  missing import statement to the `RenameUploadFilterFactory` class definition.

## 1.3.0 - 2016-10-11

### Added

- [zfcampus/zf-content-negotiation#81](https://github.com/zfcampus/zf-content-negotiation/pull/81) adds a new
  listener, `HttpMethodListener`. The listener is enabled by toggling the
  `api-tools-content-negotiation.x_http_method_override_enabled` flag, and providing a
  map of request method/list of override request methods in the
  `api-tools-content-negotiation.http_override_methods` configuration:

  ```php
  'api-tools-content-negotiation' => [
      'x_http_method_override_enabled' => true,
      'http_override_methods' => [
          'GET' => [
              'HEAD',
              'PATCH',
              'POST',
              'PUT',
              'DELETE',
          ],
      ],
  ],
  ```

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

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
