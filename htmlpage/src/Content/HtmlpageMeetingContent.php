<?php

/**
 * @file
 */

namespace Drupal\htmlpage\Content;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 * \Drupal::service('htmlpage.content.meeting')->demo().
 */
class HtmlpageMeetingContent {

  /**
   *
   */
  public function meetingPageBlocks($meeting_node = NULL) {
    $output = [];
    $output['html_content'] = NULL;
    $output['json_content'] = [];

    // Chartjs Bar debug.
    // $output['html_content'] .= $this->debugChartjsBar()['html_content'];
    // $output['json_content'][] = $this->debugChartjsBar()['json_content'];

    $evaluationform_tid = \Drupal::getContainer()
      ->get('flexinfo.node.service')
      ->getMeetingEvaluationformTid($meeting_node);

    $blocks_content = $this->meetingPageBlocksContent(array($meeting_node), $evaluationform_tid, 'meeting_view');
    $output['html_content'] .= $blocks_content['html_content'];
    $output['json_content'] = $blocks_content['json_content'];

    return $output;
  }

  /**
   *
   */
  public function meetingPageBlocksContent($meeting_nodes = array(), $evaluationform_tid = NULL, $view_type = 'meeting_view', $entity_id = NULL) {
    $output = [];

    $evaluation_layout_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperTermTidsByField('evaluationlayout', 'field_evallayout_form', $evaluationform_tid);

    $evaluation_layout_enable = FALSE;
    if ($evaluation_layout_tids) {
      $evaluation_layout_term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')->load($evaluation_layout_tids[0]);

      if ($evaluation_layout_term) {
        $evaluation_layout_enable = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($evaluation_layout_term, 'field_evallayout_enable');
      }
    }

    if ($evaluation_layout_enable) {
      // $output = $this->blockEventsSnapshotByEvaluationLayout($meeting_nodes, $evaluationform_tid, $view_type , $entity_id, $evaluation_layout_term);
    }
    else {
      $output = $this->meetingPageBlocksContentDefault($meeting_nodes, $evaluationform_tid, $view_type , $entity_id);
    }

    return $output;
  }

  /**
   *
   */
  public function meetingPageBlocksContentDefault($meeting_nodes = array(), $evaluationform_tid = NULL, $view_type = 'meeting_view', $entity_id = NULL) {
    $output = [];

    if ($evaluationform_tid) {
      $evaluationform_term = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->load($evaluationform_tid);

      if ($evaluationform_term && $evaluationform_term->getVocabularyId() == 'evaluationform') {
        $question_tids = \Drupal::service('flexinfo.field.service')
          ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

        // PrePost is one of Radio Question. All Radio Question
        $output = $this->blockGroupForChartRadioQuestion($meeting_nodes, $question_tids);

        // SelectKey
        $output['html_content'] .= $this->blockGroupForSelectKeyQuestion($meeting_nodes, $question_tids, $evaluationform_term);

        // Radios table for Multiple Speaker
        if ($view_type == 'meeting_view') {
          $output['html_content'] .= $this->blockGroupForRadioQuestionTableWithSpeaker($meeting_nodes, $evaluationform_term);
        }
        // elseif ($view_type == 'program_view') {
        //   $output = array_merge($output, $this->blockGroupRadioQuestionTableWithMeetingField($meeting_nodes, $evaluationform_term, $entity_id));
        // }

        // $output = array_merge($output, $this->blockEventsSnapshotRankingQuestions($meeting_nodes, $evaluationform_term));

        $output['html_content'] .= $this->blockGroupForTextfieldComment($meeting_nodes, $question_tids);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupForSelectKeyQuestion($meeting_nodes = array(), $question_tids = array(), $evaluationform_term = NULL) {
    $output = NULL;

    $filter_tids = \Drupal::service('baseinfo.queryterm.service')
      ->wrapperFieldtypeQuestionTidsFromEvaluationform('selectkey', $evaluationform_term);

    $sort_tids = array_intersect($question_tids, $filter_tids);

    if (is_array($sort_tids) && $sort_tids) {
      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($sort_tids);

      foreach ($question_terms as $question_term) {
        $output .= $this->getBlockHtmlTableBySelectKeyAnswerQuestion($meeting_nodes, $question_term);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockHtmlTableBySelectKeyAnswerQuestion($meeting_nodes = array(), $question_term = NULL) {
    $output = NULL;

    $block_definition = [
      'title' => $question_term->getName(),
      'block_id' => 'html-block-table-question-' . $question_term->id(),
      'block_column' => 'col-xs-12',
      'chart_canvas_id' => NULL,
    ];

    $table_body = $this->getHtmlTableBySelectKeyAnswerQuestion($question_term, $meeting_nodes);

    $output = \Drupal::service('htmlpage.charthtmltemplate.section.html')
      ->blockHtmlTemplate($block_definition, $table_body);

    return $output;
  }

  /**
   *
   */
  public function getHtmlTableBySelectKeyAnswerQuestion($question_term = NULL, $meeting_nodes = array()) {
    $FlexpageEventLayout = new FlexpageEventLayout();

    $pool_data = $FlexpageEventLayout->getQuestionAnswerAllData($meeting_nodes, $question_term->id());
    $pool_data_sum = count($pool_data);

    $pool_data_count = array_count_values($pool_data);

    $table = NULL;
    if ($pool_data_sum) {
      $table .= '<table class="table table-hover margin-bottom-0">';
        $table .= '<thead class="font-bold">';
          $table .= '<tr>';
            $table .= '<th>';
              $table .= 'Name';
            $table .= '</th>';
            $table .= '<th>';
              $table .= 'Number of Responses';
            $table .= '</th>';
            $table .= '<th>';
              $table .= 'Percentage';
            $table .= '</th>';
          $table .= '</tr>';
        $table .= '</thead>';

        foreach ($pool_data_count as $key => $row) {
          $selectAnswerTerm = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->load($key);

          $selectAnswerTermPercentage = \Drupal::getContainer()
            ->get('flexinfo.calc.service')
            ->getPercentageDecimal($row, $pool_data_sum, 0) . '%';

          $table .= '<tbody>';
            $table .= '<tr>';
              $table .= '<th class="font-weight-normal">';
                $table .= $selectAnswerTerm->label();
              $table .= '</th>';
              $table .= '<th class="font-weight-normal">';
                $table .= $row;
              $table .= '</th>';
              $table .= '<th class="font-weight-normal">';
                $table .= $selectAnswerTermPercentage;
              $table .= '</th>';
            $table .= '</tr>';
          $table .= '</tbody>';
        }

      $table .= '</table>';
    }

    return $table;
  }

  /**
   * Html Table for Multiple Speaker for Radios question.
   */
  public function blockGroupForRadioQuestionTableWithSpeaker($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = NULL;

    $speaker_uids = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIds(current($meeting_nodes), 'field_meeting_speaker');

    if ($speaker_uids && count($speaker_uids) > 1) {
      $question_tids = \Drupal::service('baseinfo.queryterm.service')
        ->wrapperMultipleQuestionTidsFromEvaluationform($evaluationform_term);

      $output = $this->getBlockGroupTableMultipleSpeakerByReferUid($meeting_nodes, $question_tids);
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockGroupTableMultipleSpeakerByReferUid($meeting_nodes = array(), $question_tids = array()) {
    $output = NULL;

    if (is_array($question_tids) && $question_tids) {
      $question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {
        $question_relatedfield = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($question_term, 'field_queslibr_relatedfield');
        if ($question_relatedfield == 'field_meeting_speaker') {
          $output .= $this->getBlockHtmlTableForMultipleSpeakerByReferUid($meeting_nodes, $question_term);
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockHtmlTableForMultipleSpeakerByReferUid($meeting_nodes = array(), $question_term = NULL) {
    $output = NULL;

    $block_definition = [
      'title' => $question_term->getName(),
      'block_id' => 'html-block-table-question-' . $question_term->id(),
      'block_column' => 'col-xs-12',
      'chart_canvas_id' => NULL,
    ];

    $table_body = $this->htmlTableContentByMultipleSpeakerByReferUid($question_term, $meeting_nodes);

    $output = \Drupal::service('htmlpage.charthtmltemplate.section.html')
      ->blockHtmlTemplate($block_definition, $table_body);

    return $output;
  }

  /**
   *
   */
  public function htmlTableContentByMultipleSpeakerByReferUid($question_term = NULL, $meeting_nodes = array()) {
    $FlexpageEventLayout = new FlexpageEventLayout();
    $pool_data = $FlexpageEventLayout->getQuestionAnswerAllDataWithReferUid($meeting_nodes, $question_term->id());

    $table = NULL;
    if ($pool_data && count($pool_data) > 1) {
      $table .= '<table class="table table-hover margin-bottom-0 font-size-12">';
        $table .= '<thead class="font-bold">';
          $table .= '<tr>';
            $table .= '<th>';
              $table .= 'Name';
            $table .= '</th>';
            $table .= '<th>';
              $table .= 'Total';
            $table .= '</th>';
            for ($i = 5; $i > 0; $i--) {
              $table .= '<th>';
                $table .= $i;
              $table .= '</th>';
            }
          $table .= '</tr>';
        $table .= '</thead>';

        foreach ($pool_data as $key => $row) {
          if ($key) {
            $user = \Drupal::entityTypeManager()
              ->getStorage('user')
              ->load($key);

            if ($user) {
              $count_values = array_count_values($row);

              $table .= '<tbody>';
                $table .= '<tr>';
                  $table .= '<th class="font-weight-normal">';
                    $table .= $user->getDisplayName();
                  $table .= '</th>';
                  $table .= '<th class="font-weight-normal">';
                    $table .= count($row);
                  $table .= '</th>';
                  for ($i = 5; $i > 0; $i--) {
                    $cell_value = isset($count_values[$i]) ? $count_values[$i] : 0;

                    $table .= '<th class="font-weight-normal">';
                      $table .= $cell_value;
                      $table .= ' (' . \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($cell_value, count($row), 0) . '%)';
                    $table .= '</th>';
                  }
                $table .= '</tr>';
              $table .= '</tbody>';
            }
          }
        }

      $table .= '</table>';
    }

    return $table;
  }

  /**
   *
   */
  public function blockGroupForChartRadioQuestion($meeting_nodes = array(), $question_tids = array()) {
    $output = [];
    $output['html_content'] = NULL;
    $output['json_content'] = [];

    $radio_tids = \Drupal::service('baseinfo.queryterm.service')
      ->wrapperQuestionTidsOnlyRadios($question_tids);

    $sort_tids = array_intersect($question_tids, $radio_tids);

    if (is_array($question_tids)) {
      $question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($sort_tids);

      foreach ($question_terms as $question_term) {
        $block_chart_content = $this->getBlockChartForRadioQuestion($meeting_nodes, $question_term);
        $output['html_content'] .= $block_chart_content['html_content'];
        $output['json_content'][] = $block_chart_content['json_content'];
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupForTextfieldComment($meeting_nodes = array(), $question_tids = array()) {
    $textfield_tid= \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = 'textfield', $vocabulary_name = 'fieldtype');

    $textfield_question_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $textfield_tid);

    $sort_tids = array_intersect($question_tids, $textfield_question_tids);

    $output = $this->getBlockGroupCommentContent($meeting_nodes, $sort_tids);

    return $output;
  }

  /**
   *
   */
  public function getBlockGroupCommentContent($meeting_nodes = array(), $textfield_question_tids = array()) {
    $output = NULL;

    if (is_array($textfield_question_tids)) {
      $textfield_question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($textfield_question_tids);

      foreach ($textfield_question_terms as $textfield_question_term) {
        $output .= $this->blockCommentContentByQuestion($meeting_nodes, $textfield_question_term);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockCommentContentByQuestion($meeting_nodes = array(), $question_term = NULL) {
    $output = NULL;

    $block_definition = [
      'title' => $question_term->getName(),
      'block_id' => 'html-block-comments-question-' . $question_term->id(),
      'block_column' => 'col-xs-12',
      'chart_canvas_id' => NULL,
    ];

    if ($question_term) {
      $question_answers = \Drupal::service('ngdata.term.question')
        ->getTextfieldQuestionAllData($meeting_nodes, $question_term->id());

      if (isset($question_answers) && $question_answers !== NULL) {
        $comments = NULL;
        $comments .= '<ul class="padding-bottom-2 bg-ffffff font-size-12 margin-left-0">';
          foreach ($question_answers as $key => $row) {
            $comments .= '<li>' . $row . '</li>';
          }
        $comments .= '</ul">';
      }
    }

    $output = \Drupal::service('htmlpage.charthtmltemplate.section.html')
      ->blockHtmlTemplate($block_definition, $comments);

    return $output;
  }

  /**
   *
   */
  public function getBlockChartForRadioQuestion($meeting_nodes = array(), $question_term = NULL) {
    $output = array();

    $charttype_name = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_charttype');

    switch ($charttype_name) {
      // case 'Bar Chart':
      //   $output = $this->getBlockChartByRadioQuestionForBar($question_term, $meeting_nodes);
      //   break;

      // case 'Bar Chart Correct Answer':
      //   $output = $this->getBlockChartByPrePostQuestionWithAnswerForBar($question_term, $meeting_nodes);
      //   break;

      // case 'Stacked Bar Chart Multiple':
      //   break;

      // case 'PrePost Multiple Stacked Horizontal Bar Chart Column12':
      //   $output = $this->getBlockChartByPrePostQuestionForStackedBarMultipleHorizontalColumn12($question_term, $meeting_nodes);
      //   break;

      // case 'PrePost Pie Chart Column12':
      //   $output = $this->getBlockChartByPrePostQuestionForPieChartColumn12($question_term, $meeting_nodes);
      //   break;

      default:
        $output = $this->blockChartjsByRadioQuestionForPie($meeting_nodes, $question_term);
        break;
    }

    return $output;
  }

  /**
   *
   */
  public function blockChartjsByRadioQuestionForPie($meeting_nodes, $question_term) {
    $block_definition = [
      'title' => $question_term->getName(),
      'block_id' => 'chartjs-block-question-' . $question_term->id(),
      'block_column' => 'col-xs-6',
      'chart_canvas_id' => 'chartjs-canvas-question-' . $question_term->id(),
    ];

    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.chartjs')
      ->blockChartjsTemplateForMeetingPage($block_definition, $meeting_nodes, $question_term);

    //
    $base_pie_data = \Drupal::service('htmlpage.chartjsonbase.chartjstemplate')
      ->chartjsBasePiePure();
    $base_pie_data["content"]["data"]["labels"] = \Drupal::service('ngdata.atomic.atom')
      ->getRaidoQuestionLegend($question_term);;
    $base_pie_data["content"]["data"]["datasets"][0]["data"] = \Drupal::service('ngdata.node.evaluation')
      ->getRaidoQuestionData($question_term, $meeting_nodes);
    $base_pie_data["content"]["data"]["datasets"][0]["backgroundColor"] = \Drupal::service('ngdata.term.question')
      ->getRaidoQuestionColors($question_term, TRUE);
    $base_pie_data['chart_canvas_id'] = $block_definition['chart_canvas_id'];
    $output['json_content'] = $base_pie_data;

    return $output;
  }

  /**
   * Chartjs.
   */
  public function debugChartjsBar() {
    $output = [];
    $block_definition = [
      'title' => 'Chartjs Multiple Bar Chart',
      'block_id' => 'chartjs-bar-sample-block-2',
      'chart_canvas_id' => 'chartjs-bar-sample-2',
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.chartjs')
      ->blockChartjsTemplate($block_definition);
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.chartjstemplate')
      ->chartjsBaseMultipleBar();

    return $output;
  }

}
