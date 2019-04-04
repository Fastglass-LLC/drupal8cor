<?php

namespace Drupal\azure_storage_settings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\node\Entity\Node;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\GetBlobOptions;
use MicrosoftAzure\Storage\Blob\Models\ContainerACL;
use MicrosoftAzure\Storage\Blob\Models\SetBlobPropertiesOptions;
use MicrosoftAzure\Storage\Blob\Models\ListPageBlobRangesOptions;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
use MicrosoftAzure\Storage\Common\Internal\Resources;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Common\Models\Range;
use MicrosoftAzure\Storage\Common\Models\Logging;
use MicrosoftAzure\Storage\Common\Models\Metrics;
use MicrosoftAzure\Storage\Common\Models\RetentionPolicy;
use MicrosoftAzure\Storage\Common\Models\ServiceProperties;

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

    $form['azure_blob_storage-actions'] = [
      '#type' => 'details',
      '#title' => t('Azure Blob Storage Setting Actions'),
      '#open' => TRUE,
    ];

    $form['azure_blob_storage']['blob-storage-connection-string'] = [
      '#type' => 'textarea',
      '#title' => t('Azure Blob Storage Connection String'),
      '#default_value' => $this->config('azure_storage_settings.settings')
        ->get('blob-storage-connection-string'),
      '#size' => 500,
      '#description' => t('This is the connection string to the Azure Blob Storage account where documents are located.'),
    ];

    //TODO - Need to remove this.
    $form['azure_blob_storage-actions']['populate-containers'] = [
      '#type' => 'submit',
      '#value' => 'Populate Containers',
      '#submit' => ['::populateContainers'],
      '#description' => t('Clicking this button will get all the data needed to build the existing Azure Blob Storage Container nodes.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function populateContainers(array &$form, FormStateInterface $form_state) {

    $config = \Drupal::config('azure_storage_settings.settings');
    $blobClient = BlobRestProxy::createBlobService($config->get('blob-storage-connection-string'));

    try {
      // List blobs.
      $listBlobsOptions = new ListBlobsOptions();

      // Setting max result to 1 is just to demonstrate the continuation token.
      // It is not the recommended value in a product environment.
      //$listBlobsOptions->setMaxResults(50);

      $blobContainers = $blobClient->listContainers();
      $blobContainerArray = $blobContainers->getContainers();

      $x=0;
      foreach ($blobContainerArray as $container)
      {
        //Add the Azure Container to Drupal.
        $node = Node::create([
          'type' => 'azure_blob_container',
          'title' => $container->getName(),
          'path' => ['alias' => '/container/' . $container->getName()],
        ]);
        $node->save();
        $x++;
      }

      $this->messenger()->addStatus($this->t($x.' Containers have been synced to the system.'));

    } catch (ServiceException $exception) {
      \Drupal::logger('azure_storage_settings')->error($exception->getMessage());
      $this->messenger()->
      $this->messenger()->addError($this->t('An error has occurred.  Please check the logs for additional information.'));
    }

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