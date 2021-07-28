<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceRelatedAccessories\Admin.
 */

namespace Netzstrategen\WooCommerceRelatedAccessories;

/**
 * Administrative back-end functionality.
 */
class Admin {

  /**
   * Displays a notice if woocommerce is not installed and active.
   *
   * @implements admin_notices
   */
  public static function admin_notices() {
    if (!is_plugin_active('advanced-custom-fields-pro/acf.php') || !class_exists('WooCommerce')) { ?>
      <div class="error below-h3">
        <p><strong><?php _e('WooCommerce Related Accessories plugin requires that ACF Pro, YITH Quick View and WooCommerce plugins are installed and active.', Plugin::L10N); ?></strong></p>
      </div>
    <?php }
  }

  public static function wp_ajax_related_accessories_backlinks() {
    global $wpdb;
    $params = array(
      'post_type' => ['product', 'product_variant'],
      'post_status' => 'any',
      'meta_query' => array(
        array('key' => 'field_group_related_accessories_care_products', //meta key name here
          'value' => '',//"245301"
          'compare' => 'LIKE',
        )
      ),
      'posts_per_page' => 5,
    );
    $wc_query = new \WP_Query($params);
    var_dump($wc_query->request);die('');
    global $post, $product;
    $result = [];
    if( $wc_query->have_posts() ) {
      while( $wc_query->have_posts() ) {
        $wc_query->the_post();
        $result[]=$wc_query->post_name;

      } // end while

    } // end if

/*
    $result = $wpdb->get_results("SELECT
    pm.*
FROM
    wp_posts p
        INNER JOIN
    wp_postmeta pm ON pm.post_id = p.ID
        AND pm.meta_key ='field_group_related_accessories_care_products'
        AND pm.meta_value LIKE '%\"245301\"%'
WHERE p.post_type IN ('product','product_variant')");
*/
    echo wp_json_encode($result);

    wp_die();
  }



}
