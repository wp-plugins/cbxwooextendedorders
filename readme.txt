===  CBX Woo Extended Order Display ===
Contributors: manchumahara, codeboxr, wpboxr
Donate link: http://wpboxr.com
Tags: woocommerce, woocommerce order
Requires at least: 3.0
Tested up to: 4.2.2
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Woocommerce Exteneded Order Display by coupon filter and more.

== Description ==

CBX Woo Extended Order Display

CBX Woo Extended Order Display is a WordPress plugin that will help you to sort and filter  orders by coupon names in admin panel and view coupon names used for orders in an extra column in order table.
In front end it rewrite the orders template of woocommerce plugin .You can select  user groups who can  filter orders by coupon name in front end and backend .
In front end one user can view and filter his/her own orders according to settings ,You can enable auto complete coupon names option also.
There is pagination view and you can select how many orders to show in every page. There is a short code to show the logged in user orders only in any page or posts .
They table you get  with shortcode  contains an extra coulmn items where all items (name ,quantity and image ) in an order is listed nicely.



Features:

*   Extra column with coupons listing in backend order table
*   Filter orders by coupon in front end and backend
*   Nice paginated view with optional per page settings
*   Optional auto complete coupon name in front end
*   Enable or disable filtering facility for any user groups
*   List all orders or only logged in user orders in front end as you want

See more details and usages guide here http://wpboxr.com/product/cbx-woocommerce-extended-order-display


== Installation ==

How to install the plugin and get it working.


1. Upload `cbxwooextendedorders ` folder  to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Setting->Order by Coupon to edit settings
4. Create my account page with woocommerce shortcode [woocommerce_my_account] or it override woocommerce's shortcode [woocommerce_my_account]
5. In any post or page you can write shortcode [cbxwoomyorders] to view only logged in user orders with extra column of item listing


== Frequently Asked Questions ==

= What will happen if i deselect a user role ex: editor from fronend settings  ? =

then a user with role editor cant filter orders by coupon and will see a listing of all user orders /own orders according to settings

= Why there is user role restrict option for backend ?=

as coupon names are secret sometimes coupon name and filter option will be shown for user roles you select

= Why auto complete coupon names  ?=

to help user selecting coupon names




== Screenshots ==

1. Setting Menu from left side
2. Setting panel 1
3. Setting panel 2
4. User role selection in setting panel
5. Coupon dropdown in order listing
6. Frontend coupon search field
7. Coupon auto complete

== Changelog ==

= 2.0.0 =
* First Release

