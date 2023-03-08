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
              'description' => __('Related accessories ids', 'woocommerce-related-accessories'),
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
            if (isset($relatedAccessoriesLabels["field_$key"])) {
                $relatedAccessories[] = [
                  'label' => $relatedAccessoriesLabels["field_$key"],
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
        $resolver = new PostObjectConnectionResolver($source, $args, $context, $info, 'product');
        $resolver->set_query_arg('post__in', $source['accessoriesIds']);
        $connection = $resolver->get_connection();
        return $connection;
      },
    ];
    register_graphql_connection($config);
  }

}
