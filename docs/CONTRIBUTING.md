# Contributing to STN-LABZ Threat API

Contributions must preserve API compatibility, protect deployment secrets, and avoid committing operational threat data.

## Workflow

1. Create a focused branch.
2. Keep changes small and document behavior changes.
3. Validate all affected endpoints and error paths.
4. Confirm secrets, logs, and runtime JSON are not staged.
5. Update `docs/CHANGELOG.md` for notable changes.

## Engineering expectations

- Use strict PHP types where practical.
- Return valid, explicit HTTP status codes and content types.
- Validate untrusted request data before storage or processing.
- Preserve backward compatibility for documented endpoints unless a versioned migration is approved.
- Do not weaken API-key comparison, HTTPS expectations, filesystem controls, or logging safeguards.
