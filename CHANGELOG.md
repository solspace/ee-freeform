# Changelog

## Unreleased

### Fixed
- Fixed reCAPTCHA v3 submission handling so custom submit handlers wait for a fresh token, repeated clicks do not start overlapping token requests, and forms loaded after page initialization initialize correctly.
