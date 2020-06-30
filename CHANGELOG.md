# Change Log

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

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
