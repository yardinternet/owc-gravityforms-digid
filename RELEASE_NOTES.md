# Release Notes

Functional release notes for stakeholders, focusing on user-facing changes and their impact.

---

## Table of Contents
- [v2.0.0](#v200)
- [v1.7.0](#v170)
- [v1.6.1](#v161)
- [v1.6.0](#v160)

---

## v2.0.0

### Summary
- **Compatibility**: codebase and dependencies to PHP 8.1 and above.
- **Compatability**: drop support for PHP ^7.
- **Update**: use env from its own helper file.
- **Update**: use Monolog v3 as opposed to Monolog MS-teams dependency.
- **Update**: JavaScript dependencies.
- **Update**: GitHub actions to newer versions.

### Functional Impact
- The codebase has now settled on PHP 8.1 and above, dropping support for PHP 7.x versions.
- Dependencies have been upgraded in line with modern PHP standards.
- The environment variables are resolved from an internal helper file to streamline configuration.
- Logging has been updated to use Monolog v3, removing the MS-teams dependency for more flexibility.
- JavaScript dependencies have been updated to their latest versions to ensure security and performance.

## v1.7.0

### Summary
- **Compatibility**: Added support for PHP 8.1.
- **Updates**: Updated NPM (Node Package Manager) dependencies.
- **Fix**: Multiple IDP (identity providers) support.
- **Fix**: Hidden honeypot field should not be required.

### Functional Impact
- Users can now run the plugin on servers using PHP 8.1 without compatibility issues.
- We've implemented a fix which allows the plugin to handle multiple identity providers for instance eIDAS and eHerkenning.
- A form with a single DigiD field will verify it DigiD is already authenticated, a honeypot (anti-spam) field would sometimes block this behaviour, this is now fixed.

## v1.6.1

### Summary
- **Fix**: URL parameter could be set as a boolean instead of a string.

### Functional Impact
- The plugin now correctly handles URL parameters, ensuring they are always treated as strings, preventing potential issues with boolean values.

## v1.6.0

### Summary
- **Added**: Remove the form submit button and auto submit the form when DigiD session is valid and DigiD is the only form field.
- **Added**: Standardized interfaces for handling IDP (identity provider) user data.

### Functional Impact
- A form with a single DigiD field will now automatically submit if the user is already authenticated, improving user experience by reducing the need for manual submission.
- The plugin now provides a standardized way to handle user data from different identity providers, making it easier to extend and maintain.

## Older releases

For older releases, please refer to the [CHANGELOG.md](CHANGELOG.md) file for detailed technical changes and updates.

