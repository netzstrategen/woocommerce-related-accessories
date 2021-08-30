<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceRelatedAccessories\AdminBacklinks.
 */

namespace Netzstrategen\WooCommerceRelatedAccessories;

/**
 * Adds admin meta box showing backlinks on product edit screen.
 */
class AdminBacklinks {

  /**
   * Related accessories backlinks meta box content.
   *
   * @param $post
   */
  public static function related_accessories_backlinks_meta_box($post) {
    echo '<a id="related-accessories-backlinks-load" class="button">' . esc_html__('Load', Plugin::PREFIX) . '</a>';
  }

  /**
   * Enqueues styles and scripts.
   *
   * @implements admin_enqueue_scripts
   */
  public static function enqueueAssets() {
    wp_register_script('related-accessories-backlinks', Plugin::getBaseUrl() . '/dist/scripts/admin-backlinks.js', ['jquery'], FALSE, TRUE);
    wp_localize_script('related-accessories-backlinks', 'adminBackLinks', [
      'i18n' => [
        'Loading' => __('Loading', Plugin::PREFIX),
      ],
      'post' => get_the_ID()
    ]);
    wp_enqueue_script('related-accessories-backlinks');
  }

  /**
   * Related accessories backlinks meta box action.
   */
  public static function related_accessories_backlinks_meta_box_setup() {
    add_action('add_meta_boxes', __CLASS__ . '::related_accessories_backlinks_add_meta_box');
  }

  /**
   * Related accessories backlinks meta box configuration.
   */
  public static function related_accessories_backlinks_add_meta_box() {
    add_meta_box(
      'related-accessories-backlinks',
      __('Related Accessories Backlinks', Plugin::L10N),
      __CLASS__ . '::related_accessories_backlinks_meta_box',
      'product',
      'advanced',
      'default'
    );
  }

  /**
   * Returns products backlinking to a given accessory product.
   */
  public static function wp_ajax_related_accessories_backlinks() {
    global $wpdb;
    $post_id = intval($_POST['post']);
    $field_names = wc_list_pluck(acf_get_local_fields('field_group_related_accessories'), 'name');
    $meta_queries = [];
    foreach ($field_names as $field_name) {
      $meta_queries[] = [
        'key' => 'field_group_related_accessories_' . $field_name,
        'value' => '"' . $post_id . '"',
        'compare' => 'LIKE',
      ];
    }
    if ($post_id) {
      $params = [
        'post_type' => ['product', 'product_variant'],
        'post_status' => 'any',
        'meta_query' => [
          'relation' => 'OR',
          $meta_queries,
        ],
        'posts_per_page' => -1,
      ];
      $wc_query = new \WP_Query($params);
      if ($wc_query->have_posts()) {
        $html = '<ol>';
        while ($wc_query->have_posts()) {
          $wc_query->the_post();
          $html .= '<li><a href="' . get_edit_post_link($wc_query->post) . '">';
          $html .= esc_html($wc_query->post->post_title);
          $html .= '</a></li>';
        }
        $html .= '</ol>';
      }
      else {
        $html = __('No results', Plugin::PREFIX);
      }
    }
    echo $html;
    wp_die();
  }

}
