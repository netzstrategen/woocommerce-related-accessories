<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceRelatedAccessories\Plugin.
 */

namespace Netzstrategen\WooCommerceRelatedAccessories;

use Netzstrategen\WooCommerceRelatedAccessories\Helper;

/**
 * Main front-end functionality.
 */
class Plugin {

  /**
   * Prefix for naming.
   *
   * @var string
   */
  const PREFIX = 'woocommerce-related-accessories';

  /**
   * Gettext localization domain.
   *
   * @var string
   */
  const L10N = self::PREFIX;

  /**
   * @var string
   */
  private static $baseUrl;

  /**
   * @implements init
   */
  public static function preInit() {
    // Removes quick view button.
    add_filter('yith_add_quick_view_button_html', '__return_false');
  }

  /**
   * @implements init
   */
  public static function init() {
    if (function_exists('register_field_group') && defined('MOEVE_SHOP')) {
      // Determine fields based on site.
      if (MOEVE_SHOP === 'GACO') {
        $fields = [
          ['pads_and_pillows', 'Pads and pillows', TRUE, 'auflagen-kissen'],
          ['covers', 'Covers', TRUE, 'schutzhuellen'],
          ['care_products', 'Care products', TRUE, 'pflegemittel'],
          ['spare_parts', 'Spare parts', TRUE, 'ersatzteile'],
          // No field_ in name.
          ['parasol_accessories', 'Parasol accessories', FALSE, 'sonnenschirm-zubehoer'],
          // No field_ in name.
          ['beach_chair_accessories', 'Beach chair accessories', FALSE, 'strandkorb-zubehoer'],
          ['other_accessories', 'Other accessories', TRUE, 'sonstiges-zubehoer']
        ];
      }
      elseif (MOEVE_SHOP === 'WOPA') {
        $fields = [
          ['zubehoer', 'Zubehör', TRUE, 'zubehoer'],
          ['pflegemittel', 'Pflegemittel', TRUE, 'pflegemittel'],
          ['accessoires_und_deko', 'Accessoires & Deko', TRUE, 'accessoires-und-deko']
        ];
      }
      elseif (MOEVE_SHOP === 'LART') {
        $fields = [
          ['leuchtmittel', 'Leuchtmittel', TRUE, 'leuchtmittel'],
          ['led_lampen', 'LED-Lampen', TRUE, 'led-lampen'],
          ['leuchtenzubehoer', 'Leuchtenzubehör', TRUE, 'leuchtenzubehoer'],
          ['zubehoer', 'Zubehoer', TRUE, 'zubehoer'],
          ['zubehoer_occhio', 'Zubehör Occhio', TRUE, 'zubehoer-occhio'],
          ['zubehoer_top_light', 'Zubehör Top Light', TRUE, 'zubehoer-top-light'],
          ['zubehoer_bopp', 'Zubehör Bopp', TRUE, 'zubehoer-bopp'],
          ['baldachine', 'Baldachine', TRUE, 'baldachine'],
          ['leuchtenschirme', 'Leuchtenschirme', TRUE, 'leuchtenschirme']
        ];
      }
      elseif (MOEVE_SHOP === 'MIA') {
        $fields = [
          ['gartenmoebel', 'Gartenmöbel', TRUE, 'gartenmoebel'], 
          ['leuchtmittel', 'Leuchtmittel', TRUE, 'leuchtmittel'],        
          ['leuchtenzubehoer', 'Leuchtenzubehör', TRUE, 'leuchtenzubehoer'],
          ['zubehoer', 'Zubehoer', TRUE, 'zubehoer'],
          ['plus-produkt', 'Plus-Produkt', TRUE, 'plus-produkt'],
          ['sale', 'Sale', TRUE, 'sale'],
          ['sets', 'Sets', TRUE, 'sets']
        ];
      }
      else {
        // No acf registered.
      }
      // Pass fields based on site to register_acf function.
      if (isset($fields)) {
        static::register_acf($fields);
      }

      add_filter('acf/fields/relationship/query/key=field_pads_and_pillows', __CLASS__ . '::acf_relationship_filter');
      add_filter('acf/fields/relationship/query/key=field_covers', __CLASS__ . '::acf_relationship_filter');
      add_filter('acf/fields/relationship/query/key=field_care_products', __CLASS__ . '::acf_relationship_filter');
      add_filter('acf/fields/relationship/query/key=field_spare_parts', __CLASS__ . '::acf_relationship_filter');
      add_filter('acf/fields/relationship/query/key=field_other_accessories', __CLASS__ . '::acf_relationship_filter');
    }

    // Add admin meta box showing backlinks on product edit screen.
    add_action('wp_ajax_related_accessories_backlinks', __NAMESPACE__ . '\AdminBacklinks::wp_ajax_related_accessories_backlinks');
    add_action('load-post.php', __NAMESPACE__ . '\AdminBacklinks::related_accessories_backlinks_meta_box_setup');
    add_action('admin_enqueue_scripts', __NAMESPACE__ . '\AdminBacklinks::enqueueAssets');

    if (is_admin()) {
      return;
    }

    // Enqueues styles and scripts.
    add_action('wp_enqueue_scripts', __CLASS__ . '::enqueueAssets', 100);

    // Displays product related accessories as a notice when it is added to the cart.
    add_action('woocommerce_add_to_cart', __NAMESPACE__ . '\WooCommerce::woocommerce_add_to_cart', 20, 6);

    // Displays product related accessories.
    add_action('woocommerce_single_product_summary', __NAMESPACE__ . '\WooCommerce::woocommerce_single_product_summary', 45);
    add_shortcode('related_accessories', __NAMESPACE__ . '\WooCommerce::woocommerce_single_product_summary');
    add_action('woocommerce_before_single_product', __NAMESPACE__ . '\WooCommerce::woocommerce_before_single_product');

    // GraphQL support.
    if (is_plugin_active('wp-graphql/wp-graphql.php') && is_plugin_active('wp-graphql-woocommerce/wp-graphql-woocommerce.php')) {
      add_action('graphql_register_types', __NAMESPACE__ . '\GraphQL::graphql_register_types');
    }

  }

  /**
   * Registers custom fields.
   */
  public static function register_acf(array $fields) {
    $fieldParameters = [];
    foreach ($fields as $field) {
      list($name, $label, $field, $taxonomy) = $field;
      $fieldParameters[] = [
        'key' => $field ? 'field_' . strtr($name, '-', '_') : $name,
        'label' => __($label, Plugin::L10N),
        'name' => $name,
        'type' => 'relationship',
        'post_type' => ['product'],
        'taxonomy' => ['product_cat:' . $taxonomy],
        'filters' => ['search', 'post_type', 'taxonomy'],
        'elements' => ['featured_image'],
        'return_format' => 'id',
      ];
    }
    acf_add_local_field_group([
      'key' => 'group_related_accessories',
      'title' => __('Related Accessories', Plugin::L10N),
      'fields' => [
        [
          'key' => 'field_group_related_accessories',
          'name' => 'field_group_related_accessories',
          'type' => 'group',
          'sub_fields' => $fieldParameters,
        ],
      ],
      'location' => [
        [
          [
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'product',
          ],
        ],
      ],
    ]);
  }

  /**
   * @implements acf/fields/relationship/query/name=acf_related_posts
   */
  public static function acf_relationship_filter(array $options) {
    $options['post_status'] = ['publish'];
    return $options;
  }

  /**
   * Enqueues styles and scripts.
   *
   * @implements wp_enqueue_scripts
   */
  public static function enqueueAssets() {
    if (!is_product()) {
      return;
    }
    wp_enqueue_style('related-accessories/custom', static::getBaseUrl() . '/dist/styles/main.css', FALSE);
    wp_enqueue_script('related-accessories/custom', static::getBaseUrl() . '/dist/scripts/main.js', ['jquery'], FALSE, TRUE);
  }

  /**
   * Renders a given plugin template, optionally overridden by the theme.
   *
   * WordPress offers no built-in function to allow plugins to render templates
   * with custom variables, respecting possibly existing theme template overrides.
   * Inspired by Drupal (5-7).
   *
   * @param array $template_subpathnames
   *   An prioritized list of template (sub)pathnames within the plugin/theme to
   *   discover; the first existing wins.
   * @param array $variables
   *   An associative array of template variables to provide to the template.
   *
   * @throws \InvalidArgumentException
   *   If none of the $template_subpathnames files exist in the plugin itself.
   */
  public static function renderTemplate(array $template_subpathnames, array $variables = []) {
    $template_pathname = locate_template($template_subpathnames, FALSE, FALSE);
    extract($variables, EXTR_SKIP | EXTR_REFS);
    if ($template_pathname !== '') {
      include $template_pathname;
    }
    else {
      while ($template_pathname = current($template_subpathnames)) {
        if (file_exists($template_pathname = static::getBasePath() . '/' . $template_pathname)) {
          include $template_pathname;
          return;
        }
        next($template_subpathnames);
      }
      throw new \InvalidArgumentException("Missing template '$template_pathname'");
    }
  }

  /**
   * Generates a version out of the current commit hash.
   *
   * @return string
   *   Git version number.
   */
  public static function getGitVersion() {
    $git_version = NULL;
    if (is_dir(ABSPATH . '.git')) {
      $ref = trim(file_get_contents(ABSPATH . '.git/HEAD'));
      if (strpos($ref, 'ref:') === 0) {
        $ref = substr($ref, 5);
        if (file_exists(ABSPATH . '.git/' . $ref)) {
          $ref = trim(file_get_contents(ABSPATH . '.git/' . $ref));
        }
        else {
          $ref = substr($ref, 11);
        }
      }
      $git_version = substr($ref, 0, 8);
    }
    return $git_version;
  }

  /**
   * Loads the plugin textdomain.
   */
  public static function loadTextdomain() {
    load_plugin_textdomain(static::L10N, FALSE, static::L10N . '/languages/');
  }

  /**
   * The base URL path to this plugin's folder.
   *
   * Uses plugins_url() instead of plugin_dir_url() to avoid a trailing slash.
   */
  public static function getBaseUrl() {
    if (!isset(static::$baseUrl)) {
      static::$baseUrl = plugins_url('', static::getBasePath() . '/plugin.php');
    }
    return static::$baseUrl;
  }

  /**
   * The absolute filesystem base path of this plugin.
   *
   * @return string
   *   Plugin base directory name.
   */
  public static function getBasePath() {
    return dirname(__DIR__);
  }

}
