<?php

namespace Drupal\azure_search_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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

    return parent::buildForm($form, $form_state);
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

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('azure_search_settings.settings')
      //->set('example', $form_state->getValue('example'))
      ->set('endpoint', $form_state->getValue('endpoint'))
      ->set('api-version', $form_state->getValue('api-version'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
