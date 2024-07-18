=== Payment Banking Slip Pix for LifterLMS ===
Contributors: linknacional
Donate link: https://www.linknacional.com/wordpress/plugins/
Tags: lifterlms, bank, slip, pix, payment
Requires at least: 5.5
Tested up to: 6.6
Stable tag: 1.0.7
Requires PHP: 7.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Enable bank slip and pix payment for LifterLMS.

== Description ==

The [Payment Banking Slip Pix for LifterLMS](https://www.linknacional.com/wordpress/plugins/) is an extension plugin for LifterLMS which enables the bank slip and pix payment.


**Dependencies**

Payment Banking Slip Pix for LifterLMS plugin is dependent on [LifterLMS plugin](https://wordpress.org/plugins/lifterlms/), please make sure LifterLMS is installed and properly configured before starting Payment Banking Slip Pix for LifterLMS installation.

We utilize some open-source JS libraries:
* [Bootstrap](https://github.com/twbs/bootstrap) for complement tooltip generation and better input treatment;
* [Popper JS](https://www.npmjs.com/package/@popperjs/core) for tooltip generation.

We utilize the following open-source composer PHP libraries:
* [PHP Barcode Generator](https://github.com/picqer/php-barcode-generator) for banking slip invoice payment generation.

We utilize [PagHiper](https://www.paghiper.com/) as a payment processor, it will process the payment with the information provided in the checkout page. You will need a PagHiper account to utilize this payment method. Here are the  (service terms)[https://www.paghiper.com/contrato].

**User instructions**

1. Now go to the Settings menu of the LifterLMS;

2. Select the 'Checkout' option;

3. Next to 'Checkout Settings' are the payment methods, select one;

4. Look for the option 'Enable / Disable' and click on it, with this the payment method will be active;

5. Look for the fields 'Api Key' and 'Token Key' and type your PagHiper credentials; 

6. Configure the rest of the payment method according to your needs;

7. Then, click on the 'Save Changes' button at the top right of page;

8. Repeat the same steps for other payment methods.

The Payment Banking Slip Pix for LifterLMS is now live and working.


== Installation ==

1. Look in the sidebar for the WordPress plugins area;

2. In installed plugins look for the 'add new' option in the header;

3. Click on the 'submit plugin' option in the page title and upload the payment-banking-slip-pix-for-lifterlms.zip plugin;

4. Click on the 'install now' button and then activate the installed plugin;

The Payment Banking Slip Pix for LifterLMS is now activated.


== Frequently Asked Questions ==

= What is the plugin license? =

* This plugin is released under a GPL license.

= What is needed to use this plugin? =

* LifterLMS version 7.1.4 or latter installed and active.


== Screenshots ==

1. List LifterLMS payment methods for PIX and Banking Slip.
2. Payment method PIX settings page.
3. Payment method PIX settings page.
4. Payment method Banking Slip settings page.
5. Payment method Banking Slip settings page.
6. Payment method PIX front page.
7. Payment method Banking Slip front page.
8. PIX payment area.
9. Banking Slip payment area.

== Changelog ==

= 1.0.7 =
**10/07/2024**
* Change in the barcode generation method and its implementation.

= 1.0.6 =
**18/03/2024**
* Update README file with PagHiper service terms;
* Add composer.json reference in the plugin build.


= 1.0.5 =
**06/03/2024**
* Remove plugin updater lib;
* Add more references for libraries utilized.

= 1.0.4 =
**15/02/2024**
* Update root file name to slug;
* Add comments for translations placeholders;
* Fixed incorrectly i18n function calls.

= 1.0.3 =
**02/02/2024**
* Update script to not allow direct access;
* Change prefix to lknpbsp to avoid collisions.

= 1.0.2 =
**25/01/2024**
* Add external scripts to load localy;
* Remove variables from internationalization functions;
* Removed HEREDOC;
* Escape HTML variables;
* Add prefix for global functions.

= 1.0.1 =
**12/01/2024**
* Fixed issues found in plugin checker;
* Add missing dependencies on build;
* Updated plugin building script.

= 1.0.0 =
**04/10/2023**
* Plugin launch;
* Add payment method Pix using PagHiper API;
* Add payment method Bank slip using PagHiper API.

== Upgrade Notice ==

= 1.0.0 =
**04/10/2023**
* Plugin launch;
* Add payment method Pix using PagHiper API;
* Add payment method Bank slip using PagHiper API.
