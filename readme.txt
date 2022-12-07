=== Connect Nuki Smartlock ===
Contributors: sebastienserre
Tags: smartlock,automation,nuki,bookings
Donate link: https://nuki-smartlock-for-wp.com/product/nuki-for-wordpress
Requires at least: 5.8
Tested up to: 6.1
Requires PHP: 7.2
Stable tag: 1.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Connect your Nuki.io smartlock to your WordPress admin.

== Description ==
By connecting your Nuki Smartlocks to your WordPress admin, you\'ll be able to schedule an autolock between 2 hours.
A Dashboard Widget allow you to quickly see your Smartlocks state (battery level, lock state and more soon) . It will allow you to create pincode in one click if a Nuki Keypad is paired with your Smartlock.

## WooCommerce Bookings Addons ##
A WooCommerce Bookings addons is available. It will allow you to create & send to the Nuki Keypad a pin code each time a booking order is completed on your WordPress + WooCommerce website.
Available at [Nuki Smartlock for WordPress](https://nuki-smartlock-for-wp.com/).

== Frequently Asked Questions ==
= How to connect my Smartlock ? =
You need to get an API token to https://web.nuki.io/#/login
Official doc to get it: https://developer.nuki.io/page/nuki-web-api-1-4/3#heading--api-tokens

= Is it possible to connect several smartlock ? =
Yes, from version 0.5.0, Connect Your Nuki Smartlock will list all Nuki SMartlock connected to your NukiWeb account

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
= 1.0.1 -- 07 december 2022 =
- Correct link to delete gnerated pin code

= 1.0.0 -- 19 october 2022 =
- add style to API Key error msg
- first stable version.

= 0.5.5 -- 11 october 2022 =
- Fix notices.
- Correct hours selectors.

= 0.5.4 -- 09 october 2022 =
- Fix notices.
- correct wrong 0.5.3 svn fucking deployment.

= 0.5.1//0.5.2 -- 07 october 2022 =
- Fix a fatale + Warning & notice if no or wrong APIKey filled in settings.

= 0.5.0 -- 07 october 2022 =
- Allow to link how much smartlock you have on your backoffice.
- The Dashboard widget is now showing all smartlock you may have connected.

= 0.4.0 -- 02 october 2022 =
- Allow to lock/unlock from the Widget Dashboard
- add PHPUnit tests for robustness

= 0.3.1 -- 01 october 2022 =
- Hide the pincode generation if no keypad paired
- fix some typo

= 0.3.0 -- 30 september 2022 =
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
