# Changelog

All notable changes to the STN-LABZ Threat API are documented here.

## Unreleased

### Added

- Standard STN-LABZ GitHub documentation layout.
- Architecture, configuration, installation, policy, contribution, and licensing documentation.
- Repository rules excluding secrets, operational threat data, and logs.

### Existing API baseline

- Authenticated `POST /threats` ingestion.
- Latest-first `/threats` and `/events` feeds.
- `/intel` aggregate analysis and blocklist output.
- `/patterns` dynamic pattern output.
- `/sl-feed` compact text output.
