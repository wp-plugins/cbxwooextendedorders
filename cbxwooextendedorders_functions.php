<?php


/**
 * add this to set my function priority more than woocommerce order table pririty
 */
add_action('plugins_loaded', 'init_cbxextendedorders_loaded');

/**
 * main function to load when plugin loaded
 */
function init_cbxextendedorders_loaded()
{

    $posttype                         = "shop_order";
    $cborderbycoupon_setting_api      = get_option('cbxwooextendedorders_back_settings');
	//var_dump($cborderbycoupon_setting_api);
    $cborderbycoupon_userrole_backend = array(); //default

    if (isset($cborderbycoupon_setting_api['cborderbycoupon_usergroupbackend'])) {

        $cborderbycoupon_userrole_backend = $cborderbycoupon_setting_api['cborderbycoupon_usergroupbackend'];
    }

    $cbpermitted_role_for_filtercoupon_backend = (array_intersect($cborderbycoupon_userrole_backend, wp_get_current_user()->roles));

    if (is_array($cbpermitted_role_for_filtercoupon_backend) && !empty($cbpermitted_role_for_filtercoupon_backend)) { // check curerent user has the desired role

        add_filter("manage_edit-{$posttype}_columns", array('cbxwooextendedorders', 'cbxwooextendedorders_columnset'), 20, 1); // add coupon coulmn
        add_action("manage_{$posttype}_posts_custom_column", array('cbxwooextendedorders', 'cbxwooextendedorders_columndisplay'), 20, 2); // show coupon names in column
        add_filter("manage_edit-{$posttype}_sortable_columns", array('cbxwooextendedorders', 'cbxwooextendedorders_columnsort')); //add coupon column as sort column
        add_action('restrict_manage_posts', array('cbxwooextendedorders', 'cbxwooextendedorders_restrict_manage_orders')); //coupon select box in filter area

        // edit wp_query
        add_filter('posts_join_request', array('cbxwooextendedorders', 'cbx_woo_orderby'));
        add_filter('posts_where_request', array('cbxwooextendedorders', 'cbx_woo_orderswhere'));

    }


    //add_filter('woocommerce_locate_template', array('cbxwooextendedorders', 'cbxwooextendedorders_woocommerce_locate_template'), 10, 3); // to show woocommerce template from our plugin

} // end of function init_cbxwooextendedorders

if (!function_exists('cbxwooextendedorders_admin_init')): // settings initialization

    /**
     * cbxwooextendedorders_admin_init function
     * init settings
     */

    function cbxwooextendedorders_admin_init()
    {

        $sections     = array(
            array(
                'id'    => 'cbxwooextendedorders_front_settings',
                'title' => __('My Account Page Settings', 'cbxwooextendedorders')
            ),
            array(
                'id'    => 'cbxwooextendedorders_back_settings',
                'title' => __('Backend Order Page Settings', 'cbxwooextendedorders')
            )
        );


        $fields       = array('cbxwooextendedorders_front_settings' => array(), 'cbxwooextendedorders_back_settings' => array());
        $settings_api = new cbxwooextendedorders_Settings_API();
        $settings_api->set_sections($sections);
        $settings_api->set_fields($fields);
        $settings_api->admin_init(); //initialize them


    }
endif;
// end of function cbxwooextendedorders_admin_init


/**
 * @param $per_page                                 // how many post in a page will get from global settings
 * @param $page_number                              // in which page
 * @param $order_coupon                             default null for all coupons
 *                                                  @$cborderbycoupon_show_this_user_coupon default null , 'on' for my orders only
 * @param $cborderbycoupon_filter_this_user_coupon  default null , 'on' for my orders only
 *
 * @return array
 * return rows of order main function to get orders
 */

function cbxwoocommerce_order_by_coupon_read($page_number = 1, $order_coupon = '', $cborderbycoupon_show_this_user_coupon = 'on', $cborderbycoupon_filter_this_user_coupon = 'on', $reference = '')
{

    $cborderbycoupon_setting_api = get_option('cbxwooextendedorders_front_settings');
    $cbx_orders_output           = '';
    $cborderbycoupon_perpage     = 15; // default perpage

    if (isset($cborderbycoupon_setting_api['cborderbycoupon_postperpage']) && $cborderbycoupon_setting_api['cborderbycoupon_postperpage'] != 0 && $cborderbycoupon_setting_api['cborderbycoupon_postperpage'] != '') {

        $cborderbycoupon_perpage = intval($cborderbycoupon_setting_api['cborderbycoupon_postperpage']); // take perpage from global settings
    }

    if ($order_coupon != '') {
        //normal readmore
        global $wpdb;
        $coupon_table1 = $wpdb->prefix . "posts"; //$wpdb->posts
        $coupon_table3 = $wpdb->prefix . "postmeta";
        $coupon_table2 = $wpdb->prefix . "woocommerce_order_items";
        $coupon_limit1 = ($page_number - 1) * ($cborderbycoupon_perpage);

        if ($cborderbycoupon_filter_this_user_coupon == 'on') {

            $sql = 'SELECT post.*, item.* FROM ' . $coupon_table1 . '  post
                         INNER JOIN ' . $coupon_table2 . ' item ON post.ID = item.order_id
                         INNER JOIN ' . $coupon_table3 . ' itemmeta ON post.ID = itemmeta.post_id
                         WHERE itemmeta.meta_key = "_customer_user" AND itemmeta.meta_value = "' . get_current_user_id() . '" AND post.post_type = "shop_order" AND post.post_status != "trash"  AND item.order_item_type="coupon" AND item.order_item_name="' . $order_coupon . '"LIMIT ' . $coupon_limit1 . ', ' . $cborderbycoupon_perpage;

        } else {
            $sql = 'SELECT post.*, item.* FROM ' . $coupon_table1 . '  post
                         INNER JOIN ' . $coupon_table2 . ' item ON post.ID = item.order_id
                         WHERE post.post_type = "shop_order" AND post.post_status != "trash"  AND item.order_item_type="coupon" AND item.order_item_name="' . $order_coupon . '"LIMIT ' . $coupon_limit1 . ', ' . $cborderbycoupon_perpage;

        }

        $result_orders = $wpdb->get_results($sql, ARRAY_A);

        if (!empty($result_orders)) {

            foreach ($result_orders as $result_order) {
                $order = new WC_Order();
                $order->populate(get_post($result_order['ID']));
                $order_coupons = ($order->get_used_coupons());
                $status        = get_term_by('slug', $order->status, 'shop_order_status');
                $cbx_orders_output .= cborderbycoupon_prepare_order($order, $reference);

            }

        }

        if ($cborderbycoupon_show_this_user_coupon == 'on') {

            $sql = 'SELECT post.*, item.* FROM ' . $coupon_table1 . '  post
                        INNER JOIN ' . $coupon_table2 . ' item ON post.ID = item.order_id
                        INNER JOIN ' . $coupon_table3 . ' itemmeta ON post.ID = itemmeta.post_id
                        WHERE itemmeta.meta_key = "_customer_user" AND itemmeta.meta_value = "' . get_current_user_id() . '" AND post.post_type = "shop_order" AND post.post_status != "trash" AND item.order_item_type="coupon" AND item.order_item_name="' . $order_coupon . '"';

        } else {
            $sql = 'SELECT post.*, item.* FROM ' . $coupon_table1 . '  post
                        INNER JOIN ' . $coupon_table2 . ' item ON post.ID = item.order_id
                        WHERE  post.post_type = "shop_order" AND post.post_status != "trash" AND item.order_item_type="coupon" AND item.order_item_name="' . $order_coupon . '"';

        }

        $result_orders_count = count($wpdb->get_results($sql, ARRAY_A));

        if ($cborderbycoupon_perpage > 0)
            $total_pages = ceil($result_orders_count / $cborderbycoupon_perpage);

        if ((int)$total_pages > (int)$page_number) {

            $cbxnextorderbutton = '<a href = "#" data-order-coupon = "" data-ref = "' . $reference . '" data-busy = "0" class = "button cbxwooextendedorders_coupon_filter cbwoocommerce_order_coupon_readmore" data-next-page = "' . $page_number . '" data-type = "readmore">' . __('View More', 'cbxwooextendedorders') . '</a>';

        } else {

            $cbxnextorderbutton = '';
        }

        return array($cbx_orders_output, $cbxnextorderbutton);

    } // end of if order coupon !0
    else {

        if ($cborderbycoupon_show_this_user_coupon == 'on') {

            $cborderbycoupon_args = array(
                'posts_per_page' => $cborderbycoupon_perpage,
                'meta_key'       => '_customer_user',
                'meta_value'     => get_current_user_id(),
                'post_type'      => 'shop_order',
                'post_status'    => array_keys(wc_get_order_statuses()),
                'paged'          => $page_number
            );
        } else {

            $cborderbycoupon_args = array(
                'posts_per_page' => $cborderbycoupon_perpage,
                'post_type'      => 'shop_order',
                'post_status'    => array_keys(wc_get_order_statuses()),
                'paged'          => $page_number
            );
        }


        $cborderbycoupon_orders = new WP_Query($cborderbycoupon_args);
        ++$page_number;

        if ($cborderbycoupon_orders->max_num_pages >= $page_number) {

            $cbxnextorderbutton = '<a href = "#" data-order-coupon = "" data-ref = "' . $reference . '" data-busy = "0" class = "button cbxwooextendedorders_coupon_filter cbwoocommerce_order_coupon_readmore" data-next-page = "' . $page_number . '" data-type = "readmore">View More</a>';
        } else {

            $cbxnextorderbutton = '';
        }
        if ($cborderbycoupon_orders->have_posts()):

            while ($cborderbycoupon_orders->have_posts()): $cborderbycoupon_orders->the_post();

                $order = new WC_Order();
                $order->populate(get_post(get_the_ID()));
                $order_coupons = ($order->get_used_coupons());
                $status        = get_term_by('slug', $order->status, 'shop_order_status');

                $cbx_orders_output .= cborderbycoupon_prepare_order($order, $reference);


            endwhile;
        endif;
	    wp_reset_query(); // Restore

        return array($cbx_orders_output, $cbxnextorderbutton);
    }
    // if all orders

}


/**
 * @param $order
 * prepare order item row for table
 * @return string
 */

function cborderbycoupon_prepare_order($order, $ref = '')
{


    $cbxorderitems     = ($order->get_items());
    $item_count        = $order->get_item_count();
    $cbx_orders_output = '';
    $cbx_orders_output .= '<tr class="order">

                                <td class="order-number">
                                    <a href=" ' . $order->get_view_order_url() . '">
                                       ' . $order->get_order_number() . '
                                    </a>
                                </td>
                                <td class="order-date">
                                    <time datetime = "' . date('Y-m-d', strtotime($order->order_date)) . ' " title="' . esc_attr(strtotime($order->order_date)) . '">' . date_i18n(get_option('date_format'), strtotime($order->order_date)) . '</time>
                                </td>
                                <td class ="order-status" style="text-align:left; white-space:nowrap;">
                                    ' . ucfirst($order->status) . '
                                </td>
               ';
    if ($ref == 'shortcode') {

        $cbx_orders_output .= '<td class ="order-items">';
        $cbx_orders_output .= '<table class="cbxorderbycoupon-shop-item-table">';
        $cbxorderitems     = is_array($cbxorderitems) ? $cbxorderitems : array();

        $cbx_orders_output .= '<tr>
                                        <td>' . __('Name', 'cbxwooextendedorders') . '</td>

                                         <td>' . __('Qty', 'cbxwooextendedorders') . '</td>

                                         <td>' . __('Image', 'cbxwooextendedorders') . '</td>

                                        </tr><tbody>';
        foreach ($cbxorderitems as $cbxorderitem) {
            $cbx_orders_output .= '<tr>';

            $cbx_orders_output .= '<td>' . $cbxorderitem['name'] . '</td>' . '<td>' . $cbxorderitem['qty'] . '</td>';
            $feat_image = wp_get_attachment_url(get_post_thumbnail_id($cbxorderitem['product_id']));

            if ($feat_image != '' && is_string($feat_image)) {
                $cbx_orders_output .= '<td><img class="cbxorderbycoupon-shop-item-image" style = "border:none;width:100px!important;height:100px!important;" src = "' . $feat_image . '" alt ="Product"/></td>';
            } elseif (is_array($feat_image) && !empty($feat_image)) {
                $cbx_orders_output .= '<td><img class="cbxorderbycoupon-shop-item-image" style = "border:none;width:100px!important;height:100px!important;" src = "' . $feat_image[0] . '" alt ="Product"/></td>';
            }

            $cbx_orders_output .= '</tr>';
        }
        $cbx_orders_output .= '</tbody></table>';
        $cbx_orders_output .= '</td>';
    }

    $cbx_orders_output .= ' <td class ="order-total">
                                 ' . $order->get_formatted_order_total() . __(' for ', 'cbxwooextendedorders') . $item_count . __(' items', 'cbxwooextendedorders') . '
                            </td>';

    $cbx_orders_output .= '<td class ="order-actions">';

    $actions = array();

    if (in_array($order->status, apply_filters('woocommerce_valid_order_statuses_for_payment', array('pending', 'failed'), $order))) {

        $actions['pay'] = array(
            'url'  => $order->get_checkout_payment_url(),
            'name' => __('Pay', 'woocommerce')
        );
    }

    if (in_array($order->status, apply_filters('woocommerce_valid_order_statuses_for_cancel', array('pending', 'failed'), $order))) {

        $actions['cancel'] = array(
            'url'  => $order->get_cancel_order_url(get_permalink(wc_get_page_id('myaccount'))),
            'name' => __('Cancel', 'woocommerce')
        );
    }

    $actions['view'] = array(
        'url'  => $order->get_view_order_url(),
        'name' => __('View', 'woocommerce')
    );

    $actions = apply_filters('woocommerce_my_account_my_orders_actions', $actions, $order);

    if ($actions) {
        foreach ($actions as $key => $action) {
            $cbx_orders_output .= '<a href="' . esc_url($action['url']) . '" class="button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
        }
    }

    $cbx_orders_output .= ' </td>
                </tr>';

    return $cbx_orders_output;
}