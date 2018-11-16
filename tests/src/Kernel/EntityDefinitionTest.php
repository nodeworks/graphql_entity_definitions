<?php

namespace Drupal\Tests\graphql_entity_definitions\Kernel;

use Drupal\simpletest\ContentTypeCreationTrait;
use Drupal\Tests\graphql_core\Kernel\GraphQLContentTestBase;

/**
 * Test graphql entity definitions.
 *
 * @group graphql_entity_definitions
 */
class EntityDefinitionTest extends GraphQLContentTestBase {
  use ContentTypeCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'node',
    'field',
    'text',
    'filter',
    'graphql_core',
    'graphql_entity_definitions',
    'content_translation',
    'node',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // The global CurrentUserContext doesn't work properly without a
    // fully-installed user module.
    // @see https://www.drupal.org/project/rules/issues/2989417
    $this->container->get('module_handler')->loadInclude('user', 'install');
    $this->installEntitySchema('node');

    $this->createContentType([
      'type' => 'article',
    ]);

    user_install();
  }

  /**
   * Test entity definition query label.
   */
  public function testDefinitionLabel() {
    $query = $this->getQueryFromFile('definition.gql');
    $result = $this->query($query, ['name' => 'user']);
    $content = json_decode($result->getContent(), TRUE);
    $definition = $content['data']['definition'];
    $label = $definition['label'];

    self::assertEquals('User', $label, 'Result has correct definition label.');
  }

  /**
   * Test entity definition query settings.
   */
  public function testDefinitionSettings() {
    $query = $this->getQueryFromFile('definition.gql');
    $result = $this->query($query, ['name' => 'user']);
    $content = json_decode($result->getContent(), TRUE);
    $definition = $content['data']['definition'];
    $settings = $definition['fields'][0]['settings'];

    self::assertEquals('unsigned', $settings[0]['key'], 'Result has correct setting keys.');
    self::assertTrue($settings[0]['value'], 'Result has correct setting values.');
  }

  /**
   * Test entity definition query with specified bundle.
   */
  public function testDefinitionBundle() {
    $query = $this->getQueryFromFile('definition.gql');
    $result = $this->query($query, ['name' => 'node', 'bundle' => 'article']);
    $content = json_decode($result->getContent(), TRUE);
    $definition = $content['data']['definition'];
    $label = $definition['label'];

    self::assertEquals('article', $label, 'Result has correct definition label.');
  }

  /**
   * Test entity definition query with field config field types.
   */
  public function testDefinitionFieldConfigFieldTypes() {
    $query = $this->getQueryFromFile('definition.gql');
    $result = $this->query($query, ['name' => 'node', 'bundle' => 'article', 'field_types' => 'FIELD_CONFIG']);
    $content = json_decode($result->getContent(), TRUE);
    $definition = $content['data']['definition'];
    $fields = $definition['fields'];

    self::assertCount(1, $fields, 'Result has the correct amount of fields.');
    self::assertEquals('Body', $fields[0]['label'], 'Result has the correct field.');
  }

  /**
   * Test entity definition query with base field field types.
   */
  public function testDefinitionBaseFieldFieldTypes() {
    $query = $this->getQueryFromFile('definition.gql');
    $result = $this->query($query, ['name' => 'node', 'bundle' => 'article', 'field_types' => 'BASE_FIELDS']);
    $content = json_decode($result->getContent(), TRUE);
    $definition = $content['data']['definition'];
    $fields = $definition['fields'];

    self::assertCount(20, $fields, 'Result has the correct amount of fields.');
    self::assertEquals('ID', $fields[0]['label'], 'Result has the correct field.');
  }

  /**
   * Test entity definition query with all field types.
   */
  public function testDefinitionAllFieldTypes() {
    $query = $this->getQueryFromFile('definition.gql');
    $result = $this->query($query, ['name' => 'node', 'bundle' => 'article', 'field_types' => 'ALL']);
    $content = json_decode($result->getContent(), TRUE);
    $definition = $content['data']['definition'];
    $fields = $definition['fields'];

    self::assertCount(21, $fields, 'Result has the correct amount of fields.');
    self::assertEquals('ID', $fields[0]['label'], 'Result has the correct first field.');
    self::assertEquals('Body', $fields[20]['label'], 'Result has the correct last field.');
  }

}
