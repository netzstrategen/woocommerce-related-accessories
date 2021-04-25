<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceRelatedAccessories\Plugin.
 */

namespace Netzstrategen\WooCommerceRelatedAccessories;

/**
 * Main front-end functionality.
 */
class Plugin {

  /**
   * Prefix for naming.
   *
   * @var string
   */
  const PREFIX = 'related-accessories';

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
    if (function_exists('register_field_group')) {
      if (SKIN === 'wopa') {
        static::register_wopa_acf();
      }
      else {
        static::register_lart_acf();
      }
      add_filter('acf/fields/relationship/query/key=field_pads_and_pillows', __CLASS__ . '::acf_relationship_filter');
      add_filter('acf/fields/relationship/query/key=field_covers', __CLASS__ . '::acf_relationship_filter');
      add_filter('acf/fields/relationship/query/key=field_care_products', __CLASS__ . '::acf_relationship_filter');
      add_filter('acf/fields/relationship/query/key=field_spare_parts', __CLASS__ . '::acf_relationship_filter');
      add_filter('acf/fields/relationship/query/key=field_other_accessories', __CLASS__ . '::acf_relationship_filter');
    }

    if (is_admin()) {
      return;
    }

    // Enqueues styles and scripts.
    add_action('wp_enqueue_scripts', __CLASS__ . '::enqueueAssets', 100);

    // Displays product related accessories as a notice when it is added to the cart.
    add_action('woocommerce_add_to_cart', __NAMESPACE__ . '\WooCommerce::woocommerce_add_to_cart', 20, 6);

    // Displays product related accessories.
    add_action('woocommerce_single_product_summary', __NAMESPACE__ . '\WooCommerce::woocommerce_single_product_summary', 45);
    add_action('woocommerce_before_single_product', __NAMESPACE__ . '\WooCommerce::woocommerce_before_single_product');
  }

  /**
   * Registers custom fields for WOPA.
   */
  public static function register_wopa_acf() {
    acf_add_local_field_group([
      'key' => 'group_related_accessories',
      'title' => __('Related Accessories', Plugin::L10N),
      'fields' => [
        [
          'key' => 'field_group_related_accessories',
          'name' => 'field_group_related_accessories',
          'type' => 'group',
          'sub_fields' => [
            [
              'key' => 'field_zubehoer',
              'label' => __('Zubehör', Plugin::L10N),
              'name' => 'zubehoer',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:zubehoer',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_pflegemittel',
              'label' => __('Pflegemittel', Plugin::L10N),
              'name' => 'pflegemittel',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:pflegemittel',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_accessoires_und_deko',
              'label' => __('Accessoires & Deko', Plugin::L10N),
              'name' => 'accessoires-und-deko',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:accessoires-und-deko',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
          ],
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
   * Registers custom fields for LART.
   */
  public static function register_lart_acf() {
    acf_add_local_field_group([
      'key' => 'group_related_accessories',
      'title' => __('Related Accessories', Plugin::L10N),
      'fields' => [
        [
          'key' => 'field_group_related_accessories',
          'name' => 'field_group_related_accessories',
          'type' => 'group',
          'sub_fields' => [
            [
              'key' => 'field_leuchtmittel',
              'label' => __('Leuchtmittel', Plugin::L10N),
              'name' => 'leuchtmittel',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:leuchtmittel',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_led_lampen',
              'label' => __('LED-Lampen', Plugin::L10N),
              'name' => 'led-lampen',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:led-lampen',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_leuchtenzubehoer',
              'label' => __('Leuchtenzubehör', Plugin::L10N),
              'name' => 'leuchtenzubehoer',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:leuchtenzubehoer',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_zubehoer',
              'label' => __('Zubehoer', Plugin::L10N),
              'name' => 'zubehoer',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:zubehoer',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_zubehoer_occhio',
              'label' => __('Zubehör Occhio', Plugin::L10N),
              'name' => 'zubehoer-occhio',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:zubehoer-occhio',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_zubehoer_top_light',
              'label' => __('Zubehör Top Light', Plugin::L10N),
              'name' => 'zubehoer-top-light',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:zubehoer-top-light',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_zubehoer_bopp',
              'label' => __('Zubehör Bopp', Plugin::L10N),
              'name' => 'zubehoer-bopp',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:zubehoer-bopp',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_baldachine',
              'label' => __('Baldachine', Plugin::L10N),
              'name' => 'baldachine',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:baldachine',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
            [
              'key' => 'field_leuchtenschirme',
              'label' => __('Leuchtenschirme', Plugin::L10N),
              'name' => 'leuchtenschirme',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:leuchtenschirme',
              ],
              'filters' => [
                0 => 'search',
                1 => 'post_type',
                2 => 'taxonomy',
              ],
              'elements' => [
                0 => 'featured_image',
              ],
              'return_format' => 'id',
            ],
          ],
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
