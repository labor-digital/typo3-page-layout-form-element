# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [10.6.2](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.6.1...v10.6.2) (2022-06-22)


### Bug Fixes

* add cache tags when rendering the page content ([6b21eac](https://github.com/labor-digital/typo3-page-layout-form-element/commit/6b21eacfcce129c0ee2ce8688d1ebf45dcb15cc4))

### [10.6.1](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.6.0...v10.6.1) (2022-06-21)


### Bug Fixes

* **Rewrite:** apply rewrite for content element GUI correctly ([1b8293b](https://github.com/labor-digital/typo3-page-layout-form-element/commit/1b8293b946d8d701cc8ea09b51cdc40faa586e9a))

## [10.6.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.5.0...v10.6.0) (2022-04-29)


### Features

* **Search:** adjust record indexer to match new t3sai requirements ([bf21175](https://github.com/labor-digital/typo3-page-layout-form-element/commit/bf21175585b4625c3920a3c9b6c682c7625da3a1))

## [10.5.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.4.5...v10.5.0) (2022-04-25)


### Features

* reenable T3sai indexer trait for v10 ([a3c0b97](https://github.com/labor-digital/typo3-page-layout-form-element/commit/a3c0b973acfebaa8e0976d8a924a2c3cdffd2046))

### [10.4.5](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.4.4...v10.4.5) (2022-03-02)


### Bug Fixes

* **IframeActions:** add compatibility layer to handle element browser actions in the iframe mode ([cfaf9c7](https://github.com/labor-digital/typo3-page-layout-form-element/commit/cfaf9c7aaae73b1fe16b92483166098838fca3a7))

### [10.4.4](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.4.3...v10.4.4) (2022-01-31)


### Bug Fixes

* **Override:** avoid issues when generating the AbstractTreeView override ([b7d29ef](https://github.com/labor-digital/typo3-page-layout-form-element/commit/b7d29ef61617a762e4b55963c9ff1e1279227370))

### [10.4.3](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.4.2...v10.4.3) (2022-01-31)


### Bug Fixes

* **PageTree:** ensure that AbstractTreeView gets registered correctly ([0877175](https://github.com/labor-digital/typo3-page-layout-form-element/commit/087717502b817cbf055f4ee6af7a5494079feff5))

### [10.4.2](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.4.1...v10.4.2) (2022-01-21)


### Bug Fixes

* remove multiple access points to the content page to avoid "editor havoc" ([74107e9](https://github.com/labor-digital/typo3-page-layout-form-element/commit/74107e9787783ac4043e1b09a20286204137c164))

### [10.4.1](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.4.0...v10.4.1) (2022-01-21)


### Bug Fixes

* **JavaScript:** fix inline file relations in iframe editing mode ([46f8786](https://github.com/labor-digital/typo3-page-layout-form-element/commit/46f8786fc3921979c521ecafa7aa412b9543129b))
* **Translation:** update german translation labels ([8747a48](https://github.com/labor-digital/typo3-page-layout-form-element/commit/8747a48e8eab15af8af5cc8539b0cdd362cc026c))

## [10.4.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.3.0...v10.4.0) (2021-12-10)


### Features

* **PageLayoutRepository:** inherit storage page permissions for content pages ([70691e4](https://github.com/labor-digital/typo3-page-layout-form-element/commit/70691e4475b3804ba3a563c745089fce5ed7bcae))
* **PageService:** use "soft" forcing wherever possible ([c38d204](https://github.com/labor-digital/typo3-page-layout-form-element/commit/c38d204c45d1a8e9c24c4d2538dd0e8acdde7b38))

## [10.3.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.2.0...v10.3.0) (2021-10-27)


### Features

* **IframeActions:** ensure that child forms are saved when parent is saved/closed ([ba3f01d](https://github.com/labor-digital/typo3-page-layout-form-element/commit/ba3f01d0b579a4adde04612bab6f4aab63a7306f))


### Bug Fixes

* ensure the hidden form field gets updated when a child page is added/deleted ([6f7076c](https://github.com/labor-digital/typo3-page-layout-form-element/commit/6f7076ca557315accb29d0c22d5a25f16275cc2e))
* **FieldPreset:** set default value of field to "0" ([1082f86](https://github.com/labor-digital/typo3-page-layout-form-element/commit/1082f86757c9fef14218b6bbbfc1a399c83eb1cd))
* **IframeActions:** remove console spam ([0b9b842](https://github.com/labor-digital/typo3-page-layout-form-element/commit/0b9b84289fb33c03c538a0533612a47e21d6722d))

## [10.2.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.0.1-beta.1...v10.2.0) (2021-10-26)

### [10.1.1](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.1.0...v10.1.1) (2021-10-11)


### Bug Fixes

* **PageLayoutFormElement:** ensure the db column has "0" as default value ([9558f86](https://github.com/labor-digital/typo3-page-layout-form-element/commit/9558f86864075904e70277043c051131f7b1d9b8))

## [10.1.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.0.2...v10.1.0) (2021-10-05)


### Features

* introduce page parent field upgrade wizard ([082a8ac](https://github.com/labor-digital/typo3-page-layout-form-element/commit/082a8acca8e97d960326eb9d9a22b838a9b3e10e))

### [10.0.2](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.0.1-beta.0...v10.0.2) (2021-09-03)

### [10.1.1](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.1.0...v10.1.1) (2021-10-11)


### Bug Fixes

* **PageLayoutFormElement:** ensure the db column has "0" as default value ([9558f86](https://github.com/labor-digital/typo3-page-layout-form-element/commit/9558f86864075904e70277043c051131f7b1d9b8))

## [10.1.0](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.0.2...v10.1.0) (2021-10-05)


### Features

* introduce page parent field upgrade wizard ([082a8ac](https://github.com/labor-digital/typo3-page-layout-form-element/commit/082a8acca8e97d960326eb9d9a22b838a9b3e10e))

### [10.0.2](https://github.com/labor-digital/typo3-page-layout-form-element/compare/v10.0.1-beta.0...v10.0.2) (2021-09-03)

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
