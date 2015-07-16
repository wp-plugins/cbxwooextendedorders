/**
 * Created by codeboxr on 9/29/14.
 */
jQuery(document).ready(function($){

    // show data in front end
    //cbcouponrefer_ajax_icon hide
    jQuery( ".cbcouponrefer_ajax_icon").hide();

    ///////read more button click
    $('.cbwoocommerce_order_coupon_readmore').click(function(e){

        e.preventDefault();
        var cborderbycoupon_busy             = $(this).attr('data-busy');
        var cborderbycoupon_type             = $(this).attr('data-type');
        var cborderbycoupon_ref              = $(this).attr('data-ref');

        if(cborderbycoupon_type == 'readmore'){

            var cborderbycoupon_nextpage         = $(this).attr('data-next-page');
            var cborderbycoupon_coupon           = $(this).attr('data-order-coupon');
            if(cborderbycoupon_busy == '0'){

                jQuery(".cbwoocommerce_order_coupon_readmore").attr("data-busy",'1');
                jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").addClass('cborderbycoupon_active');

                jQuery.ajax({
                    type     : "post",
                    dataType : "json",
                    url      : couponAjax.ajaxurl,
                    data     : {action: "cbxwooextendedorders_readmore" , cborderbycoupon_nextpage : cborderbycoupon_nextpage , cborderbycoupon_type: cborderbycoupon_type,cborderbycoupon_coupon : cborderbycoupon_coupon , cborderbycoupon_ref : cborderbycoupon_ref},
                    success  : function(data, textStatus, XMLHttpRequest){

                        if(data[1] == ''){

                            jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").hide();
                            // jQuery(".cborderbycouponforwoocommerce_coupon_filter_noorder").html('No more orders');

                        }
                            jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").attr("data-next-page",++cborderbycoupon_nextpage);
                            jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").attr("data-order-coupon",cborderbycoupon_coupon);
                            jQuery(".cborderbycouponforwoocommerce_order_table_body").append(data[0]);
                            jQuery(".cbwoocommerce_order_coupon_readmore").attr("data-busy",'0');
                            jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").removeClass('cborderbycoupon_active');
                    }// end of success
                });// end of ajax

            }// end of if not busy
        }
       else{
            //filter

            var cborderbycoupon_coupon           = $('#cborderbycouponforwoocommerce_dropdown_order_coupon').val();
            var cborderbycoupon_nextpage         = $(this).attr('data-next-page');

            if(cborderbycoupon_coupon == ''){
                alert('Please add coupon code');
                return false;
            }
            if(cborderbycoupon_busy == '0'){

                jQuery(".cbwoocommerce_order_coupon_readmore").attr("data-busy",'1');
                jQuery(".cbwoocommerce_order_coupon_readmore[data-type='filter']").addClass('cborderbycoupon_active');

                jQuery.ajax({
                    type     : "post",
                    dataType : "json",
                    url      : couponAjax.ajaxurl,
                    data     : {action: "cbxwooextendedorders_readmore" , cborderbycoupon_nextpage : cborderbycoupon_nextpage , cborderbycoupon_type: cborderbycoupon_type,cborderbycoupon_coupon : cborderbycoupon_coupon , cborderbycoupon_ref : cborderbycoupon_ref},
                    success  : function(data, textStatus, XMLHttpRequest){


                        if(data[0] != ''){
                            jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").show();
                            jQuery(".cborderbycouponforwoocommerce_order_table_body").html(data[0]);
                        }
                        else{
                            jQuery(".cborderbycouponforwoocommerce_order_table_body").html('No orders found');
                        }
                        if(data[1] == ''){

                            jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").hide();
                        }

                        jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").attr("data-next-page",2);            // }
                        jQuery(".cbwoocommerce_order_coupon_readmore[data-type='readmore']").attr("data-order-coupon",cborderbycoupon_coupon);
                        jQuery(".cbwoocommerce_order_coupon_readmore").attr("data-busy",'0');
                        jQuery(".cbwoocommerce_order_coupon_readmore[data-type='filter']").removeClass('cborderbycoupon_active');

                    }
                });
            } // if(cborderbycoupon_coupon == ''){

            }// if filter

    });// end of click

});//e nd of dom ready