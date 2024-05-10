=== WPForce Logout - WordPress User Login Logout Management Plugin ===
Contributors: sanzeeb3
Tags: logout, force, online status, last seen, last login 
Requires at least: 4.0
Tested up to: 6.5
Requires PHP: 5.6
Stable tag: 2.0.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Forcefully log out users from your WordPress site, manage online status, and track last login activity.

== Description ==

WPForce Logout allows administrators to log out all or selected users with a single click, enhancing the security of user accounts. It helps protect against brute force attacks and allows you to manage compromised accounts by forcing logout. 

If you need to work on your WordPress website without any users being logged in, this plugin allows you to force all user accounts to be logged out. Additionally, if you suspect that your WordPress site is hacked, forcing logout will help you secure your site. This plugin is also useful for membership or pay-per-view sites to prevent password sharing among users.

You can easily view online/offline users, last login activity from the users tab.

### Features:
- Force Logout All Users
- Logout Specific User(s)
- Bulk Logout Users
- View Online Users
- Last Login Activity Tracking
- Well Documented
- Translation Ready

### Extended Features:
- Idle User Logout
- Auto logout on browser close
- Session Expiration
- Logout redirect

### WP-CLI Commands:
- `wp wpfl logout all` - Force logout all users.
- `wp wpfl logout 14 54 info@example.com example123` - Bulk logout users.
- `wp wpfl logout sanzeeb.ar@example.com` - Logout specific user.

The passed argument can be User ID, Username, or User Email.

For more details, refer to the [documentation](https://sanjeebaryal.com.np/force-user-to-logout-with-wpforce-logout-plugin/).

[Contribute on GitHub Repository](https://github.com/sanzeeb3/wp-force-logout)

== Frequently Asked Questions ==

= If I forcefully log out users, can they log in again? =
Yes, they will be able to log in with correct credentials.

= Can I contribute? =
Yes, you can! Join in on the [GitHub repository](https://github.com/sanzeeb3/wp-force-logout).

== Screenshots ==

1. Users Status

== Changelog ==

= 2.0.3 - 05/10/2024 =
* Tweak - improvment in auto logout on browser close functionality

= 2.0.2 - 04/26/2024 =
* Fix - Case sensitive issue on filename

= 2.0.1 - 04/26/2024 =
* Fix - absolute path issue on specific servers

= 2.0.0 - 04/xx/2024 =
* Info - Tested upto 6.5

= 1.5.0 - 01/24/2022 =
- Compatibility with WP 5.9
- Added WP CLI commands

= 1.4.5 - 4/20/2021 =
- Update online status for users with low capability.