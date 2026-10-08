<?php

namespace Netzstrategen\WooCommerceRelatedAccessories;

use WPGraphQL\Data\Connection\PostObjectConnectionResolver;
use Netzstrategen\WooCommerceRelatedAccessories\WooCommerce;

/**
 * GraphQL related functionalities.
 */
class GraphQL {

  /**
   * @uses graphql_register_types
   */
  public static function graphql_register_types() {
    register_graphql_object_type(
        'relatedAccessories',
        [
          'description' => __('Related accessories', 'woocommerce-related-accessories'),
          'fields' => [
            'label' => [
              'type' => 'String',
              'description' => __('Related accessory label', 'woocommerce-related-accessories'),
            ],
            'accessoriesIds' => [
              'type' => ['list_of' => 'Int'],
              'description' => __('Related accessory ids', 'woocommerce-related-accessories'),
            ]
          ],
        ]
      );

    register_graphql_field('Product', 'relatedAccessories', [
        'description' => __('Related accessories', Plugin::L10N),
        'type' => ['list_of' => 'relatedAccessories'],
        'resolve' => function () {
          $relatedAccessoriesIds = array_filter(WooCommerce::getRelatedAccessoriesIds());
          if (empty($relatedAccessoriesIds)) {
              return NULL;
          }
          $relatedAccessoriesLabels = wc_list_pluck(acf_get_local_fields('field_group_related_accessories'), 'label');
          $relatedAccessories = [];
          foreach ($relatedAccessoriesIds as $key => $value) {
            if (isset($relatedAccessoriesLabels["field_$key"]) || isset($relatedAccessoriesLabels[$key])) {
                $label = $relatedAccessoriesLabels["field_$key"] ?? $relatedAccessoriesLabels[$key];
                $relatedAccessories[] = [
                  'label' => sprintf(__('%s', 'woocommerce-related-accessories'), $label),
                  'accessoriesIds' => $value
                ];
            }
          };

          return $relatedAccessories;
        }
    ]);

    $config = [
      'fromType' => 'relatedAccessories',
      'toType' => 'Product',
      'fromFieldName' => 'accessories',
      'connectionTypeName' => 'ProductsFromRelatedAccessories',
      'resolve' => function ($source, $args, $context, $info) {
        // Pass the IDs as connection args (instead of setting post__in on the
        // resolver afterwards), so WPGraphQL keeps the order defined in the
        // backend (orderby post__in) and paginates cursors against it. The IDs
        // are re-indexed, because filters (e.g. removing unavailable products)
        // may leave gaps in the keys, while cursors are resolved by position.
        $args['where']['in'] = array_values($source['accessoriesIds']);
        $resolver = new PostObjectConnectionResolver($source, $args, $context, $info, 'product');
        $connection = $resolver->get_connection();
        return $connection;
      },
    ];
    register_graphql_connection($config);
  }

}
