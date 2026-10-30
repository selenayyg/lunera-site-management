# Security notice

This is an educational application, not production-ready software. Do not deploy publicly without a full review of authentication, authorization, CSRF protection, input validation and third-party dependencies.

The original archive contained a hard-coded SMTP application password. That password must be revoked at the provider even though it has been removed from this package.

Do not commit real database exports, `.env` files, credentials or personal records.
