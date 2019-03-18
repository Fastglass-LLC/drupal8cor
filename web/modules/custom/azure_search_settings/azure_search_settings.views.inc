<?php

/**
 * Implements hook_views_data().
 */
function azure_search_settings_views_data() {
  $data = [];

  // Base data.
  $data['azure_search_settings']['table']['group'] = t('Azure Search');
  $data['azure_search_settings']['table']['base'] = [
    'title' => t('Azure Search'),
    'help' => t('Azure Search data that is returned from the defined endpoint.'),
    'query_id' => 'azure_search',
  ];

  // Fields.
  //TODO - Need to find a way to accomodate dates in the setup in a way that makes Drupal happy.
  $data['azure_search_settings']['content'] = [
    'title' => t('Content'),
    'help' => t('Content that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_content_type'] = [
    'title' => t('Metadata Storage Content Type'),
    'help' => t('Metadata Storage Content Type that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_size'] = [
    'title' => t('Metadata Storage Size'),
    'help' => t('Metadata Storage Size that is returned in the search criteria.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];
  $data['azure_search_settings']['metadata_storage_last_modified'] = [
    'title' => t('Metadata Storage Last Modified'),
    'help' => t('Metadata Storage Last Modified that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_name'] = [
    'title' => t('Metadata Storage Name'),
    'help' => t('Metadata Storage Name that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_path'] = [
    'title' => t('Metadata Storage Path'),
    'help' => t('Metadata Storage Path that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_content_type'] = [
    'title' => t('Metadata Content Type'),
    'help' => t('Metadata Content Type that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_author'] = [
    'title' => t('Metadata Author'),
    'help' => t('Metadata Author that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_character_count'] = [
    'title' => t('Metadata Character Count'),
    'help' => t('Metadata Character Count that is returned in the search criteria.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  $data['azure_search_settings']['metadata_creation_date'] = [
    'title' => t('Metadata Creation Date'),
    'help' => t('Metadata Creation Date that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_last_modified'] = [
    'title' => t('Metadata Last Modified'),
    'help' => t('Metadata Last Modified that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_page_count'] = [
    'title' => t('Metadata Page Count'),
    'help' => t('Metadata Page Count that is returned in the search criteria.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  $data['azure_search_settings']['metadata_word_count'] = [
    'title' => t('Metadata Word Count'),
    'help' => t('Metadata Word Count that is returned in the search criteria.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  $data['azure_search_settings']['language'] = [
    'title' => t('Language'),
    'help' => t('Language that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['merged_content'] = [
    'title' => t('Merged Content'),
    'help' => t('Merged Content that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  return $data;
}