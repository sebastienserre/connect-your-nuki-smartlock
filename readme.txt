=== Connect Nuki Smartlock ===
Contributors: sebastienserre
Tags: smartlock, automation, nuki, bookings
Donate link: https://github.com/sponsors/sebastienserre/
Requires at least: 4.7
Tested up to: 6.0
Requires PHP: 7.0
Stable tag: 0.4.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Connect your Nuki.io smartlock to your WordPress admin.

== Description ==
By connecting your Nuki Smartlock to your WordPress admin, you\'ll be able to schedule an autolock between 2 hours.
A Dashboard Widget allow you to quickly see your Smartlock state. It will allow you to create pincode in one click if a Nuki Keypad is paired with your Smartlock.

## WooCommerce Bookings Addons ##
A WooCommerce Bookings addons is available. It will allow you to create & send to the Nuki Keypad a pin code each time a booking order is completed on your WordPress + WooCommerce website.
Available at [Nuki Smartlock for WordPress](https://nuki-smartlock-for-wp.com/)

== Frequently Asked Questions ==
= How to connect my Smartlock ? =
You need to get an API token to https://web.nuki.io/#/login
Official doc to get it: https://developer.nuki.io/page/nuki-web-api-1-4/3#heading--api-tokens

= Does this plugin developed by Nuki ? =
No, I'm an independent WordPress developer, owning a Nuki Smartlock 3.0 Pro.

= I've an idea to improve this plugin. =
Great! share your idea at our [Ideas page](https://nuki-smartlock-for-wp.com/ideas)

= How to contribute to this plugin? =
If you're a developer, you can send pull request to [Github](https://github.com/sebastienserre/connect-nuki-smartlock)
If you're Polyglot, you can help translating this plugin at [Translate Plugins](https://translate.wordpress.org/projects/wp-plugins/connect-your-nuki-smartlock/)
You can also just sponsors my work at [GH Sponsor](https://github.com/sponsors/sebastienserre/)

== Screenshots ==
1. Settings
2. Dashboard Widget

== Changelog ==
= 0.4.0 -- 02 octobre 2022 =
- Allow to lock/unlock from the Widget Dashboard
- add PHPUnit tests for robustness

= 0.3.1 -- 01 octobre 2022 =
- Hide the pincode generation if no keypad paired
- fix some typo

= 0.3.0 -- 30 septembre 2022 =
- Add a dashboard widget with vitals + Codepin generation
- Add PHPCS/WPCS corrections

= 0.2.2 -- 10 September 2022 =
- Improve i18n
- Correct settings name

= 0.2.1 -- 10 September 2022 =
- Correct textdomain

= 0.2.0 -- 07 September 2022 =
- Add support for Bookings (Needs Pro add-on)

= 0.1.0 -- 25 August 2022 =
- Initial version
- allow to schedule a time period where the smartlock must be locked.
- The plugin automatically lock the smartlock if it\'s unlocked in this period.
