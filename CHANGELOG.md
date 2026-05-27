# Changelog

## [v.2.2.0] - 2025-05-27

-   Fix: ensure bsn is cast to string before encryption
-   Feat: add additional certificate field for the trustchain
-   Chore: add github release workflow

## [v2.1.0] - 2025-05-04

-   Feat: Add WP action to capture logs

## [v2.0.0] - 2025-10-21

-   Refactor: update codebase to PHP 8 standards
-   Refactor: drop support for PHP 7
-   Change: use env from its own helper file
-   Change: use Monolog v3 as opposed to Monolog MS-teams dependency
-   Update: JavaScript dependencies
-   Update: GitHub actions to newer versions

## [v1.7.0] - 2025-02-27

-   Feat: Add PHP 8.1 compatibility

## [v1.6.1] - 2024-12-24

-   Fix: string could be set as a boolean

## [v1.6.0] - 2024-11-06

-   Feat: add IDP userdata

## [v1.5.3] - 2024-08-23

-   Fix: add CSP header to logout url

## [v1.5.2] - 2024-08-14

-   Fix: use own copy of SAMLBase

## [v1.5.1] - 2024-04-23

-   Fix: add CSP header to keep-alive url

## [v1.5.0] - 2024-03-15

-   Feat: add conditional logic for sessions from different IdP's

## [v1.4.4] - 2024-02-28

-   Feat: automatic logout on pages without Gravity Forms
-   Feat: option to enable automatic logout of WP user when session ends

## [v1.4.3] - 2024-02-22

-   Feat: automatic logout on pages without Gravity Forms
-   Feat: option to enable automatic logout of WP user when session ends

## [v1.4.2] - 2024-01-25

-   Add: Use GFFormsModel::get_current_page_url to determine resume_link

## [v1.4.1] - 2024-01-15

-   Feat: supplement BSN on entry list and entry detail when required is not met
-   Feat: the decrypt helper function might return a boolean value, deviating from the expected return type of the function
-   Feat: validate values after decrypting, when decrypting fails use the initial value

## [v1.4.0] - 2024-01-08

-   Feat: use shared Aura session via a filter hook provided by the Yard Shared Aura Session Instance Plugin
-   Refactor: rename CountDown JS class because of conflicts
-   Refactor: remove unnecessary typehinting and return types

## [v1.3.2] - 2023-12-21

-   Fix: decrypt encapsulated esc_html, should be the other way around

## [v1.3.1] - 2023-12-21

-   Refactor: display decrypted value of DigiDField on entry list if hook 'owc_gravityforms_digid_use_value_bsn_decrypted' is used

## [v1.3.0] - 2023-12-19

-   Refactor: display encrypted value of DigiDField on entry list instead of decrypted

## [v1.2.2] - 2023-12-11

-   Fix: error messages text

## [v1.2.1] - 2023-12-11

-   Fix: encrypt fake session id

## [v1.2.0] - 2023-11-24

-   Add: improve validation when two IDPs are on the same form and a session is active
-   Chore: update readme and fix badge

## [v1.1.9] - 2023-11-21

-   Fix: sessionTTL

## [v1.1.8] - 2023-11-21

-   Add: inactivity timer

## [v1.1.7] - 2023-10-24

-   Chore: upgrade samlbase dependency due to twig/twig CVE-2022-39261

## [v1.1.6] - 2023-10-16

-   Fix: security warnings on babel/traverse and postcss dependencies
-   Chore: swap teams monologger package

## [v1.1.5] - 2023-09-06

-   Fix: add CSP only to acs routes.

## [v1.1.4] - 2023-09-06

-   Add: CSP to custom routes.

## [v1.1.3] - 2023-08-28

-   Chore: add better name for app.js and css to identify plugin.
-   Fix: unsafe eval in webpack and csp inline style.

## [v1.1.2] - 2023-07-17

-   Change: clean up logging to MS teams.

## [v1.1.1] - 2023-07-10

-   Change: lazy the session.
-   Fix: PHP tests.

## [v1.1.0] - 2023-03-17

-   Add: make sessions persistent across multiple forms.

## [v1.0.19] - 2022-01-05

-   Change: use getenv() instead of global $\_ENV.

## [v1.0.18] - 2021-12-31

-   Change: counter + logout button positioning.

## [v1.0.17] - 2021-12-31

-   Fix: counter export default class component.

## [v1.0.16] - 2021-12-10

-   Change: position of logout button.

## [v1.0.15] - 2021-05-21

-   Change: update npm to remove vulnerabilities for lodash & postcss.

## [v1.0.14] - 2021-05-05

### Fix

-   Fix: trying to access array offset on value of type null (notice).
-   Change: translate English sentence.
-   Change: rewrite namespaces for tests.

## [v1.0.13] - 2021-04-09

-   Fix: remove extra quote.

## [v1.0.12] - 2021-04-01

-   Change: styling and text after login using DigiD.

## [v1.0.11] - 2021-02-12

-   Fix: update npm package "ini". This package might have CVE-2020-7788.

## [v1.0.10] - 2020-09-30

-   Change: update outdated SAML dependency.

## [v1.0.9] - 2020-08-14

-   Change: better error handling.

## [v1.0.8] - 2020-07-15

-   Change: set secure and httpOnly cookie.
-   Change: refactor code.

## [v1.0.7] - 2020-06-04

-   Change: local storage to session storage.
-   Fix: duration of a resumed session.

## [v1.0.6] - 2020-06-03

-   Change: CSS in JS.

## [v1.0.5] - 2020-05-30

## [v1.0.4] - 2020-05-30

-   Change: JS to class instance.
-   Fix: collision with Gravity Forms live validator plugin.

## [v1.0.3] - 2020-05-27

-   Add: extra HTML output for DigiD field.

## [v1.0.2] - 2020-05-13

-   Fix: even better error handling messages.
-   Fix: HTML output for countdown.

## [v1.0.1] - 2020-05-06

-   Add: disabling notifications via .env key `MS_TEAMS_DISABLE_LOGGING=true`.
-   Add: better error handling messages.
-   Fix: refactor views

## [v1.0.0] - 2020-04-22

-   Add: DigiD login field
-   Add: settings page
