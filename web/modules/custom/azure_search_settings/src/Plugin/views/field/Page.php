<?php

namespace Drupal\azure_search_settings\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

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
        if ($search_page) {
          return [
            '#theme' => 'image',
            '#uri' => 'https://image.shutterstock.com/image-vector/smiley-vector-happy-face-450w-465566966.jpg',
            '#alt' => $this->t('Search Page'),
          ];
          //'#theme' => 'image',
          //  '#uri' => $avatar[$this->options['avatar_size']],
          //  '#alt' => $this->t('Avatar'),
        }
  }

}