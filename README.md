# OWC GravityForms DigiD

![Tests passing](https://github.com/yardinternet/owc-gravityforms-digid/actions/workflows/run-tests.yml/badge.svg "Tests passing")

This plugin provides a GravityForms field to retrieve a KvK number from eHerkenning.

## Local development

DigiD is only able to connect with valid PKi certificates, for local development you can fake a session by setting the below environment variable.

```env
DIGID_FAKE_SESSION='012345678'
```

## Creating a new release

1. Update the version in `plugin.php` x2 (plugin version and metadata)
2. Ensure that `CHANGELOG.md` and `RELEASE_NOTES.md` are updated with the latest changes
3. Draft a new release
