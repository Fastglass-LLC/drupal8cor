<?php

namespace Drupal\azure_search_settings\Plugin\views\field;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Class Page
 *
 * @ViewsField("search_page")
 */
class Page extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();


    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {


    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $search_page = $this->getValue($values);
    $image_result = '';
    if ($search_page) {
      try {
        $config = \Drupal::config('azure_search_settings.settings');

        //TODO - Need to find a way to pass in the container name for the function.
        $client = new Client();
        $request = new Request(
          "GET",
          "https://" . $config->get('azure-functions-endpoint') . ".azurewebsites.net/api/HighlightImageString?code=" . $config->get('azure-functions-api-code') . "&containerName=processed-images&blobName=" . $search_page,
          [],
          "");

        $response = $client->send($request);
        $image_result = $response->getBody();

      } catch (\Exception $exception) {
        \Drupal::logger('azure_search_settings')
          ->error($exception->getMessage());
      }

      return [
        '#type' => 'inline_template',
        '#template' => "<img height='50%' src='{{ data }}' />",
        '#context' => [
          'data' => $image_result,
        ],
      ];
    }
  }
}