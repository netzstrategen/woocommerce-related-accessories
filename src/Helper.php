<?php

namespace Netzstrategen\WooCommerceRelatedAccessories;

class Helper {

  public static function getFieldParameters(string $name, string $label): array {
    return [
      'key' => 'field_' . strtr($name, '-', '_'),
      'label' => __($label, Plugin::L10N),
      'name' => $name,
      'type' => 'relationship',
      'post_type' => ['product'],
      'taxonomy' => ['product_cat:' . $name],
      'filters' => ['search', 'post_type', 'taxonomy'],
      'elements' => ['featured_image'],
      'return_format' => 'id',
    ];
  }

}
