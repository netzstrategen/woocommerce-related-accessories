<?php

/**
 * @file
 * Contains \Netzstrategen\WooCommerceRelatedAccessories\WooCommerce.
 */

namespace Netzstrategen\WooCommerceRelatedAccessories;

/**
 * WooCommerce related functionality.
 */
class WooCommerce {

  /**
   * @var boolean
   */
  private static $isAddedToCart;

  /**
   * Displays related accessories after single product summary.
   *
   * @implements woocommerce_single_product_summary
   */
  public static function woocommerce_single_product_summary() {
    if ($related_accessories_ids = static::getRelatedAccessoriesIds()) {
      $related_accessories = static::buildRelatedProductsView($related_accessories_ids);
      Plugin::renderTemplate(['templates/related-accessories.php'], [
        'fields_labels' => wc_list_pluck(acf_get_local_fields('field_group_related_accessories'), 'label'),
        'related_accessories' => $related_accessories,
        'is_notice_template' => FALSE,
      ]);
    }
  }

  /**
   * Checks if product has been added to the cart.
   *
   * We need to know if current product has been added to the cart to decide
   * if its related accessories should be displayed before the single product
   * content.
   *
   * @implements woocommerce_add_to_cart
   */
  public static function woocommerce_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    static::$isAddedToCart = TRUE;
  }

  /**
   * Displays product related accessories before single product content.
   *
   * @implements woocommerce_before_single_product
   */
  public static function woocommerce_before_single_product() {
    if (!static::$isAddedToCart || !$related_accessories_ids = static::getRelatedAccessoriesIds()) {
      return;
    }
    static::renderRelatedAccessories($related_accessories_ids);
  }

  /**
   * Renders the related accessories.
   *
   * @param array $related_accessories_ids
   *   (optional) The product IDs of the related accessories to render. If omitted,
   *   the related accessories of the currently output product are output.
   */
  public static function renderRelatedAccessories(?array $related_accessories_ids = NULL) {
    if (!isset($related_accessories_ids)) {
      $related_accessories_ids = static::getRelatedAccessoriesIds();
    }
      return;
    }
    $related_accessories = static::buildRelatedProductsView($related_accessories_ids);
    Plugin::renderTemplate(['templates/related-accessories.php'], [
      'fields_labels' => wc_list_pluck(acf_get_local_fields('field_group_related_accessories'), 'label'),
      'related_accessories' => $related_accessories,
      'is_notice_template' => TRUE,
    ]);
  }

  /**
   * Retrieves product related accessories IDs.
   *
   * @return array
   *   List of related accessories IDs.
   */
  public static function getRelatedAccessoriesIds() {
    $related_accessories_ids = array_filter(get_field('field_group_related_accessories') ?: []);
    return apply_filters(Plugin::PREFIX . '/get_related_accessories_ids', $related_accessories_ids);
  }

  /**
   * Builds product related accessories list.
   *
   * We are retrieving products IDs from ACF and flatten them to run a single
   * query that loads the product objects. Then we are building an array that
   * pairs related accessories categories with the respective products.
   *
   * @return array
   *   List of related accessories.
   */
  public static function buildRelatedProductsView($related_accessories_ids) {
    $products_ids = [];
    array_walk_recursive($related_accessories_ids, function ($product_id) use (&$products_ids) {
      if ($product_id) {
        $products_ids[] = $product_id;
      }
    });
    if (!count($products_ids)) {
      return [];
    }

    $products = wc_get_products([
      'posts_per_page' => -1,
      'post_type' => 'product',
      'include' => $products_ids,
      'orderby' => 'post__in',
      'post_status' => 'publish',
    ]);

    if (!$products) {
      return [];
    }

    $related_accessories = $related_accessories_ids;
    $related_accessories['all'] = [];
    foreach ($related_accessories_ids as $key => $value) {
      if (empty($value)) {
        continue;
      }
      $related_accessories[$key] = [];
      foreach ($products as $product) {
        $product_id = $product->get_id();
        if (in_array($product_id, $value)) {
          $related_accessories[$key][] = $product;
          $related_accessories['all'][] = $product;
        }
      }
    }
    return $related_accessories;
  }

}
