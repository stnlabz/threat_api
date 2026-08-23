# STN-LABZ Threat API

![Platform](https://img.shields.io/badge/Platform-Apache%20%2B%20PHP-blue)
![Language](https://img.shields.io/badge/Language-PHP-blue)
![Status](https://img.shields.io/badge/Status-Active%20Development-orange)

**Threat ingestion and intelligence feed API for STN-LABZ Sentinel.**

The service accepts authenticated threat reports, stores operational events, and exposes threat, event, intelligence, pattern, and Second Life feed endpoints.

## Endpoints

- `GET /threats` — latest-first raw threat feed
- `GET /events` — legacy alias for the threat feed
- `GET /intel` — aggregate statistics, live analysis, patterns, and blocklist data
- `GET /patterns` — dynamically observed firewall patterns
- `GET /sl-feed` — compact text feed for Second Life consumers
- `POST /threats` — authenticated ingestion using `X-API-KEY`

## Documentation

- [Architecture](docs/ARCHITECTURE.md)
- [Configuration](docs/CONFIGURATION.md)
- [Installation](docs/INSTALL.md)
- [Policy](docs/POLICY.md)
- [Contributing](docs/CONTRIBUTING.md)
- [Changelog](docs/CHANGELOG.md)
- **License:** Proprietary — Copyright © 2026 STN-LABZ. All rights reserved.
