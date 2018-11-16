<?php

namespace Drupal\graphql_entity_definitions\Plugin\GraphQL\Enums;

use Drupal\graphql\Plugin\GraphQL\Enums\EnumPluginBase;

/**
 * @GraphQLEnum(
 *   id = "field_types",
 *   name = "FieldTypes",
 *   values = {
 *     "ALL" = "ALL",
 *     "BASE_FIELDS" = "BASE_FIELDS",
 *     "FIELD_CONFIG" = "FIELD_CONFIG"
 *   }
 * )
 */
class FieldTypes extends EnumPluginBase {

}
