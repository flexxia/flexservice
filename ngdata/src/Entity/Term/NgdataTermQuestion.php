<?php

namespace Drupal\ngdata\Entity\Term;

/**
 * Class NgdataTermQuestion.
 * \Drupal::service('ngdata.term.question')->demo();.
 */
class NgdataTermQuestion extends NgdataTerm {

  /**
   * Constructs a new NgdataTermQuestion object.
   */
  public function __construct() {
  }

  /**
   *
   */
  public function getChartLegendSortOrderValueByQuestionTerm($question_term = NULL) {
    $output = '';

    // sort the legend by ascent order
    $question_chartlegend_term = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetIdTermEntity($question_term, $field_name = 'field_queslibr_chartlegend');

    $output = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValue($question_chartlegend_term, $field_name = 'field_chartlegend_sortorder');

    return $output;
  }

  /**
   *
   */
  public function getQuestionAnswerAllData($meeting_nodes = array(), $question_tid = NULL) {
    $output = [];

    $evaluation_nodes = \Drupal::service('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $output[] = $row['question_answer'];
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getQuestionAnswerAllDataWithEvaluationNid($meeting_nodes = array(), $question_tid = NULL) {
    $output = [];

    $evaluation_nodes = \Drupal::service('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $answer = [
              'evaluation_nid' => $evaluation_node->id(),
              'question_answer' => $row['question_answer'],
            ];
            $output[] = $answer;
          }
        }
      }
    }

    return $output;
  }

  /**
   * Using ReferUid as Output Key.
   */
  public function getQuestionAnswerAllDataWithReferUidWithEvaluationNid($meeting_nodes = array(), $question_tid = NULL) {
    $evaluation_nodes = \Drupal::service('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = array();
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $answer = [
              'evaluation_nid' => $evaluation_node->id(),
              'question_answer' => $row['question_answer'],
              'refer_uid' => $row['refer_uid'],
            ];
            $output[$row['refer_uid']][] = $answer;
          }
        }
      }
    }

    return $output;
  }

  /**
   * Using EvaluationNid as Output Key.
   */
  public function getQuestionAnswerAllDataWithEvaluationNidWithReferUid($meeting_nodes = array(), $question_tid = NULL) {
    $evaluation_nodes = \Drupal::service('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = array();
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        $evaluation_nid = $evaluation_node->id();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $answer = [
              'evaluation_nid' => $evaluation_nid,
              'question_answer' => $row['question_answer'],
              'refer_uid' => $row['refer_uid'],
            ];
            $output[$evaluation_nid][] = $answer;
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionColors($question_term = NULL, $pound_sign = FALSE) {
    $color_palette = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_palette');

    if (!$color_palette) {
      $color_palette = 'EventPie5 Reverse';
    }
    $output = \Drupal::service('flexinfo.setting.service')
      ->colorPlateOutputKeyPlusOneByPaletteName($color_palette, $color_key = NULL, $pound_sign, 'f6f6f6');

    $question_scale = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_scale');

    // only output length is equal with question_scale
    if ($question_scale > 0) {
      $output = array_slice($output, 0, $question_scale);

      if ($this->getChartLegendSortOrderValueByQuestionTerm($question_term) == 'Ascend') {
        $output = array_reverse($output);
      }
    }

    return $output;
  }

  /**
   * @return Weighted Average mean
   */
  public function getRaidoQuestionTidsStatsAverage($question_tids = array(), $meeting_nodes = array()) {
    $output = 0;
    $question_data_total = array();

    $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);
    foreach ($question_terms as $key => $question_term) {
      $question_data = \Drupal::service('ngdata.node.evaluation')
        ->getRaidoQuestionData($question_term, $meeting_nodes);
      $question_data = array_reverse($question_data);

      $question_data_array[] = $question_data;
    }

    if ($question_data_array && is_array($question_data_array)) {
      foreach ($question_data_array as $question_data_sub) {
        foreach ($question_data_sub as $subkey => $row) {
          if (isset($question_data_total[$subkey])) {
            $question_data_total[$subkey] += $row;
          }
          else {
            $question_data_total[$subkey] = $row;
          }
        }
      }
    }

    if ($question_data_total) {
      $output = \Drupal::getContainer()
        ->get('flexinfo.calc.service')
        ->arrayAverageByCount($question_data_total, 1);
    }

    return $output;
  }

  /**
   * @return Average mean
   */
  public function getRaidoQuestionTidStatsAverage($question_tid = NULL, $meeting_nodes = array()) {
    $output = NULL;

    if ($question_tid) {
      $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_tid);
      if ($question_tid) {
        $output = $this->getRaidoQuestionTermStatsAverage($question_term, $meeting_nodes);
      }
    }

    return $output;
  }

  /**
   * @return Question Average mean
   */
  public function getRaidoQuestionTermStatsAverage($question_term = NULL, $meeting_nodes = array()) {
    $question_data = \Drupal::service('ngdata.node.evaluation')
      ->getRaidoQuestionData($question_term, $meeting_nodes);
    $question_data = array_reverse($question_data);

    $output = \Drupal::service('flexinfo.calc.service')
      ->arrayAverageByCount($question_data, 1);

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionsTidNTSScoreScale10($question_tid = NULL, $meeting_nodes = array(), $plus_sign = TRUE) {
    $question_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($question_tid);

    $output = $this->getRaidoQuestionsTermNTSScoreScale10($question_term, $meeting_nodes, $plus_sign);

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionsTermNTSScoreScale10($question_term = NULL, $meeting_nodes = array(), $plus_sign = TRUE) {
    $output = 0;

    $question_data = \Drupal::service('ngdata.node.evaluation')
      ->getRaidoQuestionData($question_term, $meeting_nodes);

    $output = \Drupal::service('flexinfo.calc.service')
      ->calcNTSScoreScale10($question_data, $plus_sign);

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionCorrectAnswerData($question_term = NULL, $meeting_nodes = array(), $type_tid = NULL) {
    $output = $this->getQuestionAnswerByQuestionTidWithEvaluationType($meeting_nodes, $question_term->id(), 5, $type_tid);
    return $output;
  }

  /**
   *
   */
  public function getSelectkeyQuestionData($question_term = NULL, $meeting_nodes = array()) {
    $all_answer_terms = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($question_term, 'field_queslibr_selectkeyanswer');

    $result = $this->getQuestionAnswerAllData($meeting_nodes, $question_term->id());

    $answer_data_count = array_count_values($result);

    foreach ($all_answer_terms as $key => $answer_term) {
      $num_answer = isset($answer_data_count[$key]) ? $answer_data_count[$key] : 0;

      $output[$answer_term->getName()] = $num_answer;
    }

    return $output;
  }

  /**
   *
   */
  public function getTextfieldQuestionAllData($meeting_nodes = array(), $question_tid = NULL) {
    $output = array();

    $evaluation_nodes = \Drupal::service('baseinfo.querynode.service')
      ->wrapperEvaluationNodeFromMeetingNodes($meeting_nodes);

    $output = $this->getTextfieldQuestionAllDataFromEvaluationNodes($evaluation_nodes, $question_tid);

    return $output;
  }

  /**
   *
   */
  public function getTextfieldQuestionAllDataFromEvaluationNodes($evaluation_nodes = array(), $question_tid = NULL) {
    $output = array();

    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer']) {
            $output[] = $row['question_answer'];

            break;
          }
        }
      }
    }

    return $output;
  }

}
