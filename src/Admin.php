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

}
