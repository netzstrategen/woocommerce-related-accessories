<?php

namespace Netzstrategen\WooCommerceRelatedAccessories;

class Helper {

  public static function getFieldParameters(string $name, string $label, bool $field, string $taxonomy): array {
    if (!isset($taxonomy)) {
      $taxonomy = $name;
    }
    return [
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

}
