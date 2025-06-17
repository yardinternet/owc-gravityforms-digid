# OWC GravityForms DigiD

![Tests passing](https://github.com/yardinternet/owc-gravityforms-digid/actions/workflows/run-tests.yml/badge.svg "Tests passing")

This plugin provides a GravityForms field to retrieve a BSN (social security) number from DigiD.

## Local development

DigiD is only able to connect with valid PKi certificates, for local development you can fake a session by setting the below environment variable.

```env
DIGID_FAKE_SESSION='012345678'
```

## Logging

Enable logging to monitor errors during communication with DigiD.
Make sure to set WP_DEBUG to true in your wp-config.php to enable logging.
Be cautious: the generated log file is stored in the publicly accessible upload directoy and can include sensitive information.

- Logs are written daily to `gfdigid-log{-date}.json` in the uploads directory.
- A rotating file handler keeps up to 7 log files by default, deleting the oldest as needed.
- You can change the maximum number of log files using the filter described below.

## Hooks

### Change the maximum number of log files

Use the following filter to alter the rotating file handler's max files setting:

```php
apply_filters('owc_gravityforms_digid_rotating_filer_handler_max_files', GF_DIGID_LOGGER_DEFAULT_MAX_FILES)
```

### Intercept exceptions for custom handling

You can intercept exceptions caught by the plugin for additional processing or custom logging using this filter:

```php
apply_filters('owc_gravityforms_digid_exception_intercept', $exception, $method)
```

The `$exception` parameter contains the caught exception object.

## Creating a new release

1. Update the version in `plugin.php` x2 (plugin version and metadata)
2. Ensure that `CHANGELOG.md` and `RELEASE_NOTES.md` are updated with the latest changes
3. Draft a new release
