# OWC GravityForms DigiD

![Build status](https://github.com/yardinternet/owc-gravityforms-digid/workflows/CI/badge.svg "build status")

This plugin provides a GravityForm field allowing to retrieve a BSN (social security number) from DigiD.

## Local development

DigiD is only able to connect with valid PKi certificates, for local development you can fake a session by setting the below environment variable.

```env
DIGID_FAKE_SESSION='012345678'
```
