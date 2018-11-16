<?php

namespace Drupal\graphql_entity_definitions\Plugin\GraphQL\Fields\EntityDefinition;

use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\Entity\BaseFieldOverride;
use Drupal\field\Entity\FieldConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @GraphQLField(
 *   id = "entity_definition_fields",
 *   secure = true,
 *   name = "fields",
 *   type = "[EntityDefinitionField]",
 *   parents = {"EntityDefinition"}
 * )
 */
class Fields extends FieldPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * Fields constructor.
   *
   * @param array $configuration
   *   The plugin configuration array.
   * @param string $pluginId
   *   The plugin id.
   * @param mixed $pluginDefinition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(
    array $configuration,
    $pluginId,
    $pluginDefinition,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    /** @var \Drupal\Core\Entity\ContentEntityType $value */
    if ($value instanceof ContentEntityType) {
      if ($bundle = $context->getContext('bundle', $info)) {
        $key = $bundle['key'];
        $id = $value->id();
        $entity_id = $id . '.' . $id . '.' . $key;
        $fields = \Drupal::entityManager()->getFieldDefinitions($id, $key);
      }
      else {
        $id = $value->id();
        $entity_id = $id . '.' . $id . '.default';
        $fields = \Drupal::entityManager()->getFieldDefinitions($id, $id);
      }

      /** @var \Drupal\Core\Config\Entity\ConfigEntityStorage $form_display */
      $form_display = $this->entityTypeManager
        ->getStorage('entity_form_display')
        ->load($entity_id);

      $context->setContext('entity_form_display', $form_display, $info);
      if ($field_types = $context->getContext('field_types', $info)) {
        foreach ($fields as $field) {
          if ($field_types === 'BASE_FIELDS') {
            if ($field instanceof BaseFieldDefinition) {
              yield $field;
            }
          }
          elseif ($field_types === 'FIELD_CONFIG') {
            if ($field instanceof FieldConfig || $field instanceof BaseFieldOverride) {
              yield $field;
            }
          }
          else {
            yield $field;
          }
        }
      }
      else {
        yield from $fields;
      }
    }
  }

}
