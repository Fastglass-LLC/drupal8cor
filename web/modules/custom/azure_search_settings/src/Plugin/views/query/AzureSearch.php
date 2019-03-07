<?php

namespace Drupal\azure_search_settings\Plugin\views\query;

use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use http\Exception;


/**
 * Azure Search views query plugin which wraps calls to the Azure Search API in
 * order to expose the results to views.
 *
 * @ViewsQuery(
 *   id = "azure_search",
 *   title = @Translation("Azure Search"),
 *   help = @Translation("Query against the Azure Search API.")
 * )
 */
class AzureSearch extends QueryPluginBase {

  public function ensureTable($table, $relationship = NULL) {
    return '';
  }

  public function addField($table, $field, $alias = '', $params = []) {
    return $field;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(ViewExecutable $view) {

    $index = 0;

    $json_file = file_get_contents(getcwd() . '/example.json');
    $azure_search = json_decode($json_file);


    /*try {
      $this->messenger()
        ->addStatus($this->t('Row count value: ' . $azure_search['@odata.count']));
    } catch (\Exception $e) {
      $this->messenger()->addStatus($this->t($e->getMessage()));
    }*/


    foreach ($azure_search->value as $search_row) {
      if ($search_row->content != NULL) {
        $row['content'] = $search_row->content;
      }

      if ($search_row->metadata_storage_content_type != NULL) {
        $row['metadata_storage_content_type'] = $search_row->metadata_storage_content_type;
      }

      if ($search_row->metadata_storage_size != NULL) {
        $row['metadata_storage_size'] = $search_row->metadata_storage_size;
      }

      if ($search_row->metadata_storage_last_modified != NULL) {
        $row['metadata_storage_last_modified'] = $search_row->metadata_storage_last_modified;
      }

      if ($search_row->metadata_storage_name != NULL) {
        $row['metadata_storage_name'] = $search_row->metadata_storage_name;
      }

      if ($search_row->metadata_storage_path != NULL) {
        $row['metadata_storage_path'] = $search_row->metadata_storage_path;
      }

      if ($search_row->metadata_content_type != NULL) {
        $row['metadata_content_type'] = $search_row->metadata_content_type;
      }

      if ($search_row->metadata_author != NULL) {
        $row['metadata_author'] = $search_row->metadata_author;
      }

      if ($search_row->metadata_character_count != NULL) {
        $row['metadata_character_count'] = $search_row->metadata_character_count;
      }

      if ($search_row->metadata_creation_date != NULL) {
        $row['metadata_creation_date'] = $search_row->metadata_creation_date;
      }

      if ($search_row->metadata_last_modified != NULL) {
        $row['metadata_last_modified'] = $search_row->metadata_last_modified;
      }

      if ($search_row->metadata_page_count != NULL) {
        $row['metadata_page_count'] = $search_row->metadata_page_count;
      }

      if ($search_row->metadata_word_count != NULL) {
        $row['metadata_word_count'] = $search_row->metadata_word_count;
      }

      if ($search_row->language != NULL) {
        $row['language'] = $search_row->language;
      }

      if ($search_row->merged_content != NULL) {
        $row['merged_content'] = $search_row->merged_content;
      }

      $this->messenger()
        ->addStatus($this->t('Search Row value: ' . $search_row->metadata_page_count));
      $row['index'] = $index;
      $view->result[] = new ResultRow($row);
      $index++;
    }

    /*foreach ($azure_search->value as $search_row) {
      //$this->messenger()->addStatus($this->t('Search Row value: '.));
      $row['index'] = $index;
      $row['content'] = 'crap';// $search_row->metadata_storage_name;
      $view->result[] = new ResultRow($row);
      $index++;
    }


    $this->messenger()
      ->addStatus($this->t('Index value: '.$index));*/

    /*for ($index = 1; $index <= 8; $index++) {
    $row['content'] = 'Document Name ' . $index;
    $row['index'] = $index;
    $view->result[] = new ResultRow($row);
  }*/

    parent::execute($view);
    //TODO: Change the autogenerated stub
  }
}