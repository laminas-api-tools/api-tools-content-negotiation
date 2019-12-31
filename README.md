Laminas Content Negotiation
======================

[![Build Status](https://travis-ci.org/laminas-api-tools/api-tools-content-negotiation.png?branch=master)](https://travis-ci.org/laminas-api-tools/api-tools-content-negotiation)
[![Coverage Status](https://coveralls.io/repos/laminas-api-tools/api-tools-content-negotation/badge.png?branch=master)](https://coveralls.io/r/laminas-api-tools/api-tools-content-negotation)

Module for automating content negotiation tasks within a Laminas
application.

Allows the following:

- Mapping Accept header mediatypes to specific view model types, and
  automatically casting controller results to view models.
- Defining Accept header mediatype whitelists; requests with Accept mediatypes
  that fall outside the whitelist will be immediately rejected with a 406 "Not
  Acceptable" response.
- Defining Content-Type header mediatype whitelists; requests sending content
  bodies with Content-Type mediatypes that fall outside the whitelist will be
  immediately rejected with a 415 "Unsupported Media Type" response.


Installation
------------

You can install using:

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
```
