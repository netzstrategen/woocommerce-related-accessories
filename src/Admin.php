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
    $html = '<ol>';
    if($postId)
    {
      $params = [
        'post_type' => ['product', 'product_variant'],
        'post_status' => 'any',
        'meta_query' => [
          ['key' => 'field_group_related_accessories_care_products',
            'value' => '"' . $postId . '"',
            'compare' => 'LIKE',
          ],
        ],
        'posts_per_page' => -1,
      ];
      $wc_query = new \WP_Query($params);
      global $post, $product;
      if ($wc_query->have_posts()) {
        while ($wc_query->have_posts()) {
          $wc_query->the_post();
          $html .= '<li><a href="'.get_edit_post_link($wc_query->post).'">';
          $html .= esc_html($wc_query->post->post_title);
          $html .= '</a></li>';
        }
        $html .= '</ol>';
      }
      else $html = 'No results';
    }
    echo $html;
    wp_die();
  }

  /**
   * Related accessories backlinks meta box action.
   */
  public static function related_accessories_backlinks_meta_box_setup() {
    add_action( 'add_meta_boxes', __CLASS__ . '::related_accessories_backlinks_add_meta_box' );
  }

  /**
   * Related accessories backlinks meta box configuration.
   */
  public static function related_accessories_backlinks_add_meta_box() {
    add_meta_box(
      'related-accessories-backlinks',
      esc_html__( 'Related Accessories', 'woocommerce-related-accessories' ).' backlinks',
      __CLASS__ . '::related_accessories_backlinks_meta_box',
      'product',
      'advanced',
      'default'
    );
  }

  /**
   * Related accessories backlinks meta box content.
   * @param $post
   */
  public static function related_accessories_backlinks_meta_box( $post ) {
    echo '<a id="related_accessories_backlinks_load" class="button">'.esc_html__('Load', 'woocommerce-related-accessories').'</a>';
  }

  /**
   * Related accessories backlinks load button javascript.
   */
  public static function related_accessories_backlinks_js() { ?>
  <script type="text/javascript" >
    jQuery(document).ready(function($) {
      $('#related_accessories_backlinks_load').on('click', (e) => {
        $('#related-accessories-backlinks > .inside').html('<?= __('Loading', 'woocommerce-related-accessories'); ?>');
        e.preventDefault();
        $.post(window.ajaxurl, {'action':'related_accessories_backlinks',
            'post':'<?= get_the_ID(); ?>'},
          function(response) {
            $('#related-accessories-backlinks > .inside').html(response);
          });
      });
    });
  </script><?php
}

}
