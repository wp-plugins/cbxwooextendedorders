<?php

/**
 * My Orders *
 * Shows recent orders on the account page *
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$customer_orders = get_posts(
    apply_filters(
    'woocommerce_my_account_my_orders_query',
        array(
        'numberposts' => $order_count,
        'meta_key'    => '_customer_user',
        'meta_value'  => get_current_user_id(),
        'post_type'   => wc_get_order_types( 'view-orders' ),
        'post_status' => array_keys( wc_get_order_statuses() )
        )

    )
);

if ( $customer_orders ) {

// get global settings for this plugin
    $cborderbycoupon_setting_api    = get_option( 'cbxwooextendedorders_front_settings');
    $cborderbycoupon_userrole       = array();   //default

        if ( isset( $cborderbycoupon_setting_api['cborderbycoupon_usergroupfrontend'] ) ) {
            $cborderbycoupon_userrole =  $cborderbycoupon_setting_api['cborderbycoupon_usergroupfrontend'];
        }

    $cbpermitted_role_for_filtercoupon  = (array_intersect($cborderbycoupon_userrole, wp_get_current_user()->roles));
    $header                             = __("My Recent Orders" , 'cbxwooextendedorders');


?>

<div class = "cborderbycouponforwoocommerce_order_coupon_wrapper">

    <p class = "cborderbycouponforwoocommerce_coupon_filter_title">
         <h2><?php echo apply_filters( 'woocommerce_my_account_my_orders_title', $header);?></h2>
    </p>

    <p class = "cborderbycouponforwoocommerce_coupon_filter_noorder"></p>
        <?php
            // check curerent user has the desired role
            if (is_array($cbpermitted_role_for_filtercoupon) && !empty($cbpermitted_role_for_filtercoupon) ) {

                     ?>  <!--- filter html -->
                        <input type = "text" id = "cborderbycouponforwoocommerce_dropdown_order_coupon"
                               placeholder = "Enter coupon code">
                        <a href = "#" data-type = "filter" data-order-coupon = "" data-busy = "0" class = "button cborderbycouponforwoocommerce_coupon_filter cbwoocommerce_order_coupon_readmore" data-next-page = "1" ><?php _e('Filter','cbxwooextendedorders') ;?></a>

       <?php }// if no desired role ?>

    <table class = "cborderbycouponforwoocommerce_order_table shop_table my_account_orders">
        <thead>
            <tr>
                <th class = "order-number"><span class = "nobr"><?php _e('Order', 'cbxwooextendedorders'); ?></span></th>
                <th class = "order-date"><span class = "nobr"><?php _e('Date', 'woocommerce'); ?></span></th>
                <th class = "order-status"><span class = "nobr"><?php _e('Status', 'woocommerce'); ?></span></th>
                <th class = "order-total"><span class = "nobr"><?php _e('Total', 'woocommerce'); ?></span></th>
                <th class = "order-actions">&nbsp;</th>

            </tr>
        </thead>
        <tbody class = "cborderbycouponforwoocommerce_order_table_body">

            <?php
                // call function for orders of page number one ,here coupon code none so for all coupon set coupon code 0
                $cborderbycouponforwoocommerce_next_posts = cbxwoocommerce_order_by_coupon_read();
                echo $cborderbycouponforwoocommerce_next_posts[0];
            ?>

        </tbody>
    </table>
    <?php
        // view more button
        echo $cborderbycouponforwoocommerce_next_posts[1];
        // end of else
    ?>
</div>
<?php }

 else {  ?>

    <p class = "cborderbycouponforwoocommerce_coupon_filter_title">
        <h2><?php _e( "My Recent Orders" ,'cbxwooextendedorders') ;?></h2>
        <p><?php _e( 'You have no orders yet' ,'cbxwooextendedorders') ;?></p>
    </p>

<?php }