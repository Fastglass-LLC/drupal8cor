<?php

namespace Drupal\azure_search_settings\Plugin\views\query;

use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use http\Exception;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


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

    try {

      //Get the base table of the view.  This will be the Azure Index that has been defined.
      $azure_index = key($view->getBaseTables());

      $index = 0;
      $config = \Drupal::config('azure_search_settings.settings');
      $client = new Client();

      if (strlen($config->get('endpoint')) == 0) {
        throw new \Exception("You must set the 'endpoint' value in the Azure Search Settings page in order for the View to work properly");
      }

      if (strlen($config->get('api-key')) == 0) {
        throw new \Exception("You must set the 'api-key' value in the Azure Search Settings page in order for the View to work properly");
      }

      $filters = [];

      if (isset($this->where)) {
        foreach ($this->where as $where_group => $where) {
          foreach ($where['conditions'] as $condition) {
            // Remove dot from beginning of the string.
            $field_name = ltrim($condition['field'], '.');
            $filters[$field_name] = $condition['value'];
          }
        }
      }

      dpm($filters);

      // We currently only support uid, ignore any other filters that may be
      // configured.
      //$uid = isset($filters['uid']) ? $filters['uid'] : NULL;
      //if ($access_tokens = $this->fitbitAccessTokenManager->loadMultipleAccessToken([$uid])) {
        // Query remote API and return results ...
      //}

      //Get the search fields defined in the view to get just those fields from the Azure Search request
      $search_fields = '';
      $view_fields = $view->getQuery()->view->getDisplay()->getFieldLabels();

      foreach (array_keys($view_fields) as $view_field) {
        $search_fields = $search_fields . $view_field . ',';
      }
      $search_fields = rtrim($search_fields, ',');

      //Build the array for the query to Azure Search
      $azure_search_parameters = [];
      $azure_search_parameters['search'] = "merged_content:CEDING COMPANY and metadata_storage_name:dex9*";
      $azure_search_parameters['select'] = $search_fields;
      $azure_search_parameters['queryType'] = "full";
      $azure_search_parameters['searchMode'] = "all";

      if ($config->get('enable-text-highlighting') == TRUE) {
        $azure_search_parameters['highlight'] = "merged_content-5";
        $azure_search_parameters['highlightPreTag'] = $config->get('highlight-pre-tag');
        $azure_search_parameters['highlightPostTag'] = $config->get('highlight-post-tag');
      }

      //Request to Azure Search per an HTTP POST call
      $request = new Request(
        "POST",
        "https://" . $config->get('endpoint') . ".search.windows.net/indexes/" . $azure_index . "/docs/search?api-version=" . $config->get('api-version'),
        [
          "api-key" => $config->get('api-key'),
          "content-type" => "application/json",
        ],
        json_encode($azure_search_parameters)
      );

      $response = $client->send($request);

      $azure_search = json_decode($response->getBody());

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

        $row['index'] = $index;
        $view->result[] = new ResultRow($row);
        $index++;
      }
    } catch (\Exception $exception) {
      \Drupal::logger('azure_search_settings')->error($exception->getMessage());
    }

    parent::execute($view);
    //TODO: Change the autogenerated stub
  }

  /**
   * Adds a simple condition to the query. Collect data on the configured filter
   * criteria so that we can appropriately apply it in the query() and execute()
   * methods.
   *
   * @param $group
   *   The WHERE group to add these to; groups are used to create AND/OR
   *   sections. Groups cannot be nested. Use 0 as the default group.
   *   If the group does not yet exist it will be created as an AND group.
   * @param $field
   *   The name of the field to check.
   * @param $value
   *   The value to test the field against. In most cases, this is a scalar. For more
   *   complex options, it is an array. The meaning of each element in the array is
   *   dependent on the $operator.
   * @param $operator
   *   The comparison operator, such as =, <, or >=. It also accepts more
   *   complex options such as IN, LIKE, LIKE BINARY, or BETWEEN. Defaults to =.
   *   If $field is a string you have to use 'formula' here.
   *
   * @see \Drupal\Core\Database\Query\ConditionInterface::condition()
   * @see \Drupal\Core\Database\Query\Condition
   */

  public function addWhere($group, $field, $value = NULL, $operator = NULL) {
    // Ensure all variants of 0 are actually 0. Thus '', 0 and NULL are all
    // the default group.
    if (empty($group)) {
      $group = 0;
    }
    // Check for a group.
    if (!isset($this->where[$group])) {
      $this->setWhereGroup('AND', $group);
    }
    $this->where[$group]['conditions'][] = [
      'field' => $field,
      'value' => $value,
      'operator' => $operator,
    ];
  }
}