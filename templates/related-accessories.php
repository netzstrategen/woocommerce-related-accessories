<?php

namespace Netzstrategen\WooCommerceRelatedAccessories;

global $product, $post;

?>

<div class="related-accessories<?= $is_notice_template ? '' : '--summary' ?>">
  <?php if ($is_notice_template) : ?>
    <h3><?= __('Matching accessories - order now:', Plugin::L10N) ?></h3>
  <?php else: ?>
    <h3><?= __('Accessories & Extras', Plugin::L10N) ?></h3>
  <?php endif; ?>

  <select>
    <?php if ($is_notice_template) : ?>
      <option value="all"><?= __('All related products', Plugin::L10N) ?></option>
    <?php else: ?>
      <option value=""><?= __('Choose an option', 'woocommerce') ?></option>
    <?php endif; ?>
    <?php foreach ($related_accessories as $group_name => $accessories) : ?>
      <?php
        if (empty($accessories)) {
          continue;
        }
      ?>
      <?php if ($group_name !== 'all') : ?>
        <option value="<?= $group_name ?>"><?= __(ucfirst(str_replace('_', ' ', $group_name)), Plugin::L10N) ?></option>
      <?php endif; ?>
    <?php endforeach; ?>
  </select>

  <?php if (!$is_notice_template) : ?>
    <a id="reset_related_accessories" href="#" style="display: none;"><?= __('Reset accessories', Plugin::L10N) ?></a>
  <?php endif; ?>

  <?php foreach ($related_accessories as $group_name => $accessories) : ?>
    <?php
      if (empty($accessories)) {
        continue;
      }
    ?>
    <div class="slideshow-<?= $group_name ?> related-accessories__slideshow js-related-accessories-slider product-slider">
      <ul class="product-slider__products slides">
        <?php
        foreach ($accessories as $accessory) {
          $post = get_post($accessory->get_id());
          setup_postdata($post);
          ob_start();
          wc_get_template_part('content', 'product');
          $output = ob_get_clean();
          // Inject the required class and data attribute using an existing class in the product view link as position reference.
          $pattern = '@(<a.*class=".*)(woocommerce-loop-product__link)([^"]*)"([^>]*)>@';
          $replacement = sprintf('$1$2 yith-wcqv-button $3" data-product_id="%s" $4>', $accessory->get_id());
          echo preg_replace($pattern, $replacement, $output);
        }
        wp_reset_postdata();
        ?>
      </ul>
    </div>
  <?php endforeach; ?>
</div>
