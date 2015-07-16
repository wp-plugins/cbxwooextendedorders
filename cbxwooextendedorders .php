<?php
/**
 * Plugin Name:       CBX Woo Extended Order Display
 * Plugin URI:        http://wpboxr.com/product/cbx-woocommerce-extended-order-display
 * Description:       Filter and sort orders by coupon
 * Version:           2.0.0
 * Author:            Wpboxr
 * Author URI:        http://wpboxr.com
 * Text Domain:       cbxwooextendedorders
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

define('CBX_EXTENDEDORDERS_BASENAME', plugin_basename(__FILE__)); //define global var
define('CBX_EXTENDEDORDERS_VERSION', '2.0.0');

require_once(plugin_dir_path(__FILE__) . "class.cbxwooextendedorders _settings-api.php"); // include settings page
require_once(plugin_dir_path(__FILE__) . "cbxwooextendedorders_functions.php");

register_activation_hook(__FILE__, array('cbxwooextendedorders', 'cbxwooextendedorders_install_plugin'));


/**
 * Class cbxwooextendedorders
 */
class cbxwooextendedorders{

    protected $plugin_slug = 'cbxwooextendedorders';
    const VERSION = '2';

    public function __construct()
    {

        add_action('admin_init', array($this, 'cbx_wooextendedorder_init'));
        add_action('wp_enqueue_scripts', array($this, 'cbxextendedorders_enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'cbxextendedorders_enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'cbxextendedorders_enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'cbxextendedorders_enqueue_scripts'));
        add_filter('plugin_action_links_' . CBX_EXTENDEDORDERS_BASENAME, array($this, 'cbx_woocommerce_support'));

        // add short code
        add_shortcode('cbxwoomyorders', array($this, 'cbx_woo_myorderextended_shortcode'));

        // ajax for read more
        add_action("wp_ajax_cbxwooextendedorders_readmore", array($this, "cb_ajax_cbxwooextendedorders_readmore"));
        add_action("wp_ajax_nopriv_cbxwooextendedorders_readmore", array($this, "cb_ajax_cbxwooextendedorders_readmore"));

        add_action('wp_head', array($this, 'cbx_orderbycoupon_custom_js'), 100);

        // add_action('admin_head',array($this , 'cbx_orderbycoupon_custom_js_admin'),100);
        add_action('admin_menu', array($this, 'cbxwooextendedorders_admin'));

	    //relocate the woocommerce myorder template from this plugin
	    add_filter('woocommerce_locate_template', array('cbxwooextendedorders', 'cbxwooextendedorders_woocommerce_locate_template'), 10, 3); // to show woocommerce template from our plugin

    }// end of construct

    /**
     * add js to head
     */
    function cbx_orderbycoupon_custom_js()
    {
        $cborderbycoupon_setting_api = get_option('cbxwooextendedorders_front_settings');
        $cbordercoupons              = cbxwooextendedorders :: cbxwooextendedorders_getallcoupons();

        if (isset($cborderbycoupon_setting_api['cborderbycoupon_autocomplete'])) {
            $cborderbycoupon_autocomplete = $cborderbycoupon_setting_api['cborderbycoupon_autocomplete'];
        } else {
            $cborderbycoupon_autocomplete = '';
        }

        $output = '<script type="text/javascript">
                       jQuery(function() {';

                        if ($cborderbycoupon_autocomplete == 'on') {
                            $output .= ' jQuery( "#cborderbycouponforwoocommerce_dropdown_order_coupon" ).autocomplete({
                                            source: ' . json_encode($cbordercoupons) . '
                             });';
                        }

                        $output .=
                            '});
                     </script>';

        echo $output;
    }


	/**
	 * Plugin activation hook
	 */
	public static function cbxwooextendedorders_install_plugin()
    {

        $check_user_group = (get_option('cbxwooextendedorders_front_settings'));

        if (is_array($check_user_group['cborderbycoupon_usergroupfrontend']) && empty($check_user_group['cborderbycoupon_usergroupfrontend'])) {
            $check_user_group['cborderbycoupon_usergroupfrontend'] = array("administrator" => "administrator", "editor" => "editor");
        } else {
            $check_user_group['cborderbycoupon_usergroupfrontend'] = array("administrator" => "administrator", "editor" => "editor");
        }
        update_option('cbxwooextendedorders_front_settings', $check_user_group);


        $check_user_group = (get_option('cbxwooextendedorders_back_settings'));
            if (is_array($check_user_group['cborderbycoupon_usergroupbackend']) && empty($check_user_group['cborderbycoupon_usergroupbackend'])) {
                $check_user_group['cborderbycoupon_usergroupbackend'] = array("administrator" => "administrator", "editor" => "editor");
            } else {
                $check_user_group['cborderbycoupon_usergroupbackend'] = array("administrator" => "administrator", "editor" => "editor");
            }
        update_option('cbxwooextendedorders_back_settings', $check_user_group);


    }// end of function

    /**
     * function for shortcode orders
     *
     */

    public function cbx_woo_myorderextended_shortcode()
    {


        $cbx_orders = '<div class = "cborderbycouponforwoocommerce_order_coupon_wrapper woocommerce">

            <p class = "cborderbycouponforwoocommerce_coupon_filter_title">
              <h2>' . __('My Recent Orders', 'cbxwooextendedorders') . '</h2>
            </p>

            <p class = "cborderbycouponforwoocommerce_coupon_filter_noorder"></p>
             <input type = "text" id = "cborderbycouponforwoocommerce_dropdown_order_coupon" placeholder = "' . __('Enter coupon code', 'cbxwooextendedorders') . '">
             <a href = "#" data-type = "filter" data-order-coupon = "" data-ref = "shortcode" data-busy = "0" class = "button cborderbycouponforwoocommerce_coupon_filter cbwoocommerce_order_coupon_readmore" data-next-page = "1" >' . __('Filter', 'cbxwooextendedorders') . '</a>


            <table class = "cborderbycouponforwoocommerce_order_table shop_table my_account_orders">
                <thead>
                    <tr>
                        <th class = "order-number"><span class = "nobr">' . __('Order', 'woocommerce') . '</span></th>
                        <th class = "order-date"><span class = "nobr">' . __('Date', 'woocommerce') . '</span></th>
                        <th class = "order-status"><span class = "nobr">' . __('Status', 'woocommerce') . '</span></th>
                        <th class = "order-items"><span class = "nobr">' . __('Items', 'cbxwooextendedorders') . '</span></th>
                        <th class = "order-total"><span class = "nobr">' . __('Total', 'woocommerce') . '</span></th>
                        <th class = "order-actions">&nbsp;</th>
                    </tr>
                </thead>
                <tbody class = "cborderbycouponforwoocommerce_order_table_body">';

        // call function for orders of page number one ,here coupon code none so for all coupon set coupon code 0
        // params 1 page number
        // params 2 coupon code
        $cbxwooextendedorders_next_posts =cbxwoocommerce_order_by_coupon_read(1, '', 'on', 'on', 'shortcode');
        $cbx_orders .= $cbxwooextendedorders_next_posts[0];
        $cbx_orders .= '</tbody> </table> ';
        // view more button
        $cbx_orders .= $cbxwooextendedorders_next_posts[1];
        // end of else
        $cbx_orders .= '</div> ';

        return $cbx_orders;



    }
	//add_cbxwooextendedorders admin_menu

    /**
     * add options page for this plugin
     */
	/*
    public function add_cbxwooextendedorders admin_menu()
	{
        //add_options_page(__('CBX Woo Extended Orders', 'cbxwooextendedorders'), __('CBX Woo Extended Orders', 'cbxwooextendedorders'), 'manage_options', 'cbxwooextendedorders', array($this, 'show_cbxwooextendedorders_page'));
    }
	*/

	public function cbxwooextendedorders_admin(){
		add_options_page(__('CBX Woo Extended Orders', 'cbxwooextendedorders'), __('CBX Woo Extended Orders', 'cbxwooextendedorders'), 'manage_options', 'cbxwooextendedorders', array($this, 'show_cbxwooextendedorders_page'));
	}

    /**
     * show settings page and sidebar
     */
    function show_cbxwooextendedorders_page()
    {

        global $wp_roles;
        $roles = $wp_roles->get_names();
        $roles = array_merge($roles, array('guest' => 'Guest'));

        $sections = array(
            array(
                'id'    => 'cbxwooextendedorders_front_settings',
                'title' => __('Frontend MY Account Page Settings', 'cbxwooextendedorders')
            ),
            array(
                'id'    => 'cbxwooextendedorders_back_settings',
                'title' => __('Backend  Order Page Settings', 'cbxwooextendedorders')
            )
        );

        $fields = array(
            'cbxwooextendedorders_front_settings' => array(

                array(
                    'name'    => 'cborderbycoupon_usergroupfrontend',
                    'label'   => __('User Role', 'cbxwooextendedorders'),
                    'desc'    => __('Users who can filter by coupon', 'cbxwooextendedorders'),
                    'type'    => 'postselectbox',
                    'default' => array('administrator', 'editor', 'author', 'contributor', 'subscriber', 'guest'),
                    'options' => $roles
                ),
                array(
                    'name'    => 'cborderbycoupon_postperpage',
                    'label'   => __('Post Per Page', 'cbxwooextendedorders'),
                    'desc'    => __('How many order to show in every page ', 'cbxwooextendedorders'),
                    'type'    => 'text',
                    'default' => '10',

                ),
                array(
                    'name'    => 'cborderbycoupon_autocomplete',
                    'label'   => __('Auto Complete Coupon Names', 'cbxwooextendedorders'),
                    'desc'    => __('Auto complete coupon names while filtering ', 'cbxwooextendedorders'),
                    'type'    => 'checkbox',
                    'default' => '',

                )
            ),
            'cbxwooextendedorders_back_settings'  => array(
                array(
                    'name'    => 'cborderbycoupon_usergroupbackend',
                    'label'   => __('User Role', 'cbxwooextendedorders'),
                    'desc'    => __('User who can filter by coupon', 'cbxwooextendedorders'),
                    'type'    => 'postselectbox',
                    'default' => array('administrator', 'editor', 'author', 'contributor', 'subscriber', 'guest'),
                    'options' => $roles
                )

            ),

        );


        $settings_api = new cbxwooextendedorders_Settings_API();
        $settings_api->set_sections($sections);
        $settings_api->set_fields($fields);
        //initialize them
        $settings_api->admin_init();

        include(plugin_dir_path(__FILE__) . 'cb-sidebar.php');

    }


    /**
     *init for plugin
     */
    public function cbx_wooextendedorder_init()
    {

        $path        = dirname(plugin_basename(__FILE__)) . '/languages/';
        $lang_loaded = load_plugin_textdomain('cbxwooextendedorders', false, $path);
        cbxwooextendedorders_admin_init();
    }

    /**
     * ajax function for read more
     * return array
     *
     */
    public function cb_ajax_cbxwooextendedorders_readmore()
    {

        if (isset($_POST['cborderbycoupon_nextpage']) && isset($_POST['cborderbycoupon_coupon'])) {

            $cborderbycoupon_nextpage = intval($_POST['cborderbycoupon_nextpage']);
            $cborderbycoupon_coupon   = $_POST['cborderbycoupon_coupon']; //check if it validate %

            if (isset($_POST['cborderbycoupon_ref']) && $_POST['cborderbycoupon_ref'] == 'shortcode') {

                $cborderbycoupon_nextpage_orders = cbxwoocommerce_order_by_coupon_read($cborderbycoupon_nextpage, $cborderbycoupon_coupon, 'on', 'on', 'shortcode');

            } else {

                $cborderbycoupon_nextpage_orders = cbxwoocommerce_order_by_coupon_read($cborderbycoupon_nextpage, $cborderbycoupon_coupon);
            }
            echo json_encode($cborderbycoupon_nextpage_orders);
            die();
        }
    }

    /**
     * add template hirarcy
     * this is needed to replace order page and show ours
     */
    public static function cbxwooextendedorders_woocommerce_locate_template($template, $template_name, $template_path){
        global $woocommerce;

	    if($template_name == 'myaccount/my-orders.php'){

		    $plugin_path = cbxwooextendedorders :: cbxwooextendedorders_plugin_path() . '/woocommerce/';
		    $template_ext = $plugin_path . $template_name;
		    if(file_exists($template_ext)){
			    return $template_ext;
		    }
	    }
	    return $template;



    }



    /**
     * @return string
     * to get the plugin path
     */
    public static function cbxwooextendedorders_plugin_path()
    {
        // gets the absolute path to this plugin directory
        return untrailingslashit(plugin_dir_path(__FILE__));
    }

    /**
     * @param $vars
     *
     * @return mixed
     * modify query var of order table to filter by coupon
     */
    public static function cbx_woo_orderby($join)
    {

        global $typenow, $wp_query, $wpdb;

        if ($typenow == 'shop_order' && isset($_GET['order_coupon']) && $_GET['order_coupon'] != '0') {

         /*
             $cbx_obc_wc_sales_coupon = ($_GET['order_coupon']);
            $cbx_obc_wc_sales_type   = 'shop_order';
            $coupon_table1           = $wpdb->prefix . "posts"; //$wpdb->posts
            $coupon_table3           = $wpdb->prefix . "postmeta";
         */
            $coupon_table2           = $wpdb->prefix . "woocommerce_order_items";
            $join .= ' INNER JOIN ' . $coupon_table2 . ' item ON ' . $wpdb->posts . '.ID = item.order_id                    ';

            return $join;

        } // end of if type shop_order

        else return $join;
    }// end of function

    /**
     * @param $vars
     *
     * @return mixed
     * modify query var of order table to filter by coupon
     */
    public static function cbx_woo_orderswhere($where)
    {

        global $typenow, $wp_query, $wpdb;
            if ($typenow == 'shop_order' && isset($_GET['order_coupon']) && $_GET['order_coupon'] != '0') {
                $cbx_obc_wc_sales_coupon = esc_attr($_GET['order_coupon']);

	            var_dump($cbx_obc_wc_sales_coupon);
               /*
                    $cbx_obc_wc_sales_type   = 'shop_order';
                    $coupon_table1           = $wpdb->prefix . "posts"; //$wpdb->posts
                    $coupon_table3           = $wpdb->prefix . "postmeta";
                    $coupon_table2           = $wpdb->prefix . "woocommerce_order_items";
                */
                $where .= ' AND item.order_item_type="coupon" AND item.order_item_name="' . $cbx_obc_wc_sales_coupon . '" ';

                return $where;

            } // end of if type shop_order

        else return $where;
    }// end of function

    /**
     *  cbxwooextendedorders_restrict_manage_orders
     *  show all coupon name dropdown
     */

    public static function cbxwooextendedorders_restrict_manage_orders()
    {

        global $woocommerce, $typenow, $wp_query;

        if ('shop_order' != $typenow) {
            return;
        }

	    $cborderbycoupon_setting_api      = get_option('cbxwooextendedorders_back_settings');
	    //var_dump($cborderbycoupon_setting_api);
	    $cborderbycoupon_userrole_backend = array(); //default

	    if (isset($cborderbycoupon_setting_api['cborderbycoupon_usergroupbackend'])) {

		    $cborderbycoupon_userrole_backend = $cborderbycoupon_setting_api['cborderbycoupon_usergroupbackend'];
	    }

	    $cbpermitted_role_for_filtercoupon_backend = (array_intersect($cborderbycoupon_userrole_backend, wp_get_current_user()->roles));

	    if (is_array($cbpermitted_role_for_filtercoupon_backend) && !empty($cbpermitted_role_for_filtercoupon_backend)) { // check curerent user has the desired role
		    remove_filter('posts_join_request', array('cbxwooextendedorders', 'cbx_woo_orderby'));
		    remove_filter('posts_where_request', array('cbxwooextendedorders', 'cbx_woo_orderswhere'));


		    ?>
		    <select name='order_coupon' id='dropdown_order_coupon'>

			    <option
				    <?php if (isset($_GET['order_coupon'])) selected('0', $_GET['order_coupon']); ?>value="0">
				    <?php _e(' All Coupons', 'cbxwooextendedorders'); ?>
			    </option>

			    <?php
			    $cbordercoupons = cbxwooextendedorders :: cbxwooextendedorders_getallcoupons();
			    foreach ($cbordercoupons as $cbordercoupon) {
				    ?>

				    <option <?php if (isset($_GET['order_coupon'])) selected($cbordercoupon, $_GET['order_coupon']); ?> value="<?php echo $cbordercoupon; ?>">
					    <?php echo $cbordercoupon; ?>
				    </option>

			    <?php }  ?>

		    </select>
		    <?php
	    }

    }//end of function

    /**
     * list all coupons
     */
    public static function cbxwooextendedorders_getallcoupons($get_ids = false)
    {

        $cborder_coupons       = array();
        $cborder_coupons_ids   = array();
        $cbx_obc_wc_sales_type = 'shop_coupon';

        $cbx_obc_wc_sales_type_args = array(
            'post_type'           => $cbx_obc_wc_sales_type,
            'posts_per_page'      => -1,
            'ignore_sticky_posts' => 0
        );

        $cbx_obc_wc_sales_my_query = null;
        $cbx_obc_wc_sales_my_query = new WP_Query($cbx_obc_wc_sales_type_args);

        if ($cbx_obc_wc_sales_my_query->have_posts()) {

            while ($cbx_obc_wc_sales_my_query->have_posts()) : $cbx_obc_wc_sales_my_query->the_post();
                array_push($cborder_coupons, get_the_title());
                array_push($cborder_coupons_ids, get_the_ID());

            endwhile;
        }

        wp_reset_query(); // Restore

        if ($get_ids != false) {
            return $cborder_coupons_ids;
        } else {
            return $cborder_coupons;
        }


    }


    /**
     * cbxwooextendedorders_columnset
     * set order coupon column in order table
     */
    public static function cbxwooextendedorders_columnset($columns)
    {
        $columns['order_coupon'] = __("Coupon", "cbxwooextendedorders");
        return $columns;
    }

    /**
     * @param $column_name
     * @param $post_id
     * show coupon name in coupon column
     */

    public static function cbxwooextendedorders_columndisplay($column_name, $post_id)
    {

        if ('order_coupon' != $column_name)
            return;

        // $cbx_obc_wc_sales_information = $post_id;
        global $post, $woocommerce, $cbx_obc_wc_sales_the_order;

            if (empty($cbx_obc_wc_sales_the_order) || $cbx_obc_wc_sales_the_order->id != $post->ID) {
                $cbx_obc_wc_sales_the_order = new WC_Order($post_id);
            }

        $cbx_obc_wc_sales_information = $cbx_obc_wc_sales_the_order->get_used_coupons();
       // $cbx_obc_wc_meta              = get_post_meta($post_id);

        if (is_array($cbx_obc_wc_sales_information) && count($cbx_obc_wc_sales_information) > 0) {

            foreach ($cbx_obc_wc_sales_information as $cbx_obc_wc_sales_coupon) {
                echo ($cbx_obc_wc_sales_coupon) . ' ';
            }

        } else {
            echo __('No coupon used', 'cbxwooextendedorders');
        }
    }

    /**
     * @param $columns
     *
     * @return mixed
     * called to add sort function to column coupon
     */
    public static function cbxwooextendedorders_columnsort($columns)
    {
        $columns['order_coupon'] = 'order_coupon';
        return $columns;
    }


    /**
     * Register and enqueues  JavaScript files.
     *
     * @since    1.0.0
     */
    public function cbxextendedorders_enqueue_scripts()
    {

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script($this->plugin_slug . '-chosen-cb', plugins_url('/assets/js/chosen.jquery.min.js', __FILE__), array('jquery'), cbxwooextendedorders::VERSION);
        wp_enqueue_script($this->plugin_slug . '-cbxexorders', plugins_url('/assets/js/cbxwooextendedorders.js', __FILE__), array('jquery', 'jquery-ui-autocomplete'), cbxwooextendedorders::VERSION);
        wp_localize_script($this->plugin_slug . '-cbxexorders', 'couponAjax', array('adminurl' => admin_url('user-edit.php?user_id='), 'ajaxurl' => admin_url('admin-ajax.php'), 'error_msg' => __('You Have No Order', 'cbxwooextendedorders')));

    }


    /**
     * Register and enqueue style sheet.
     *
     * @since    1.0.0
     */
    public function cbxextendedorders_enqueue_styles()
    {

        wp_enqueue_style($this->plugin_slug . '-ui-styles', plugins_url('assets/css/ui-lightness/jquery-ui.min.css', __FILE__), array(), cbxwooextendedorders::VERSION);
        wp_enqueue_style($this->plugin_slug . '-chosen-cb', plugins_url('/assets/css/chosen.min.css', __FILE__), array(), cbxwooextendedorders::VERSION);
        wp_enqueue_style($this->plugin_slug . '-customstyle', plugins_url('/assets/css/cbxwooextendedorders.css', __FILE__), array(), cbxwooextendedorders::VERSION);

    }

    /**
     *
     * this function adds support link to plugin
     */
    function cbx_woocommerce_support($links)
    {

        array_unshift(  $links, sprintf('<a href="options-general.php?page=cbxwooextendedorders">' . __('Settings', 'cbxwooextendedorders') .'</a>') );
        array_unshift(  $links, sprintf('<a href="http://wpboxr.com/contact-us/">'. __("Support"). ' </a>'));

        return $links;
    }

}

// end of class
// create a instance of the class
new cbxwooextendedorders();