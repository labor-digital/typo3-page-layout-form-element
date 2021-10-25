# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [10.0.1-beta.1](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.0.1-beta.0...v10.0.1-beta.1) (2021-10-25)


### Bug Fixes

* **PageLayout:** use correct "uid" property instead of old contentId ([0e073be](https://github.com/labor-digital/typo3-page-layout-form-element/commit/0e073be675085f3f6e5232a5b63f4018bff4ce5a))
* remove unused "Dummy" class ([295f419](https://github.com/labor-digital/typo3-page-layout-form-element/commit/295f4190e7c6b8abf4c03d13721685e24d181090))

### [10.0.1-beta.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v9.2.5-beta.0...v10.0.1-beta.0) (2021-07-27)


### Features

* rewrite from ground up to match v10 requirements ([2305631](https://github.com/labor-digital/typo3-page-layout-form-element/commit/23056317fa5b62a6cc7a976a8c87f16791b4c295))

### [9.2.5-beta.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v9.2.4...v9.2.5-beta.0) (2021-07-16)

### [9.2.4](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v9.2.3...v9.2.4) (2020-09-29)


### Bug Fixes

* **FormPageLayoutContentResourceTransformer:** make code compatible with new frontend api version ([f1e14b4](https://github.com/labor-digital/typo3-page-layout-form-element/commit/f1e14b4fbd74fa2a856e73b7e612449e903b8cc3))

### [9.2.3](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v9.2.2...v9.2.3) (2020-08-13)


### Bug Fixes

* **PageLayoutFormElement:** generate correct "back" url for newly created record forms ([35a75bc](https://github.com/labor-digital/typo3-page-layout-form-element/commit/35a75bc2df8805c6ccdd0de902b4601d293c2b09))
* **PageTs:** remove the the backend layout from the list ([42eda5d](https://github.com/labor-digital/typo3-page-layout-form-element/commit/42eda5d5dd31a8d937a8dd69e97ae519e768b250))

### [9.2.2](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v9.2.1...v9.2.2) (2020-08-06)


### Bug Fixes

* **ExtendedTreeController:** fix visible content element doktypes in v9.5.20 ([b0c949b](https://github.com/labor-digital/typo3-page-layout-form-element/commit/b0c949b354cb6d967b87acb1a51e28d2e215ecc0))

### [9.2.1](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v9.2.0...v9.2.1) (2020-08-06)


### Bug Fixes

* **ExtendedTreeController:** make the class compatible with v9.5.20 ([13be236](https://github.com/labor-digital/typo3-page-layout-form-element/commit/13be236b55c12713497b0bdc49b003d596951cb9))

## [9.2.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v9.1.1...v9.2.0) (2020-07-21)


### Features

* implement search ([1df9c6d](https://github.com/labor-digital/typo3-page-layout-form-element/commit/1df9c6d072d6f4661d045143b05df68a158c3581))
* make more code type save + resolve deprecated dependencies ([ff0a1f7](https://github.com/labor-digital/typo3-page-layout-form-element/commit/ff0a1f74d03104838a4642102761234ca5c0e934))

### [9.1.1](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v9.1.0...v9.1.1) (2020-06-30)

## 9.1.0 (2020-06-30)


### Features

* initial public release ([c18acc9](https://github.com/labor-digital/typo3-page-layout-form-element/commit/c18acc93bde9f54d945382b2da2e7ec10ef8156e))

## 1.0.1 (2020-02-04)


### Bug Fixes

* make sure the "back to record" points to the correct url by storing the value in the session (9256c38)



# 1.0.0 (2020-02-03)


### Features

* make package compatible with newer frontend api version (1795c1f)


### BREAKING CHANGES

* raises required version of frontend api



# 0.6.0 (2020-01-07)


### Features

* **Resource:** change form page layout resource handling to apply to the latest version of the frontend api package (cbc2d6e)



# 0.5.0 (2019-12-09)


### Features

* **FormElement:** force the backend action execution (f73d897)



# 0.4.0 (2019-10-24)


### Bug Fixes

* **PagesOverride:** remove page layout doktype from the select list in the backend (537e874)


### Features

* force new version number (547bd06)



# 0.3.0 (2019-09-07)


### Bug Fixes

* **PageLayoutContent:** allow null as a page uid as well (5df3688)


### Features

* implement better api change: rename TableConfigInterface->configure to configureTable and make it static (d815198)



# 0.2.0 (2019-09-06)


### Bug Fixes

* **BackendEventHandler:** make sure the backend gui filter works for TYPO3 v7 as well (1c807f0)
* add additional workarounds to make sure we can render the page layout even int TYPO3 v7 (721f7de)
* **PageLayoutContent:** make sure that the result variable always exists (09c553c)
* make sure the hidden pages do not show up in the link creation browser (74a6ba5)
* make sure the viewhelper works in TYPO3 v7 as well (6a55d4a)


### Features

* add documentation and sql definition to form preset applier (d47656a)
* **FrontendApi:** add support for the frontend api by providing a resource controller and transformer for pageLayout contents (3275651)
* minor adjustments to keep up with the changes of better api (708c0ec)



## 0.1.1 (2019-08-06)



# 0.1.0 (2019-07-16)


### Features

* implement main functionality (2961774)



## 0.0.2 (2019-07-09)
