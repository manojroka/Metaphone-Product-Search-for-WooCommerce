=== Woo Metaphone Product Search ===
Contributors: manojroka
Donate link: http://account.dmandomains.com/manoj/
Tags: search, woocommerce, ajax search
Requires at least: 4.0
Tested up to: 4.7.3
Stable tag: 4.7.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin uses Metaphone 2 algorithm to perform the WooCommerce product search. It can be used either with WooCommerce search form or wordpress search form and presents the search result in nice popup using ajax.

== Description ==

It performs ajax search over WooCommerce Products using famous double metaphone algorithm by Lawrence Philips. No more worries about spelling mistakes while users search your store. Please be informed that it may take some time while activating the plugin depending upon the number of products on your store. The results and behaviour of search can be controlled from the plugin settings page in admin panel. Although the main priority while displaying search result is given to product name, it also matches the product short description and description.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Metaphone Search screen to configure the plugin from admin panel

== Frequently Asked Questions ==

= Plugin did not work? No ajax popup? Error in console? =

This plugin is dependant on jQuery and Bootstrap Datatable. If you experience any issue like above then the reason is most likely there is either missing jQuery and/or Bootstrap Datable plugin or because of its conflict due to multiple version. Go to plugin Settings page Admin >> Metaphone Search and try loading/unloading the jQuery and Bootstrap. By default, the plugin expects that jQuery is already loaded in the site and bootstrap is not loaded.

= Search Result Popup looking very small? =

Try adding the following css rule in your css file: .gc_dmetaphone_ajax #suggestions.suggestionsBox{ right:0 }. You can customize and position the search result popup with the above selector as you like depending on your search box position.

== Screenshots ==
1. `/assets/searchresult.png`
2. `/assets/adminscreen.png`