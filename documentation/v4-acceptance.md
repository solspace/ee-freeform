# Freeform 4 acceptance status

This records checks for the `4.0.0-alpha.1` draft. Passing script checks do not substitute for a running ExpressionEngine 7 site.

| Area | Current result | Evidence or next check |
| --- | --- | --- |
| PHP 8.5 dependency APIs, field conversions and transactional saves, cleanup throttle, CP output escaping | Passed locally on PHP 8.5.8 | `scripts/check-php.php`, `check-field-types.php`, `check-field-type-save.php`, `check-cleanup.php`, `check-cp-escaping.php` |
| PHP 8.2 and 8.5 automated checks | Passed CI | [Freeform 4 checks, run 2](https://github.com/solspace/ee-freeform/actions/runs/37097776144): platform requirements, syntax, and all five PHP check scripts. PHP 8.3 and 8.4 are being added to the same matrix. |
| PHP syntax | Passed locally on PHP 8.5.8 for 349 add-on files | `php -l` over `src/freeform_next`, excluding bundled vendor |
| Release package | Passed locally and in CI | `pnpm run build`; CI also installs Composer dependencies, verifies ZIP integrity and required files, and loads new classes from the archive |
| EE 7 install and upgrade | Needs an EE 7 test site with a database | Install the ZIP on a clean site; upgrade a copy of a Freeform 3 site and verify its existing forms and submissions |
| Builder in the EE control panel | Needs a test site | Add pages and fields, edit settings, undo and redo, save, reload, and check keyboard use and narrow viewport layout |
| Form submission, Spam, Quick Export | Needs a test site | Submit a valid and a spam form; inspect both lists and details, filter, change columns, export CSV/JSON, and verify escaping of submitted text |
| CRM and mailing list failures | Needs a test site and disposable integrations | Trigger a failing integration, confirm the submission completes and the relevant Freeform log records context |

Do not mark the live rows as passed until their behavior is observed in EE. A disposable site and integration credentials are necessary for those checks.
