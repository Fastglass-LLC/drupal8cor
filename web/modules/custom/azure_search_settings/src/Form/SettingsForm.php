<?php

namespace Drupal\azure_search_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\node\Entity\Node;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Configure Azure Search Settings settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'azure_search_settings_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['azure_search_settings.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['azure_search_settings'] = [
      '#type' => 'details',
      '#title' => t('Azure Search Settings'),
      '#open' => TRUE,
    ];

    $form['azure_search_indexes'] = [
      '#type' => 'details',
      '#title' => t('Azure Search Settings'),
      '#open' => TRUE,
    ];

    $form['azure_search_settings']['endpoint'] = [
      '#type' => 'textfield',
      '#title' => t('Azure Search Endpoint'),
      '#default_value' => $this->config('azure_search_settings.settings')
        ->get('endpoint'),
      '#size' => 40,
      '#description' => t('This is the endpoint of your Azure Search service.  The format for this should be https://[YOUR NAME HERE].search.windows.net'),
      '#field_prefix' => t('https://'),
      '#field_suffix' => t('.search.windows.net'),
    ];

    $form['azure_search_settings']['api-version'] = [
      '#type' => 'textfield',
      '#title' => t('API Version'),
      '#default_value' => $this->config('azure_search_settings.settings')
        ->get('api-version'),
      '#size' => 40,
      '#description' => t('This is the api version of endpoint of your Azure Search service.  The default supported value at this time is "2017-11-11"'),
    ];

    $form['azure_search_settings']['api-key'] = [
      '#type' => 'textfield',
      '#title' => t('API Key'),
      '#default_value' => $this->config('azure_search_settings.settings')
        ->get('api-key'),
      '#size' => 40,
      '#description' => t('This is the api key of endpoint of your Azure Search service.'),
    ];

    $form['azure_search_indexes']['resync_indexes'] = [
      '#type' => 'submit',
      '#value' => 'Resync Indexes and Synonyms',
      '#submit' => ['::resyncIndexes'],
      '#description' => t('Clicking this button will resync the settings for all stored Indexes, Synonyms, and their related Fields within your Azure Search instance.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * This is the resyncIndexes function.
   */
  public function resyncIndexes(array &$form, FormStateInterface $form_state) {

    $config = \Drupal::config('azure_search_settings.settings');

    $x = 0;
    $y = 0;
    $z = 0;

    //Call Azure to get the Search Indexes and related details.
    $client = new Client();
    $request = new Request(
      "GET",
      "https://" . $config->get('endpoint') . ".search.windows.net/indexes?api-version=" . $config->get('api-version'),
      [
        "api-key" => $config->get('api-key'),
        "content-type" => "application/json",
      ],
      "{}");

    $response = $client->send($request);
    $azure_search_indexes = json_decode($response->getBody());

    //TODO - Need to add proper error logging.
    if ($response->getStatusCode() != 200) {
      $this->messenger()
        ->addError('There was an error calling the web service \n' . $response->getBody());
    }

    //TODO - Need to refactor out the entity_delete_multiple function calls as it is deprecated in D9.
    //Delete all the existing content if any exists.
    $existing_fields = \Drupal::entityQuery('node')
      ->condition('type', 'azure_search_index_field')
      ->execute();
    entity_delete_multiple('node', $existing_fields);

    $existing_indexes = \Drupal::entityQuery('node')
      ->condition('type', 'azure_search_index')
      ->execute();
    entity_delete_multiple('node', $existing_indexes);

    $existing_synonyms = \Drupal::entityQuery('node')
      ->condition('type', 'azure_search_synonym')
      ->execute();
    entity_delete_multiple('node', $existing_synonyms);

    $existing_fields = NULL;
    $existing_indexes = NULL;
    $existing_synonyms = NULL;

    //Iterate through the web service response for Indexes.
    foreach ($azure_search_indexes->value as $index) {

      //Add the Search Index to Drupal.
      $index_node = Node::create([
        'type' => 'azure_search_index',
        'title' => $index->name,
        'path' => ['alias' => '/index/' . $index->name],
      ]);
      $index_node->save();
      $x++;

      //Iterate through the web service response for Index Fields.
      foreach ($index->fields as $field) {

        //Add the Search Index Field to Drupal.
        $field_node = Node::create([
          'type' => 'azure_search_index_field',
          'title' => $field->name,
          'field_azure_search_index_field' => $index_node->id(),
          'field_azure_search_field_facet' => $field->facetable,
          'field_azure_search_field_filter' => $field->filterable,
          'field_azure_search_field_key' => $field->key,
          'field_azure_search_field_retriev' => $field->retrievable,
          'field_azure_search_field_sortabl' => $field->sortable,
          'field_azure_search_index_fld_bln' => $field->searchable,
          'field_azure_search_index_fld_txt' => $field->type,
          'path' => ['alias' => '/index/' . $index->name . '/' . $field->name],
        ]);
        $field_node->save();
        $y++;
      }
    }

    //Call Azure to get the Search Synonyms and their related items.
    $request_synonyms = new Request(
      "GET",
      "https://" . $config->get('endpoint') . ".search.windows.net/synonymmaps?api-version=" . $config->get('api-version'),
      [
        "api-key" => $config->get('api-key'),
        "content-type" => "application/json",
      ],
      "{}");

    $response_synonyms = $client->send($request_synonyms);
    $azure_search_synonyms = json_decode($response_synonyms->getBody());

    //TODO - Need to add proper error logging.
    if ($response_synonyms->getStatusCode() != 200) {
      $this->messenger()
        ->addError('There was an error calling the web service \n' . $response_synonyms->getBody());
    }

    //Iterate through the web service response for Synonyms.
    foreach ($azure_search_synonyms->value as $synonym) {

      //Add the Search Index to Drupal.
      $synonym_node = Node::create([
        'type' => 'azure_search_synonym',
        'title' => $synonym->name,
        'field_azure_search_syn_format' => $synonym->format,
        'field_azure_search_synonym_value' => $synonym->synonyms,
      ]);
      $synonym_node->save();
      $z++;
    }

    $this->messenger()
      ->addStatus($this->t('Added  ' . $x . ' Index node(s), ' . $y . ' Field node(s), and ' . $z . ' Synonym node(s) as defined in the Azure Search endpoint.'));
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (strlen($form_state->getValue('endpoint')) == 0) {
      $form_state->setErrorByName('endpoint', $this->t('The value for Azure Search Endpoint cannot be empty.'));
    }

    if (strlen($form_state->getValue('api-version')) == 0) {
      $form_state->setErrorByName('api-version', $this->t('The value for API Version cannot be empty.'));
    }

    if (strlen($form_state->getValue('api-key')) == 0) {
      $form_state->setErrorByName('api-key', $this->t('The value for API Key cannot be empty.'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('azure_search_settings.settings')
      ->set('endpoint', $form_state->getValue('endpoint'))
      ->set('api-version', $form_state->getValue('api-version'))
      ->set('api-key', $form_state->getValue('api-key'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
