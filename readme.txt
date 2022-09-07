=== Connect Nuki Smartlock ===
Contributors: sebastienserre
Tags: smartlock, automation,
Donate link: https://github.com/sponsors/sebastienserre/
Requires at least: 4.7
Tested up to: 6.0
Requires PHP: 7.0
Stable tag: 0.2.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Connect your Nuki.io smartlock to your WordPress admin.

== Description ==
By connecting your Nuki Smartlock to your WordPress admin, you\'ll be able to schedule an autolock between 2 hours.
BY default, Nuki allow to lock the door at a predefined date but, if it\'s unlocked after this one, then, it doesn\'t lock back.
With Connect your Nuki Smartlock, it checks the smartlock state and lock it between the defined hour.

== Frequently Asked Questions ==
= How to connect my Smartlock ? =
You need to get an API token to https://web.nuki.io/#/login
Official doc to get it: https://developer.nuki.io/page/nuki-web-api-1-4/3#heading--api-tokens

= Does this plugin developed by Nuki ? =
No, I\'m an independent WordPress developer, owning a Nuki Smartlock 3.0 Pro.


== Screenshots ==
1. Settings

== Changelog ==
= 0.2.0 -- 07 September 2022 =
- Add support for Bookings (Needs Pro add-on)

= 0.1.0 -- 25 August 2022 =
- Initial version
- allow to schedule a time period where the smartlock must be locked.
- The plugin automatically lock the smartlock if it\'s unlocked in this period.