# Changelog

-   requires: WordPress 6.0.0
-   tested: WordPress 6.3.1

## v1.3.2

-   Fix: decrypt encapsulated esc_html, should be the other way around

## v1.3.1

-   Refactor: display decrypted value of DigiDField on entry list if hook 'owc_gravityforms_digid_use_value_bsn_decrypted' is used

## v1.3

-   Refactor: display encrypted value of DigiDField on entry list instead of decrypted

## v1.2.2

-   Fix: error messages text

## v1.2.1

-   Fix: encrypt fake session id

## v1.2.0

-   Add: improve validation when two IDPs are on the same form and a session is active
-   Chore: update readme and fix badge

## v1.1.9

-   Fix: sessionTTL

## v1.1.8

-   Add: inactivity timer

## v1.1.7

-   Chore: upgrade samlbase dependency due to twig/twig CVE-2022-39261

## v1.1.6

-   Fix: security warnings on babel/traverse and postcss dependencies
-   Chore: swap teams monologger package

## v1.1.5

-   Fix: add CSP only to acs routes.

## v1.1.4

-   Add: CSP to custom routes.

## v1.1.3

-   Chore: add better name for app.js and css to identify plugin.
-   Fix: unsafe eval in webpack and csp inline style.

## v1.1.2

-   Change: clean up logging to MS teams.

## v1.1.1

-   Change: lazy the session.
-   Fix: PHP tests.

## v1.1.0

-   Add: make sessions persistent across multiple forms.

## v1.0.19

-   Change: use getenv() instead of global $\_ENV.

## v1.0.18

-   Change: counter + logout button positioning.

## v1.0.17

-   Fix: counter export default class component.

## v1.0.16

-   Change: position of logout button.

## v1.0.15

-   Change: update npm to remove vulnerabilities for lodash & postcss.

## v1.0.14

### Fix

-   Fix: trying to access array offset on value of type null (notice).
-   Change: translate English sentence.
-   Change: rewrite namespaces for tests.

## v1.0.13

-   Fix: remove extra quote.

## v1.0.12

-   Change: styling and text after login using DigiD.

## v1.0.11

-   Fix: update npm package "ini". This package might have CVE-2020-7788.

## v1.0.10

-   Change: update outdated SAML dependency.

## v1.0.9

-   Change: better error handling.

## v1.0.8

-   Change: set secure and httpOnly cookie.
-   Change: refactor code.

## v1.0.7

-   Change: local storage to session storage.
-   Fix: duration of a resumed session.

## v1.0.6

-   Change: CSS in JS.

## v1.0.5

## v1.0.4

-   Change: JS to class instance.
-   Fix: collision with Gravity Forms live validator plugin.

## v1.0.3

-   Add: extra HTML output for DigiD field.

## v1.0.2

-   Fix: even better error handling messages.
-   Fix: HTML output for countdown.

## v1.0.1

-   Add: disabling notifications via .env key `MS_TEAMS_DISABLE_LOGGING=true`.
-   Add: better error handling messages.
-   Fix: refactor views

## v1.0.0

-   Add: DigiD login field
-   Add: settings page
