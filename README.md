Welcome to the WPForce Logout repository on GitHub

## Documentation
* [WPForce Logout Documentation](https://miniplugins.com/how-to-use-wp-force-logout-plugin/)

## Support
This repository is not suitable for support. Please don't use our issue tracker for support requests. Support can take place through the [WordPress.org forum](https://wordpress.org/support/plugin/wp-force-logout/).

Support requests in issues on this repository will be closed on sight.

### Are you a Command Line freak?

There are WP-CLI commands available:

* `wp wpfl logout all` - force logout all users.
* `wp wpfl logout 14  54 info@example.com example123` - logout bulk users.
* `wp wpfl logout sanzeeb.ar@example.com` - logout specific user.

The passed argument can be User ID, Username, or User Email.

## Contributing Guidelines

Contribute wherever you can! For the best practices, follow the [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/).

## Free and PRO

One codebase builds both. Freemius strips the PRO code when it processes an upload:
`src/Pro/` and `assets/js/pro/` (listed in `@fs_premium_only` in `wp-force-logout.php`) and
the `can_use_premium_code__premium_only()` block in `includes/class-wp-force-logout.php`.
Keep PRO-only code in those places.

## Releasing

1. Bump the version in `wp-force-logout.php` (header), `WP_Force_Logout::$version` and
   `readme.txt` (Stable tag + changelog entry `= X.Y.Z - date =`).
2. Push a tag named after the version, e.g. `3.0.0`. The **Release** workflow then:
   - uploads the PRO zip to Freemius as *pending* and downloads the free build Freemius generates,
   - in the `production` environment, deploys the free build to wordpress.org SVN and releases
     the version on Freemius. Add required reviewers to the environment to approve this step.

Secrets: `FREEMIUS_DEV_ID`, `FREEMIUS_PUBLIC_KEY`, `FREEMIUS_SECRET_KEY`, `SVN_USERNAME`,
`SVN_PASSWORD` (the SVN password from your wordpress.org profile, not the account password).

To deploy to wordpress.org by hand, download the free zip from Freemius and run
`.github/scripts/wporg-deploy.sh [zip] [--dry-run]` (uses the SVN checkout at `~/wp-force-logout`).
