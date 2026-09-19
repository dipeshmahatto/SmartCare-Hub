# Security Notes

SmartCare Hub is a portfolio/academic application, but the repository includes practical baseline protections:

- Passwords are stored with PHP `password_hash()` and verified with `password_verify()`.
- Existing legacy plaintext passwords are upgraded to hashes after a successful login.
- Password columns are widened to `VARCHAR(255)` in the security migration.
- Authentication queries and user-controlled writes use prepared statements.
- Authenticated sessions regenerate their session ID after login.
- Session cookies are HTTP-only, SameSite=Lax, and automatically use `Secure` when served over HTTPS.
- State-changing forms use CSRF tokens.
- Profile uploads validate MIME type, file size and authenticated role; filenames are server-controlled.
- Database credentials can be supplied through environment variables instead of being committed as secrets.

## Demo-only recovery limitation

The current password recovery flow verifies a phone number plus birth year. This is intentionally documented as a **demo flow** and should not be considered strong identity verification for a production healthcare system. A production deployment should use a time-limited email/SMS reset token, rate limiting and audit logging.

## Reporting a security issue

Do not publish sensitive credentials, real patient data or exploit details in a public issue. Use a private channel when adapting this project for real deployment.
