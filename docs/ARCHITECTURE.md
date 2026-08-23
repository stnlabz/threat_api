# Architecture

## Overview

The STN-LABZ Threat API is a small PHP service designed for Apache-compatible hosting. `index.php` provides routing and response generation, while `processor.php` contains threat-type processing hooks.

```text
Sentinel clients
      |
      | POST /threats + X-API-KEY
      v
 Apache / .htaccess
      |
      v
   index.php ---- processor.php
      |
      +---- data/threats.json
      +---- logs/
      |
      +---- GET feeds and analysis
```

## Request flow

Apache rewrites supported requests to `index.php`. The router normalizes `/api` and `/v1` prefixes, dispatches by HTTP method and path, and returns JSON except for the root HTML page and `/sl-feed` text output.

Authenticated ingestion compares the `X-API-KEY` request header with `API_SECRET`, appends a normalized event to the threat store, and returns a status response. Read endpoints load the same store and produce latest-first results.

## Storage boundary

The current implementation uses JSON files for operational state. Runtime data, logs, and deployment secrets are excluded from source control. Deployments must provide writable `data/` and `logs/` directories.

## Security boundary

Only `POST /threats` is authenticated in the current implementation. Read endpoints are publicly accessible unless restricted by the hosting environment. CORS is currently open to all origins and should be narrowed when consumers are known.
