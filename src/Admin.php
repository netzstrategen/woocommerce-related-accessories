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

  /**
   * Returns products backlinking to a given accessory product.
   */
  public static function wp_ajax_related_accessories_backlinks() {
    global $wpdb;
    $postId = intval($_POST['post']);
    $result = [];
    if($postId)
    {
      $params = array(
        'post_type' => ['product', 'product_variant'],
        'post_status' => 'any',
        'meta_query' => array(
          array('key' => 'field_group_related_accessories_care_products', //meta key name here
            'value' => '"' . $postId . '"',
            'compare' => 'LIKE',

          )
        ),
        'posts_per_page' => -1,
      );
      $wc_query = new \WP_Query($params);
      global $post, $product;
      if( $wc_query->have_posts() ) {
        while( $wc_query->have_posts() ) {
          $wc_query->the_post();
          $result[]=$wc_query->post->post_name;
        }
      }
    }
    echo wp_json_encode($result);
    wp_die();
  }

  public static function related_accessories_backlinks_meta_box_setup() {
    add_action( 'add_meta_boxes', __CLASS__ . '::related_accessories_backlinks_add_meta_box' );
  }

  public static function related_accessories_backlinks_add_meta_box() {
    add_meta_box(
      'smashing-post-class',      // Unique ID
      esc_html__( 'Related accessories backlinks', 'example' ),    // Title
      __CLASS__ . '::smashing_post_class_meta_box',   // Callback function
      'product',         // Admin page (or post type)
      'side',         // Context
      'default'         // Priority
    );
  }
  public static function smashing_post_class_meta_box( $post ) {
    return "Get related accessories";
  }
}
