=== Connect Nuki Smartlock ===
Contributors: sebastienserre
Tags: smartlock,automation,nuki,bookings
Donate link: https://nuki-smartlock-for-wp.com/product/nuki-for-wordpress
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.3.12
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Connect your Nuki.io smartlock to your WordPress admin.

== Description ==
By connecting your Nuki Smartlocks to your WordPress admin, you\'ll be able to schedule an autolock between 2 hours.
A Dashboard Widget allow you to quickly see your Smartlocks state (battery level, lock state and more soon) . It will allow you to create pincode in one click if a Nuki Keypad is paired with your Smartlock.

## WooCommerce Bookings & Yith Booking and Appointment Addons ##
A WooCommerce Bookings & Yith Booking and Appointment addon is available. It will allow you to create & send to the Nuki Keypad a pin code each time a booking order is completed on your WordPress + WooCommerce website.
Available at [Nuki Smartlock for WordPress](https://nuki-smartlock-for-wp.com/).

== Frequently Asked Questions ==
= How to connect my Smartlock ? =
You need to get an API token to [Nuki Web](https://web.nuki.io/#/login)
Official doc to get it: [doc](https://developer.nuki.io/page/nuki-web-api-1-4/3#heading--api-tokens)

= Is it possible to connect several smartlock ? =
Yes, from version 0.5.0, Connect Your Nuki Smartlock will list all Nuki Smartlock connected to your NukiWeb account

= Does this plugin developed by Nuki ? =
No, I'm an independent WordPress developer, owning a Nuki Smartlock 3.0 Pro.

= I've an idea to improve this plugin. =
Great! share your idea at our [Ideas page](https://nuki-smartlock-for-wp.com/ideas)

= How to contribute to this plugin? =
If you're a developer, you can send pull request to [Github](https://github.com/sebastienserre/connect-nuki-smartlock)
If you're Polyglot, you can help translating this plugin at [Translate Plugins](https://translate.wordpress.org/projects/wp-plugins/connect-your-nuki-smartlock/)
You can also just sponsors my work at [GH Sponsor](https://github.com/sponsors/sebastienserre/)

= How to get a Nuki Smartlock ? =
Visit [https://nuki-smartlock-for-wp.com/30e-discount-on-your-nuki-smartlock]( https://nuki-smartlock-for-wp.com/30e-discount-on-your-nuki-smartlock ) for instructions.

== Screenshots ==
1. Settings
2. Dashboard Widget

== Changelog ==
= 1.3.12 -- 24 August 2024 =
- force update

= 1.3.11 -- 24 August 2024 =
- fix issue preventing a hourly booking to have their pincode !

= 1.3.10 -- 23 August 2024 =
- force update

= 1.3.9 -- 22 August 2024 =
- Revert date/time improvment. No more pincode were created on Yith Booking & appointment compatibility

= 1.3.8 -- 19 August 2024 =
- Improve French Translation (l10n)
- Yith Booking: Create and send the pincode on Paid Satust (instead of Complete Order)
- Yith Booking: Email and WooCommerce emails. Add a {pincode} placeholder to show tne pincode in your templates
- Yith Booking: Add the pincode in the email Booking details
- Yith Booking: Show the pincode in the "My Account" page, on Booking details.
- Prevent a new pincode generation if a booking/order already has one.
- Code cleaning

= 1.3.5+1.3.6 -- 14th August 2024 =
- fix a Warning in the settings

= 1.3.4 -- 30th May 2023 =
- fix bug which prevent to store a Nuki API Key :(

= 1.3.3 -- 12th April 2023 =
- Fix notices
- Fix a fatale error which prevent the WooCommerce addon to work properly. Thanks ateliernovae.de

= 1.3.2 -- 7th April 2023 =
- Fix notices
- Improve WooCommerce API (for Pro version)

= 1.3.1 -- 8th March 2023 =
- Fix a warning on API settings.

= 1.3.0 -- 8th March 2023 =
- Add time managment.
- Fix list of smartlocks available.

= 1.2.0 -- 5th February 2023 =
- Delete old pincode.
- UI improvement.

= 1.1.2 -- 20th January 2023 =
- Remove development only files from release
- Fix a typo in settings page title.
- Perf. Prevent calling the Nuki APi before having an API Key set.

= 1.1.1 -- 18th January 2023 =
- Improve license management for premium plugins
- Generate a link in the BO to unlock
- Fix the pincode sending to keypad method.

= 1.1.0 -- 28 december 2022 =
- Maintenance fix to work with Nuki for WooCommerce anf the Yith Booking integration.

= 1.0.1 -- 07 december 2022 =
- Correct link to delete generated pin code

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
