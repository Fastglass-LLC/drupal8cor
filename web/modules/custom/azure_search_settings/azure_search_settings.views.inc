<?php

/**
 * Implements hook_views_data().
 */
function azure_search_settings_views_data() {
  $data = [];

  try {
    //Get the Index nodes
    $index_nids = \Drupal::entityQuery('node')
      ->condition('type', 'azure_search_index')
      ->execute();

    $index_nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadMultiple($index_nids);

    foreach ($index_nodes as $index_node) {
      //$this->messenger()
      //  ->addStatus($this->t('Node Title: ' . $index_node->label() . ', Node ID: ' . $index_node->id()));

      //Get the Index Field nodes
      $nids_fields = \Drupal::entityQuery('node')
        ->condition('type', 'azure_search_index_field')
        ->condition('field_azure_search_index_field', $index_node->id())
        ->execute();

      $fields = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadMultiple($nids_fields);

      \Drupal::logger('azure_search_settings')
        ->info('Starting node for ' . $index_node->label());

      // Base data.
      $data[$index_node->label()]['table']['group'] = t($index_node->label());
      $data[$index_node->label()]['table']['base'] = [
        'title' => t('Azure Search - ' . $index_node->label()),
        'help' => t('Azure Search data that is returned from the defined ' . $index_node->label() . ' endpoint.'),
        'query_id' => 'azure_search',
      ];

      // Fields.
      //TODO - Need to find a way to accomodate dates in the setup in a way that makes Drupal happy.
      foreach ($fields as $field) {

        //TODO - Need to find a way to accomodate dates in the setup in a way that makes Drupal happy.
        if ($field->label() == 'content') {
          $data[$index_node->label()]['content'] = [
            'title' => t('Content'),
            'help' => t('Content that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'metadata_storage_content_type') {
          $data[$index_node->label()]['metadata_storage_content_type'] = [
            'title' => t('Metadata Storage Content Type'),
            'help' => t('Metadata Storage Content Type that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'metadata_storage_size') {
          $data[$index_node->label()]['metadata_storage_size'] = [
            'title' => t('Metadata Storage Size'),
            'help' => t('Metadata Storage Size that is returned in the search criteria.'),
            'field' => [
              'id' => 'numeric',
            ],
          ];
        }

        if ($field->label() == 'metadata_storage_last_modified') {
          $data[$index_node->label()]['metadata_storage_last_modified'] = [
            'title' => t('Metadata Storage Last Modified'),
            'help' => t('Metadata Storage Last Modified that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'metadata_storage_name') {
          $data[$index_node->label()]['metadata_storage_name'] = [
            'title' => t('Metadata Storage Name'),
            'help' => t('Metadata Storage Name that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
            'filter'=>[
              'id'=>'metadata_storage_name_id'
            ]
          ];
        }

        if ($field->label() == 'metadata_storage_path') {
          $data[$index_node->label()]['metadata_storage_path'] = [
            'title' => t('Metadata Storage Path'),
            'help' => t('Metadata Storage Path that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'metadata_content_type') {
          $data[$index_node->label()]['metadata_content_type'] = [
            'title' => t('Metadata Content Type'),
            'help' => t('Metadata Content Type that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'metadata_author') {
          $data[$index_node->label()]['metadata_author'] = [
            'title' => t('Metadata Author'),
            'help' => t('Metadata Author that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'metadata_character_count') {
          $data[$index_node->label()]['metadata_character_count'] = [
            'title' => t('Metadata Character Count'),
            'help' => t('Metadata Character Count that is returned in the search criteria.'),
            'field' => [
              'id' => 'numeric',
            ],
          ];
        }

        if ($field->label() == 'metadata_creation_date') {
          $data[$index_node->label()]['metadata_creation_date'] = [
            'title' => t('Metadata Creation Date'),
            'help' => t('Metadata Creation Date that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'metadata_last_modified') {
          $data[$index_node->label()]['metadata_last_modified'] = [
            'title' => t('Metadata Last Modified'),
            'help' => t('Metadata Last Modified that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'metadata_page_count') {
          $data[$index_node->label()]['metadata_page_count'] = [
            'title' => t('Metadata Page Count'),
            'help' => t('Metadata Page Count that is returned in the search criteria.'),
            'field' => [
              'id' => 'numeric',
            ],
          ];
        }

        if ($field->label() == 'metadata_word_count') {
          $data[$index_node->label()]['metadata_word_count'] = [
            'title' => t('Metadata Word Count'),
            'help' => t('Metadata Word Count that is returned in the search criteria.'),
            'field' => [
              'id' => 'numeric',
            ],
          ];
        }

        if ($field->label() == 'language') {
          $data[$index_node->label()]['language'] = [
            'title' => t('Language'),
            'help' => t('Language that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }

        if ($field->label() == 'merged_content') {
          $data[$index_node->label()]['merged_content'] = [
            'title' => t('Merged Content'),
            'help' => t('Merged Content that is returned in the search criteria.'),
            'field' => [
              'id' => 'standard',
            ],
          ];
        }
      }
    }
    //Finishing the database code
  } catch (\Exception $exception) {
    \Drupal::logger('azure_search_settings_views_data')
      ->error($exception->getMessage());
  }

  return $data;
}