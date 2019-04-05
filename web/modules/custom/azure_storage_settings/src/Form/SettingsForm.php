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

    $form['azure_blob_storage-actions']['populate-container-items'] = [
      '#type' => 'submit',
      '#value' => 'Populate Container Items',
      '#submit' => ['::populateContainerItems'],
      '#description' => t('Clicking this button will get all the data needed to build the existing Azure Blob Storage Container Item nodes.'),
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

      $x = 0;
      foreach ($blobContainerArray as $container) {
        //Add the Azure Container to Drupal.
        $node = Node::create([
          'type' => 'azure_blob_container',
          'title' => $container->getName(),
          'path' => ['alias' => '/container/' . $container->getName()],
        ]);
        $node->save();
        $x++;
      }

      $this->messenger()
        ->addStatus($this->t($x . ' Containers have been synced to the system.'));

    } catch (ServiceException $exception) {
      \Drupal::logger('azure_storage_settings')
        ->error($exception->getMessage());
      $this->messenger()
        ->addError($this->t('An error has occurred.  Please check the logs for additional information.'));
    }

  }

  public function populateContainerItems(array &$form, FormStateInterface $form_state) {

    $config = \Drupal::config('azure_storage_settings.settings');
    $blobClient = BlobRestProxy::createBlobService($config->get('blob-storage-connection-string'));
    //$output_string = '';
    $x = 0;
    $y = 0;

    try {
      //This query returns the Node ID's for the sected filters
      $existing_container_ids = \Drupal::entityQuery('node')
        ->condition('type', 'azure_blob_container')
        ->condition('field_azure_blob_process_contain', TRUE)
        ->execute();

      //Loop through each container and get the documents for each one.
      foreach ($existing_container_ids as $container_id) {
        $container = Node::load($container_id);

        $listBlobsOptions = new ListBlobsOptions();
        $listBlobsOptions->setMaxResults(50);

        do {
          $blob_list = $blobClient->listBlobs($container->getTitle(), $listBlobsOptions);
          foreach ($blob_list->getBlobs() as $blob) {

            $blob_node = Node::create([
              'type' => 'azure_blob_item',
              'title' => $blob->getName(),
              'field_azure_blob_item_size' => $blob->getProperties()
                ->getContentLength(),
              'field_azure_blob_item_type' => $blob->getProperties()
                ->getContentType(),
              'field_azure_blob_item_url' => $blob->getUrl(),
              'field_azure_blob_item_last_mod' => $blob->getProperties()
                ->getLastModified()
                ->format('Y-m-d\Th:i:s'),
              'field_azure_blob_item_container' => $container_id,
              'path' => ['alias' => '/blob/' . $blob->getName()],
            ]);
            $blob_node->save();

            $y++;
          }
          $listBlobsOptions->setContinuationToken($blob_list->getContinuationToken());
        } while ($blob_list->getContinuationToken());
        $x++;
      }
      $this->messenger()
        ->addMessage('Successfully updated ' . $x . ' Containers of ' . $y . ' total files');

    } catch (ServiceException $exception) {
      \Drupal::logger('azure_storage_settings')
        ->error($exception->getMessage());
      $this->messenger()
        ->addError($this->t('An error has occurred.  Please check the logs for additional information.'));
    } catch (\Exception $exception) {
      \Drupal::logger('azure_storage_settings')
        ->error($exception->getMessage());
      $this->messenger()
        ->addError($this->t('An error has occurred.  Please check the logs for additional information.'));
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