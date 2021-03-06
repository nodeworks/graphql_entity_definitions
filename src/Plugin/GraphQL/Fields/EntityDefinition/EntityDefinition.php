<?php

namespace Drupal\graphql_entity_definitions\Plugin\GraphQL\Fields\EntityDefinition;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Drupal\graphql\GraphQL\Execution\ResolveContext;

/**
 * Entity Definition.
 *
 * @GraphQLField(
 *   id = "entity_definition",
 *   secure = true,
 *   name = "entityDefinition",
 *   type = "EntityDefinition",
 *   arguments = {
 *     "name" = "String!",
 *     "bundle" = "String",
 *     "field_types" = {
 *       "type" = "FieldTypes",
 *       "default" = "ALL"
 *     }
 *   }
 * )
 */
class EntityDefinition extends FieldPluginBase implements ContainerFactoryPluginInterface {
  use DependencySerializationTrait;

  /**
   * The Queue Factory.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    if ($args['bundle']) {
      $bundle_info = \Drupal::service('entity_type.bundle.info')->getBundleInfo($args['name']);
      if (isset($bundle_info[$args['bundle']])) {
        $bundle = $bundle_info[$args['bundle']];
        $bundle['key'] = $args['bundle'];
        $context->setContext('bundle', $bundle, $info);
      }
    }

    if ($args['field_types']) {
      $context->setContext('field_types', $args['field_types'], $info);
    }

    yield $this->entityTypeManager->getDefinition($args['name']);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * EntityDefinition constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->entityTypeManager = $entityTypeManager;
  }

}
