Welcome to the WPForce Logout repository on GitHub

## Documentation
* [WPForce Logout Documentation](http://sanjeebaryal.com.np/force-user-to-logout-with-wpforce-logout-plugin/)

## Support
This repository is not suitable for support. Please don't use our issue tracker for support requests. Support can take place through the [WordPress.org forum](https://wordpress.org/support/plugin/wp-force-logout/).

Support requests in issues on this repository will be closed on sight.

## Development Guidelines

1. Follow the [WordPress Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/).

To simplify the process, 

* Install [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).  It’s actually 2 separate scripts.

	phpcs – detects violations of a defined coding standard
	phpcbf – automatically fixes coding standard violations

* Install the [WordPress Coding Standards Sniffs](https://github.com/WordPress/WordPress-Coding-Standards) somewhere in your PC(remember the path). 

    ````git clone -b master https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards.git wpcs````

* Configure phpcs to use the WordPress Coding Standards sniffs 

    ````phpcs --config-set installed_paths your_path/wpcs````

This will run a WordPress Coding Standard check on a pre-commit hook using [husky](https://github.com/typicode/husky).

You can (don't do without reason) skip pre-commit hook with --no-verify. Example: ````git commit -m "Commit message" --no-verify````

2. Use LF line endings in code editor. Use [EditorConfig](https://editorconfig.org/) if your editor supports it so that indentation, line endings and other settings are auto configured.

3. When committing, reference your issue number (#1234) and include a note about the fix.
