
<div class="wrap">

<div id="icon-options-general" class="icon32"></div>
    <?php echo '<h2>' . __( 'CBX Woo Extanded Orders', 'cbxwooextendedorders' ) . '</h2>';?>

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">

                    <div class="postbox">

                        <?php echo '<h3>' . __( 'Coupon Filter For Orders Settings', 'cbxwooextendedorders' ) . '</h3>';?>

                        <div class="inside">
                            <?php
                                $settings_api->show_navigation();
                                $settings_api->show_forms();
                            ?>
                        </div> <!-- .inside -->

                    </div> <!-- .postbox -->

                </div> <!-- .meta-box-sortables .ui-sortable -->

            </div> <!-- post-body-content -->

            <!-- sidebar -->
            <div id="postbox-container-1" class="postbox-container">

                <div class="meta-box-sortables">

                    <div class="postbox">

                        <?php echo '<h3>' . __( 'About CBX', 'cbxwooextendedorders' ) . '</h3>';?>
                        <div class="inside">
	                        <div class="postbox">
		                        <h3>Plugin Info</h3>

		                        <div class="inside">
			                        <p>Name : CBX Woo Extended Order Display <?php echo 'v' . CBX_EXTENDEDORDERS_VERSION; ?></p>
			                        <p>
				                        Author:
				                        <a href="http://wpboxr.com/product/cbx-woocommerce-extended-order-display" target="_blank">WPBoxr Team</a>
			                        </p>
			                        <p>Email : <a href="mailto:info@wpboxr.com" target="_blank">info@wpboxr.com</a></p>

			                        <p>Contact : <a href="http://wpboxr.com/contact-us" target="_blank">Contact Us</a></p>
		                        </div>
	                        </div>

	                        <div class="postbox">
		                        <h3><?php _e('Help & Supports','cbxwooextendedorders'); ?></h3>
		                        <div class="inside">
			                        <p>Support: <a href="http://wpboxr.com/contact-us" target="_blank">Contact Us</a></p>
			                        <p><i class="icon-envelope"></i> <a href="mailto:info@wpboxr.com">info@wpboxr.com</a></p>
			                        <p><i class="icon-phone"></i> <a href="tel:008801717308615">+8801717308615</a></p>
		                        </div>
	                        </div>
	                        <div class="postbox">
		                        <h3><?php _e('WPBoxr Other Plugins','cbxwooextendedorders'); ?></h3>
		                        <div class="inside">
			                        <?php

			                        include_once(ABSPATH . WPINC . '/feed.php');
			                        if (function_exists('fetch_feed')) {
				                        $feed = fetch_feed('http://wpboxr.com/feed?post_type=product');
				                        // $feed = fetch_feed('http://feeds.feedburner.com/wpboxr'); // this is the external website's RSS feed URL
				                        if (!is_wp_error($feed)) : $feed->init();
					                        $feed->set_output_encoding('UTF-8'); // this is the encoding parameter, and can be left unchanged in almost every case
					                        $feed->handle_content_type(); // this double-checks the encoding type
					                        $feed->set_cache_duration(21600); // 21,600 seconds is six hours
					                        $limit = $feed->get_item_quantity(6); // fetches the 18 most recent RSS feed stories
					                        $items = $feed->get_items(0, $limit); // this sets the limit and array for parsing the feed

					                        $blocks = array_slice($items, 0, 6); // Items zero through six will be displayed here
					                        echo '<ul>';
					                        foreach ($blocks as $block) {
						                        $url    = $block->get_permalink();
						                       /* $id     = $block->get_id();
						                        $id     = str_replace('amp;','', $id);
						                        //var_dump($id);
						                        $id     = parse_url($id);
						                        $id     = $id['query'];
						                       // var_dump($id);
						                        parse_str($id, $ids);
						                        $id = $ids['p'];*/
						                        //var_dump($id);
						                        echo '<li style="clear:both;  margin-bottom:5px;"><a target="_blank" href="' . $url . '">';
						                        //echo '<img style="float: left; display: inline; width:70px; height:70px; margin-right:10px;" src="http://wpboxr.com/wp-content/uploads/productshots/'.$id.'-profile.png" alt="wpboxrplugins" />';
						                        echo '<strong>' . $block->get_title() . '</strong></a></li>';
					                        }//end foreach
					                        echo '</ul>';


				                        endif;
			                        }
			                        ?>
		                        </div>
	                        </div>
	                        <div class="postbox">
		                        <h3><?php _e('WPBoxr Latest Updates','cbxwooextendedorders'); ?></h3>
		                        <div class="inside">
			                        <?php

			                        include_once(ABSPATH . WPINC . '/feed.php');
			                        if (function_exists('fetch_feed')) {
				                        $feed = fetch_feed('http://wpboxr.com/feed');
				                        // $feed = fetch_feed('http://feeds.feedburner.com/wpboxr'); // this is the external website's RSS feed URL
				                        if (!is_wp_error($feed)) : $feed->init();
					                        $feed->set_output_encoding('UTF-8'); // this is the encoding parameter, and can be left unchanged in almost every case
					                        $feed->handle_content_type(); // this double-checks the encoding type
					                        $feed->set_cache_duration(21600); // 21,600 seconds is six hours
					                        $limit = $feed->get_item_quantity(6); // fetches the 18 most recent RSS feed stories
					                        $items = $feed->get_items(0, $limit); // this sets the limit and array for parsing the feed

					                        $blocks = array_slice($items, 0, 6); // Items zero through six will be displayed here
					                        echo '<ul>';
					                        foreach ($blocks as $block) {
						                        $url = $block->get_permalink();
						                        echo '<li><a target="_blank" href="' . $url . '">';
						                        echo '<strong>' . $block->get_title() . '</strong></a></li>';
					                        }//end foreach
					                        echo '</ul>';


				                        endif;
			                        }
			                        ?>
		                        </div>
	                        </div>
	                        <div class="postbox">
		                        <div class="inside">
			                        <h3><?php _e('wpboxr Networks','cbxwooextendedorders') ?></h3>
			                        <p><?php _e('Html, Wordpress & Joomla Themes','cbxwooextendedorders') ?></p>
			                        <a target="_blank" href="http://themeboxr.com"><img src="http://themeboxr.com/wp-content/themes/themeboxr/images/themeboxr-logo-rect.png" style="max-width: 100%;" alt="themeboxr" title="Themeboxr - useful themes"  /></a>
			                        <br/>
			                        <p><?php _e('Wordpress Plugins','cbxwooextendedorders') ?></p>
			                        <a target="_blank" href="http://wpboxr.com"><img src="http://wpboxr.com/wp-content/themes/themeboxr/images/wpboxr-logo-rect.png" style="max-width: 100%;" alt="wpboxr" title="WPBoxr - Wordpress Extracts"  /></a>
			                        <br/><br/>
			                        <p>Joomla Extensions</p>
			                        <a target="_blank" href="http://joomboxr.com"><img src="http://joomboxr.com/wp-content/themes/themeboxr/images/joomboxr-logo-rect.png" style="max-width: 100%;" alt="joomboxr" title="Joomboxr - Joomla Extracts"  /></a>

		                        </div>
	                        </div>
	                        <div class="postbox">
		                        <h3><?php _e('WPBoxr on facebook','cbxwooextendedorders') ?></h3>
		                        <div class="inside">
			                        <iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fwpboxr&amp;width=260&amp;height=258&amp;show_faces=true&amp;colorscheme=light&amp;stream=false&amp;border_color&amp;header=false&amp;appId=558248797526834" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:260px; height:258px;" allowTransparency="true"></iframe>
		                        </div>
	                        </div>

                        </div> <!-- .inside -->

                    </div> <!-- .postbox -->

                </div> <!-- .meta-box-sortables -->

            </div> <!-- #postbox-container-1 .postbox-container -->

        </div> <!-- #post-body .metabox-holder .columns-2 -->

        <br class="clear">
    </div> <!-- #poststuff -->

</div> <!-- .wrap -->






