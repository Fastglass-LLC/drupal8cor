<?php

namespace Drupal\azure_storage_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\node\Entity\Node;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Configure Azure Storage Settings settings for this site.
 */
class SettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'azure_storage_settings_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['azure_storage_settings.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['azure_blob_storage'] = [
      '#type' => 'details',
      '#title' => t('Azure Blob Storage Settings'),
      '#open' => TRUE,
    ];

    $form['azure_blob_storage']['blob-storage-connection-string'] = [
      '#type' => 'textfield',
      '#title' => t('Azure Blob Storage Connection String'),
      '#default_value' => $this->config('azure_storage_settings.settings')
        ->get('blob-storage-connection-string'),
      '#size' => 60,
      '#description' => t('This is the connection string to the Azure Blob Storage account where documents are located.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (strlen($form_state->getValue('blob-storage-connection-string')) == 0) {
      $form_state->setErrorByName('blob-storage-connection-string', $this->t('The value for Blob Storage Connection String cannot be empty.'));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('azure_storage_settings.settings')
      ->set('blob-storage-connection-string', $form_state->getValue('blob-storage-connection-string'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}