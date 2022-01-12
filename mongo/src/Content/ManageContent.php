<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/mongo/src/Content/ManageContent.php');

  $ManageContent = new ManageContent();
  $ManageContent->runInsert();
 */

/**
 * Execute a database query
 *
 * http://zetcode.com/db/mongodbphp/
 */

use Drupal\Component\Utility\Timer;

/**
 *
 */
class ManageContent {

  /**
   *
   */
  public function getFields() {
    $output = array(
      "field_evaluation_meetingnid",
      "field_evaluation_reactset",
    );

    return $output;
  }

  /**
   *
   */
  public function getEvaluationNids() {
    $query = \Drupal::entityQuery('node');

    $query->condition('status', 1);
    $query->condition('type', 'evaluation');
    // $query->range(0, 3);

    $result = $query->execute();

    return array_values($result);
  }

  /**
   *
   */
  public function runInsert() {
    $nids = $this->getEvaluationNids();

    foreach ($nids as $key => $nid) {
      $row = array();
      $row['uid'] = 1;
      $row['meeting_nid'] = 66;

      $result = \Drupal::getContainer()
        ->get('mongo.driver.set')
        ->bulkInsertFields($row);
    }
  }

  /**
   *
   */
  public function runInsertEvaluationTable() {
    $name = 'time_one';
    Timer::start($name);

    $evaluatio_nids = $this->getEvaluationNids();

    foreach ($evaluatio_nids as $evaluatio_nid) {
      $fieldsValueArray = $this->dbSelectFetchAllValue($evaluatio_nid, 'node__field_evaluation_reactset', $this->getEvaluationFields());

      $evaluatio_nid = $fieldsValueArray[0]->entity_id;
      $meeting_nid = $this->getMeetingNid($evaluatio_nid);
      if ($meeting_nid) {
        $answer_set = [];
        foreach ($fieldsValueArray as $key => $row) {
          $answer_set[$key]['question_tid'] = intval($row->field_evaluation_reactset_question_tid);
          $answer_set[$key]['question_answer'] = intval($row->field_evaluation_reactset_question_answer);
          if ($row->field_evaluation_reactset_refer_uid) {
            $answer_set[$key]['refer_uid'] = intval($row->field_evaluation_reactset_refer_uid);
          }
          if ($row->field_evaluation_reactset_refer_tid) {
            $answer_set[$key]['refer_uid'] = intval($row->field_evaluation_reactset_refer_tid);
          }
          if ($row->field_evaluation_reactset_refer_other) {
            $answer_set[$key]['refer_uid'] = intval($row->field_evaluation_reactset_refer_other);
          }
        }

        $this->runBulkInsertEvaluation($meeting_nid, $answer_set, $evaluatio_nid);
      }
    }

    Timer::stop($name);
    dpm(Timer::read($name) . 'ms');
  }

  /**
   *
   */
  public function getMeetingNid($evaluatio_nid) {
    $output = NULL;

    $getMeetingNids = $this->dbSelectFieldsValue(
      $evaluatio_nid,
      'node__field_evaluation_meetingnid',
      array('field_evaluation_meetingnid_target_id')
    );
    if ($getMeetingNids && is_array($getMeetingNids) && current($getMeetingNids) > 0) {
      $output = current($getMeetingNids);
    }

    return $output;
  }

  /**
   *
   */
  public function runBulkInsertEvaluation($meeting_nid = NULL, $answer_set = array(), $evaluatio_nid = NULL) {
    $doc = array();
    $doc['uid'] = 1;
    $doc['evaluatio_nid'] = intval($evaluatio_nid);
    $doc['meeting_nid'] = intval($meeting_nid);
    $doc['answer'] = $answer_set;

    $result = \Drupal::getContainer()
      ->get('mongo.driver.set')
      ->bulkInsertFields($doc);
  }

  /**
   *
   * $query->fields('tablename', ['entity_id', 'field_evaluation_reactset_question_tid']);
   */
  public function dbSelectFieldsValue($nid, $table = 'node__field_evaluation_reactset', $fields = array('field_evaluation_reactset_question_tid')) {
    $query = \Drupal::database()->select($table, 'tablename');
    $query->fields('tablename', $fields);
    $query->condition('tablename.entity_id', $nid);
    // $query->range(0, 3);

    $output = $query->execute()->fetchCol();

    return $output;
  }

  /**
   *
   */
  public function dbSelectFetchAllValue($nid, $table = 'node__field_evaluation_reactset', $fields = array('field_evaluation_reactset_question_tid')) {
    $query = \Drupal::database()->select($table, 'tablename');
    $query->fields('tablename', $fields);
    $query->condition('tablename.entity_id', $nid);

    $count = $query->countQuery()->execute()->fetchField();
    dpm($count);

    $output = $query->execute()->fetchAll();

    return $output;
  }

  /**
   *
   */
  public function getEvaluationFields() {
    $output = [
      'entity_id',
      'field_evaluation_reactset_question_tid',
      'field_evaluation_reactset_question_answer',
      'field_evaluation_reactset_refer_uid',
      'field_evaluation_reactset_refer_tid',
      'field_evaluation_reactset_refer_other',
    ];

    return $output;
  }

  /**
   *
    require_once(DRUPAL_ROOT . '/modules/custom/mongo/src/Content/ManageContent.php');

    $ManageContent = new ManageContent();
    $ManageContent->runMongoFind();
   */

  /**
   *
   */
  public function runMongoFind() {
    $name = 'time_one';
    Timer::start($name);

    $result = \Drupal::getContainer()
      ->get('mongo.driver.set')
      ->runQueryAggregate();

    Timer::stop($name);
    dpm(Timer::read($name) . 'ms');
  }

  /**
   *
   */
  public function runEntityQueryFind() {
    $name = 'time_one';
    Timer::start($name);

    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('evaluation');

    $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_reactset.question_tid', 2209);
    $query->condition($group);

    $nids = $query_container->runQueryWithGroup($query);

    dpm(count($nids));
    Timer::stop($name);
    dpm(Timer::read($name) . 'ms EntityQueryFind');
  }



}
