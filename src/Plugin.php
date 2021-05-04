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
      if (MOEVE_SHOP === 'GACO') {
        static::register_gaco_acf();
      }
      elseif (MOEVE_SHOP === 'WOPA') {
        static::register_wopa_acf();
      }
      elseif (MOEVE_SHOP === 'LART') {
        static::register_lart_acf();
      }
      else {
        // No acf registered. TO DO: Combine these to avoid skins.
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
   * Registers custom fields.
   */
  public static function register_gaco_acf() {
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
              'key' => 'field_pads_and_pillows',
              'label' => __('Pads and pillows', Plugin::L10N),
              'name' => 'pads_and_pillows',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:auflagen-kissen',
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
              'key' => 'field_covers',
              'label' => __('Covers', Plugin::L10N),
              'name' => 'covers',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:schutzhuellen',
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
              'key' => 'field_care_products',
              'label' => __('Care products', Plugin::L10N),
              'name' => 'care_products',
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
              'key' => 'field_spare_parts',
              'label' => __('Spare parts', Plugin::L10N),
              'name' => 'spare_parts',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:ersatzteile',
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
              'key' => 'parasol_accessories',
              'label' => __('Parasol accessories', Plugin::L10N),
              'name' => 'parasol_accessories',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:sonnenschirm-zubehoer',
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
              'key' => 'beach_chair_accessories',
              'label' => __('Beach chair accessories', Plugin::L10N),
              'name' => 'beach_chair_accessories',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:strandkorb-zubehoer',
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
              'key' => 'field_other_accessories',
              'label' => __('Other accessories', Plugin::L10N),
              'name' => 'other_accessories',
              'type' => 'relationship',
              'post_type' => [
                0 => 'product',
              ],
              'taxonomy' => [
                0 => 'product_cat:sonstiges-zubehoer',
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
