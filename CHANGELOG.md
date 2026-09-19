# Changelog

## Portfolio Edition — 2026-09-20

### Experience
- Rebuilt the public site as a healthcare SaaS landing experience.
- Added a shared responsive design system.
- Rebuilt Patient, Doctor and Admin workspaces.
- Added a multi-step appointment booking wizard.
- Added persistent dark/light mode, toast feedback and micro-interactions.

### Appointment workflow
- Added `pending`, `confirmed`, `completed` and `cancelled` lifecycle states.
- Preserved cancelled records instead of deleting appointment history.
- Added Doctor confirmation and completion actions.

### Security
- Added password hashing with legacy plaintext auto-upgrade.
- Added prepared statements to authentication/registration/recovery paths.
- Added hardened session initialization and session ID regeneration.
- Added CSRF tokens to state-changing forms.
- Hardened image uploads with role, size and MIME validation.
- Added environment-based database configuration.

### Repository
- Added GitHub Actions PHP/JavaScript syntax checks.
- Added architecture and security documentation.
- Added migration guidance and portfolio-focused README.
