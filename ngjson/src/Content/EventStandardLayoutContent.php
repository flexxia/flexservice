<?php

/**
 * @file
 */

namespace Drupal\ngjson\Content;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 *
 */
class EventStandardLayoutContent {

  /**
   * @param $type_tid is 'pre' 'post'
   */
  public function getQuestionAnswerByQuestionTidWithEvaluationType($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL, $type_tid = NULL) {
    $evaluation_nodes = \Drupal::getContainer()
      ->get('baseinfo.querynode.service')
      ->wrapperEvaluationNodesFromMeetingNodesAndType($meeting_nodes, $type_tid);

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

  //
  public $row_break_num;

  /**
   *
   */
  public function __construct() {
    $this->row_break_num = 0;
  }

  /**
   *
   */
  public function blockEventsSnapshot($meeting_nodes = array(), $evaluationform_tid = NULL, $view_type = 'meeting_view') {
    $output = array();

    if ($evaluationform_tid) {
      $evaluationform_term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($evaluationform_tid);

      if ($evaluationform_term && $evaluationform_term->getVocabularyId() == 'evaluationform') {
        $question_tids = \Drupal::getContainer()
          ->get('flexinfo.field.service')
          ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

        // manually loop QuestionType for Radios
        $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Overall Program Satisfaction'));
        $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'General Satisfaction'));
        $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Speakers'));
        $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'General'));
        $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Hospitality'));
        $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Impact'));
        $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'Learning Objective'));
        $output = array_merge($output, $this->blockGroupRadioQuestionByQuestionType($meeting_nodes, $question_tids, 'PrePost'));

        // $output = array();
        // SelectKey
        $output = array_merge($output, $this->blockGroupForSelectKeyQuestion($meeting_nodes, $question_tids, $evaluationform_term));

        // Radios table for Multiple Speaker
        if ($view_type == 'meeting_view') {
          $output = array_merge($output, $this->blockGroupRadioQuestionShowMultipleTable($meeting_nodes, $evaluationform_term));
        }

        // $output = array_merge($output, $this->blockEventsSnapshotRankingQuestions($meeting_nodes, $evaluationform_term));

        $output = array_merge($output, $this->blockGroupForTextfieldComment($meeting_nodes, $question_tids));
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupRadioQuestionByQuestionType($meeting_nodes = array(), $question_tids = array(), $questiontype = 'General') {
    $output = array();

    $filter_tids = \Drupal::getContainer()
      ->get('baseinfo.queryterm.service')
      ->wrapperQuestionTidsByRadiosByQuestiontype($question_tids, $questiontype);

    $sort_tids = array_intersect($question_tids, $filter_tids);

    $output = \Drupal::service('ngdata.atomic.blockgroup')
      ->getBlockGroupByRadioQuestion($meeting_nodes, $sort_tids, $this->row_break_num);

    $this->row_break_num += count($sort_tids);

    return $output;
  }

  /**
   *
   */
  public function blockGroupForSelectKeyQuestion($meeting_nodes = array(), $question_tids = array(), $evaluationform_term = NULL) {
    $output = array();

    $filter_tids = \Drupal::getContainer()
      ->get('baseinfo.queryterm.service')
      ->wrapperFieldtypeQuestionTidsFromEvaluationform('selectkey', $evaluationform_term);

    $sort_tids = array_intersect($question_tids, $filter_tids);

    $output = \Drupal::service('ngdata.atomic.blockgroup')
      ->getBlockGroupChartBySelectKeyQuestion($meeting_nodes, $sort_tids);

    return $output;
  }

  /**
   *
   */
  public function blockGroupRadioQuestionShowMultipleTable($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = array();

    $speaker_uids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds(current($meeting_nodes), 'field_meeting_speaker');

    if ($speaker_uids && count($speaker_uids) > 1) {
      $question_tids = \Drupal::getContainer()
        ->get('baseinfo.queryterm.service')
        ->wrapperMultipleQuestionTidsFromEvaluationform($evaluationform_term);

      $output = \Drupal::service('ngdata.atomic.blockgroup')
        ->getBlockGroupByRadioQuestionMultipleByReferUid($question_tids, $meeting_nodes);
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupForTextfieldComment($meeting_nodes = array(), $question_tids = array()) {
    $textfield_tid= \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidByTermName($term_name = 'textfield', $vocabulary_name = 'fieldtype');

    $textfield_question_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $textfield_tid);

    $sort_tids = array_intersect($question_tids, $textfield_question_tids);

    $output = \Drupal::service('ngdata.atomic.blockgroup')
      ->getBlockGroupByCommentQuestion($meeting_nodes, $sort_tids);

    return $output;
  }

}
