<?php


namespace Drupal\azure_search_settings\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Simple filter to handle filtering Azure Search results by merged_content.
 * @ViewsFilter("merged_content_id")
 */
class MergedContent extends FilterPluginBase{

  public $no_operator = TRUE;
  /**
   * {@inheritdoc}
   */

  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#size' => 60,
      '#default_value' => $this->value,
    ];
  }
}