# Installation

## Requirements

- PHP 8.x recommended
- Apache HTTP Server
- `mod_rewrite`
- HTTPS for production deployment

## Setup

1. Clone the repository into the intended web directory.
2. Create `config/config.php` as described in [Configuration](CONFIGURATION.md).
3. Create writable `data/` and `logs/` directories.
4. Initialize `data/threats.json` and `data/intell.json` with `[]` if required by the deployment.
5. Enable Apache rewriting and allow the included `.htaccess` rules.
6. Point the virtual host or document root at the project directory.
7. Disable displayed PHP errors before production use.

## Verification

Confirm that the root page loads, `GET /intel` returns JSON, and an authenticated `POST /threats` succeeds. Verify that an invalid API key returns HTTP 401 and that runtime files remain untracked by Git.
