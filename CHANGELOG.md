# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## Unreleased
### Added
- Add a history chart to HTML output, displaying quality issues over time.

### Changed
- Each third party tool generates its own `AnalysisResult`

## 0.5.1 - 2015-08-19
### Fixed
- It was not generating HTML reports in the working directory.

## 0.5.0 - 2015-08-17
### Added
- Add `html` report (as an output format)
- Add `json`, `xml` and `csv` output formats
- Add `--format` / `-f` argument to change output format
- Add `--version` / `-v` argument to display current version
- Add `--ignore` / `-i` argument to exclude directories from the analysis
- Add code coverage and Code Climate badges
- Add change log

### Fixed
- If no target path is informed, use `.`

## 0.4.0 - 2015-08-07
### Added
- Output report beautifully as text

### Changed
- Reducing third party results into a single report

## 0.3.0 - 2015-08-07
### Changed
- Extract classes for integrations of third-party tools

## 0.2.0 - 2015-08-07
### Changed
- Running third party tools through PHP Hound Command class

## 0.1.0 - 2015-08-07
### Added
- Script for running PHPCS, PHPMD and PHPCPD one after another
