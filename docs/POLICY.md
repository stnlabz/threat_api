# Project Policy

## Purpose

The STN-LABZ Threat API receives and distributes security-event information for approved STN-LABZ systems. It is not a general-purpose public event collector.

## Data handling

- Treat IP addresses, request details, threat records, logs, and derived blocklists as operational security data.
- Collect only data required for threat detection, analysis, and response.
- Do not commit live threat data or logs to the source repository.
- Restrict production storage and read endpoints according to operational need.
- Establish retention, rotation, and deletion rules in the deployment environment.

## Access and authentication

Write access requires an approved API key. Secrets must be unique, strong, deployment-local, transmitted only over HTTPS, and rotated when exposure is suspected.

Public read access is a deployment decision, not an assumption. Hosting controls must restrict feeds that contain non-public operational information.

## Secure operation

- Production error details must not be displayed to clients.
- CORS should permit only approved consumers when feasible.
- Input must be validated and output safely encoded.
- Runtime directories must use least-privilege filesystem permissions.
- Failures must return explicit errors without silently accepting or discarding security events.

## Repository boundary

Source code and sanitized documentation belong in Git. Credentials, production configuration, runtime JSON, logs, and locally generated operational artifacts remain outside Git unless explicitly approved as controlled evidence.
