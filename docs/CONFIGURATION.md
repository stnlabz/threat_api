# Configuration

## Required configuration

Create `config/config.php` locally and define the ingestion secret:

```php
<?php
declare(strict_types=1);

const API_SECRET = 'replace-with-a-strong-secret';
```

The real file is deployment-local and excluded from Git. Never commit production credentials.

## Web server

The project expects Apache with URL rewriting enabled and `.htaccess` overrides permitted. The document root should point to the repository deployment directory.

## Runtime directories

PHP must be able to read and write:

- `data/` for the JSON threat store
- `logs/` for processor output and diagnostics

Initialize required JSON files with an empty array (`[]`) when no deployment data exists.

## Production settings

Disable displayed PHP errors in production, configure server-side error logging, serve the API over HTTPS, restrict filesystem permissions, rotate the API secret, and narrow CORS policy to approved consumers.
