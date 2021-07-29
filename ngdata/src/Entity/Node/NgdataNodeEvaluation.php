<?php

namespace Drupal\ngdata\Entity\Node;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 * Class NgdataNodeEvaluation.
  \Drupal::service('ngdata.node.evaluation')->demo();
 */
class NgdataNodeEvaluation extends NgdataNode {

  /**
   * Constructs a new NgdataNodeEvaluation object.
   */
  public function __construct() {

  }

  /**
   *
   // example
     $output = array(
       "4" => $FlexpageEventLayout->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), 1),
       "3" => $FlexpageEventLayout->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), 2),
       "2" => $FlexpageEventLayout->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), 3),
       "1" => $FlexpageEventLayout->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), 4),
       "0" => $FlexpageEventLayout->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), 5),
     );
   */
  public function getRaidoQuestionData($question_term = NULL, $meeting_nodes = array()) {
    $output = array();

    $FlexpageEventLayout = new FlexpageEventLayout();

    $question_scale = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_scale');

    if ($question_scale) {
      for ($i = 0; $i < $question_scale; $i++) {
        $output[$question_scale - $i - 1] = $FlexpageEventLayout->getQuestionAnswerByQuestionTid($meeting_nodes, $question_term->id(), ($i + 1));
      }
    }

    // sort array by low to high
    ksort($output);

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionDataWithReferOther($question_term = NULL, $meeting_nodes = array(), $refer_value = NULL) {
    $output = array();

    $FlexpageEventLayout = new FlexpageEventLayout();

    $question_scale = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_scale');

    $question_answer_all_data = $FlexpageEventLayout
      ->getQuestionAnswerAllDataWithReferOther($meeting_nodes, $question_term->id());

    $answer_count_values = [];
    if ($refer_value && isset($question_answer_all_data[$refer_value])) {
      $answer_count_values = array_count_values($question_answer_all_data[$refer_value]);
    }

    if ($question_scale) {
      for ($i = 0; $i < $question_scale; $i++) {
        $output[$question_scale - $i - 1] = 0;
        if (isset($answer_count_values[$i + 1])) {
          $output[$question_scale - $i - 1] = $answer_count_values[$i + 1];
        }
      }
    }

    // sort array by low to high
    ksort($output);

    return $output;
  }

  /**
   * @param $refer_field = 'refer_uid'/'refer_tid'/'refer_other'
   * @return integer number of Answer
   * @see old name getQuestionAnswerByQuestionTidByReferValue() => new name getNumberOfQuestionAnswerByQuestionTidByReferValue()
   */
  public function getNumberOfQuestionAnswerByQuestionTidByReferValue($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL, $refer_field = 'refer_uid', $refer_value = NULL) {
    $output = 0;

    $evaluation_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer'] == $question_answer && $row[$refer_field] == $refer_value) {
            $output++;

            break;
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getQuestionAnswerByQuestionTidWithQuestionAnswer($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL) {
    $evaluation_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = 0;
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer'] == $question_answer) {
            $output++;

            break;
          }
        }
      }
    }

    return $output;
  }

  /**
   * @param $refer_field = 'refer_uid' / 'refer_tid' / 'refer_other'
   * @return integer, $output = 0; The number of Evaluation have Correct Answer
   */
  public function getNumberOfEvaluationByQuestionCorrectAnswerByReferValue($meeting_nodes = array(), $correct_answer_question_tid = NULL, $refer_field = 'refer_uid', $refer_value = NULL) {
    $output = 0;

    $question_term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($correct_answer_question_tid);

    $correct_answer = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_answer');

    // if not set correct_answer, use last one
    if (!$correct_answer) {
      $question_scale = \Drupal::service('flexinfo.field.service')
        ->getFieldFirstValue($question_term, 'field_queslibr_scale');
      if ($question_scale) {
        $correct_answer = $question_scale;
      }
    }

    $output = $this->getNumberOfQuestionAnswerByQuestionTidByReferValue(
      $meeting_nodes,
      $correct_answer_question_tid,
      $correct_answer,
      $refer_field,
      $refer_value
    );

    return $output;
  }

}
