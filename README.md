GraphQL Entity Defintions

This module exposes Drupal content entity definitions through GraphQL via the Drupal GraphQL contrib module.

This can be useful for building forms/views for entities through a front-end automatically instead of hand coding and constantly maintaining updates and changes.

An example query would be the follow, where

```$name``` is equal to the entity id e.g., user, taxonomy_term, node, etc.
```$bundle``` is the entity bundle (not required).
```$field_types``` is an enum of either: ALL, BASE_FIELDS, or FIELD_CONFIG.

The ```$field_types``` parameter allows you to filter the fields on the type of field, such as base fields, and field config.

```
query EntityDefinition($name: String!, $bundle: String, $field_types: FieldTypes) {
  definition: entityDefinition(name: $name, bundle: $bundle, field_types: $field_types) {
    label
    fields {
      id
      label
      description
      type
      required
      multiple
      maxNumItems
      status
      defaultValue
      isReference
      isHidden
      weight
      settings
    }
  }
}
```
