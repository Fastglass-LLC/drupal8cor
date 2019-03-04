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
  $data['azure_search_settings']['content'] = [
    'title' => t('Content'),
    'help' => t('Content that is returned in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['celebrities'] = [
    'title' => t('Celebrities'),
    'help' => t('Celebrities that have been found in the search criteria.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['imageCaption'] = [
    'title' => t('Image Caption'),
    'help' => t('Image Captions returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['imageTags'] = [
    'title' => t('Image Tags'),
    'help' => t('Image Tags returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['keyphrases'] = [
    'title' => t('Keyphrases'),
    'help' => t('Keyphrases returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['language'] = [
    'title' => t('Language'),
    'help' => t('Language returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['layoutText'] = [
    'title' => t('Layout Text'),
    'help' => t('Layout Text returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['locations'] = [
    'title' => t('Locations'),
    'help' => t('Locations returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['merged_content'] = [
    'title' => t('Merged Content'),
    'help' => t('Merged Content returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_author'] = [
    'title' => t('Merged Author'),
    'help' => t('Merged Author returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['merged_character_count'] = [
    'title' => t('Merged Character Count'),
    'help' => t('Merged Character Count returned by Azure Search.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  $data['azure_search_settings']['metadata_content_type'] = [
    'title' => t('Metadata Content Type'),
    'help' => t('Metadata Content Type returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_creation_date'] = [
    'title' => t('Metadata Creation Date'),
    'help' => t('Metadata Creation Date returned by Azure Search.'),
    'field' => [
      'id' => 'date',
    ],
  ];

  $data['azure_search_settings']['metadata_last_modified'] = [
    'title' => t('Metadata Last Modified Date'),
    'help' => t('Metadata Last Modified Date returned by Azure Search.'),
    'field' => [
      'id' => 'date',
    ],
  ];

  $data['azure_search_settings']['metadata_page_count'] = [
    'title' => t('Metadata Page Count'),
    'help' => t('Metadata Page Count returned by Azure Search.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_content_type'] = [
    'title' => t('Metadata Storage Content Type'),
    'help' => t('Metadata Storage Content Type returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_last_modified'] = [
    'title' => t('Metadata Storage Last Modified Date'),
    'help' => t('Metadata Storage Last Modified Date returned by Azure Search.'),
    'field' => [
      'id' => 'date',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_name'] = [
    'title' => t('Metadata Storage Name'),
    'help' => t('Metadata Storage Name returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_path'] = [
    'title' => t('Metadata Storage Path'),
    'help' => t('Metadata Storage Path returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_size'] = [
    'title' => t('Metadata Storage Size'),
    'help' => t('Metadata Storage Size returned by Azure Search.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  $data['azure_search_settings']['metadata_storage_word_count'] = [
    'title' => t('Metadata Storage Word Count'),
    'help' => t('Metadata Storage Word Count returned by Azure Search.'),
    'field' => [
      'id' => 'numeric',
    ],
  ];

  $data['azure_search_settings']['organizations'] = [
    'title' => t('Organizations'),
    'help' => t('Organizations returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  $data['azure_search_settings']['people'] = [
    'title' => t('People	'),
    'help' => t('People returned by Azure Search.'),
    'field' => [
      'id' => 'standard	',
    ],
  ];

  $data['azure_search_settings']['text'] = [
    'title' => t('Text'),
    'help' => t('Text returned by Azure Search.'),
    'field' => [
      'id' => 'standard',
    ],
  ];

  return $data;
}