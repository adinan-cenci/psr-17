# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 2.1.1 - 2025-06-26
### Fixed
- [issue 17](https://github.com/adinan-cenci/psr-17/issues/17) - GET requests should have no body.

### Changed
- `ResponseFactory::response()` is now public.

---

## 2.1.0 - 2025-06-10
### Added
- [issue 15](https://github.com/adinan-cenci/psr-17/issues/15) - Added exceptions when dealing with malformed requests.

---

## 2.0.0 - 2025-06-04
### Fixed
- [issue 11](https://github.com/adinan-cenci/psr-17/issues/11) - PSR-12 compliance and documentation.
- [issue 13](https://github.com/adinan-cenci/psr-17/issues/13) - A bug when reading json/xml requests.

### Changed
- [issue 13](https://github.com/adinan-cenci/psr-17/issues/13) - Updated dependencies, increased unit-test coverage and improved readability.

---

## 1.0.8 - 2023-09-08
### Fixed
- [issue 9](https://github.com/adinan-cenci/psr-17/issues/9) - Messed up the previous patch.

---

## 1.0.7 - 2023-09-08
### Fixed
- [issue 7](https://github.com/adinan-cenci/psr-17/issues/7) - Exception thrown when calling `UriFactory::createFromGlobals()`.

---

## 1.0.6 - 2023-09-02
### Fixed
- [issue 5](https://github.com/adinan-cenci/psr-17/issues/5) - Error when parsing parsing empty variable from multi-part form data.

---

## 1.0.5 - 2023-08-13
### Fixed
- [issue 1](https://github.com/adinan-cenci/psr-17/issues/1) - Proper support for PUT and other http methods.
- [issue 3](https://github.com/adinan-cenci/psr-17/issues/3) - Uploaded files array structure now reflect input's name as [PSR-7 specifies](https://www.php-fig.org/psr/psr-7/#16-uploaded-files).

---

## 1.0.4 - 2023-01-31
### Fixed
- Fixed a case sensitive error preventing the `ServerRequestFactory` from
  figuring out the http method.
- Fixed a limitation preventing ServerRequestFactory from determining the
  content type.
- Added support for the HEAD http method.

---

## 1.0.3 - 2023-01-08
### Fixed
- Fixed a typo in the package's name in the `composer.json` file.

---

## 1.0.2 - 2023-01-08
### Fixed
- Added license information to the `composer.json` file.

---

## 1.0.1 - 2023-01-08
### Fixed
- Fixed an issue with the dependencies definitions in the composer.json
