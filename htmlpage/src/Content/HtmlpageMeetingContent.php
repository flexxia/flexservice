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

    // Chartjs Bar debug.
    // $output['html_content'] = NULL;
    // $output['json_content'] = [];
    // $output['html_content'] .= \Drupal::service('htmlpage.content.samplepage')
    //   ->chartjsBar()['html_content'];
    // $output['json_content'][] = \Drupal::service('htmlpage.content.samplepage')
    //   ->chartjsBar()['json_content'];

    $evaluationform_tid = \Drupal::getContainer()
      ->get('flexinfo.node.service')
      ->getMeetingEvaluationformTid($meeting_node);

    $output = $this->meetingPageBlocksContent(array($meeting_node), $evaluationform_tid, 'meeting_view');

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
        // Check enable boolean value.
        $evaluation_layout_enable = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($evaluation_layout_term, 'field_evallayout_enable');
      }
    }

    if ($evaluation_layout_enable) {
      $output = $this->meetingPageBlocksContentByLayout($meeting_nodes, $evaluationform_tid, $view_type , $entity_id, $evaluation_layout_term);
    }
    else {
      $output = $this->meetingPageBlocksContentDefault($meeting_nodes, $evaluationform_tid, $view_type , $entity_id);
    }

    return $output;
  }

  /**
   *
   */
  public function meetingPageBlocksContentByLayout($meeting_nodes = array(), $evaluationform_tid = NULL, $view_type = 'meeting_view', $entity_id = NULL, $evaluation_layout_term = NULL) {
    $output = [];
    $output['html_content'] = NULL;
    $output['json_content'] = [];

    $question_terms = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($evaluation_layout_term, 'field_evallayout_questionset');

    $evaluationform_term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($evaluationform_tid);

    foreach ($question_terms as $key => $question_term) {
      $field_type = \Drupal::service('flexinfo.field.service')
        ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

      if ($field_type == 'customtext') {
      }
      elseif ($field_type == 'radios') {
        $block_chart_content = $this->blockContentChartForRadioQuestion($meeting_nodes, $question_term);
        $output['html_content'] .= $block_chart_content['html_content'];

        // array_values() remove the index key
        $output['json_content'] = array_values(
          array_merge($output['json_content'], $block_chart_content['json_content'])
        );
      }
      elseif ($field_type == 'selectkey') {
        $output['html_content'] .= $this->blockContentHtmlTableBySelectKeyAnswerQuestion($meeting_nodes, $question_term);
      }
      elseif ($field_type == 'textfield') {
        $output['html_content'] .= $this->blockContentCommentByQuestion($meeting_nodes, $question_term);
      }
      elseif ($field_type == 'titlesection') {
        $output['html_content'] .= $this->blockContentTitlesection($meeting_nodes, $question_term);
      }

      // Radios table for Multiple Speaker
      if ($view_type == 'meeting_view') {
        $output['html_content'] .= $this->blockGroupForRadioQuestionTableWithSpeaker($meeting_nodes, $evaluationform_term);
      }
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
        $output = $this->blockGroupForRadioQuestionChart($meeting_nodes, $question_tids);

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

        $output['html_content'] .= $this->blockGroupForTextfieldComment($meeting_nodes, $question_tids, $evaluationform_term);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupForRadioQuestionChart($meeting_nodes = array(), $question_tids = array()) {
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
        $block_chart_content = $this->blockContentChartForRadioQuestion($meeting_nodes, $question_term);
        $output['html_content'] .= $block_chart_content['html_content'];

        // array_values() remove the index key
        $output['json_content'] = array_values(
          array_merge($output['json_content'], $block_chart_content['json_content'])
        );
      }
    }

    return $output;
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

      if (is_array($question_tids) && $question_tids) {
        $question_terms = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadMultiple($question_tids);

        foreach ($question_terms as $question_term) {
          $question_relatedfield = \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValue($question_term, 'field_queslibr_relatedfield');
          if ($question_relatedfield == 'field_meeting_speaker') {
            $output .= $this->blockContentHtmlTableByMultipleSpeakerByReferUid($meeting_nodes, $question_term);
          }
        }
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
        $output .= $this->blockContentHtmlTableBySelectKeyAnswerQuestion($meeting_nodes, $question_term);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockGroupForTextfieldComment($meeting_nodes = array(), $question_tids = array(), $evaluationform_term = NULL) {
    $output = NULL;

    $filter_tids = \Drupal::service('baseinfo.queryterm.service')
      ->wrapperFieldtypeQuestionTidsFromEvaluationform('textfield', $evaluationform_term);

    $sort_tids = array_intersect($question_tids, $filter_tids);

    if (is_array($sort_tids)) {
      $textfield_question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($sort_tids);

      foreach ($textfield_question_terms as $textfield_question_term) {
        $output .= $this->blockContentCommentByQuestion($meeting_nodes, $textfield_question_term);
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockContentChartForRadioQuestion($meeting_nodes = array(), $question_term = NULL) {
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

      case 'PrePost Pie Chart Column12':
        // $output = $this->getBlockChartByPrePostQuestionForPieChartColumn12($question_term, $meeting_nodes);
        $output = $this->getBlockChartjsByRadioQuestionForPrePostPieChartColumn12($meeting_nodes, $question_term);
        break;

      default:
        $output = $this->getBlockChartjsByRadioQuestionForPie($meeting_nodes, $question_term);
        break;
    }

    return $output;
  }

  /**
   *
   */
  public function blockContentCommentByQuestion($meeting_nodes = array(), $question_term = NULL) {
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
  public function blockContentTitlesection($meeting_nodes = array(), $question_term = NULL) {
    $output = NULL;

    if ($question_term) {
      $output .= '<div class="col-xs-12 margin-top-16 margin-bottom-6 margin-left-6 font-size-16">';
        $output .= $question_term->getName();
      $output .= '</div>';
    }

    return $output;
  }

  /**
   *
   */
  public function blockContentHtmlTableByMultipleSpeakerByReferUid($meeting_nodes = array(), $question_term = NULL) {
    $output = NULL;

    $block_definition = [
      'title' => $question_term->getName(),
      'block_id' => 'html-block-table-question-' . $question_term->id(),
      'block_column' => 'col-xs-12',
      'chart_canvas_id' => NULL,
    ];

    $table_body = $this->getHtmlTableByMultipleSpeakerByReferUid($question_term, $meeting_nodes);

    $output = \Drupal::service('htmlpage.charthtmltemplate.section.html')
      ->blockHtmlTemplate($block_definition, $table_body);

    return $output;
  }

  /**
   *
   */
  public function blockContentHtmlTableBySelectKeyAnswerQuestion($meeting_nodes = array(), $question_term = NULL) {
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
  public function getHtmlTableByMultipleSpeakerByReferUid($question_term = NULL, $meeting_nodes = array()) {
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
   *
   */
  public function getBlockChartjsByRadioQuestionForPie($meeting_nodes, $question_term) {
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
    $output['json_content'][] = $base_pie_data;

    return $output;
  }

  /**
   * PrePost Pie Chart Column12.
   */
  public function getBlockChartjsByRadioQuestionForPrePostPieChartColumn12($meeting_nodes, $question_term) {
    $block_definition = [
      'title' => $question_term->getName(),
      'block_id' => 'chartjs-block-question-' . $question_term->id(),
      'block_column' => 'col-xs-12',
      'chart_canvas_id' => 'see below array',
      'multiple_chart' => [
        [
          'block_column' => 'col-xs-6',
          'chart_canvas_id' => 'chartjs-canvas-question-' . $question_term->id() . '-0',
        ],
        [
          'block_column' => 'col-xs-6',
          'chart_canvas_id' => 'chartjs-canvas-question-' . $question_term->id() . '-1',
        ],
      ],
    ];

    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.chartjs')
      ->blockChartjsTemplateForMeetingPageForPrePostPieChartColumn12($block_definition, $meeting_nodes, $question_term);

    //
    $base_pie_data = \Drupal::service('htmlpage.chartjsonbase.chartjstemplate')
      ->chartjsBasePiePure();
    $base_pie_data["content"]["data"]["labels"] = \Drupal::service('ngdata.atomic.atom')
      ->getRaidoQuestionLegend($question_term);;
    $base_pie_data["content"]["data"]["datasets"][0]["data"] = \Drupal::service('ngdata.node.evaluation')
      ->getRaidoQuestionDataWithReferOther($question_term, $meeting_nodes, 'Pre');
    $base_pie_data["content"]["data"]["datasets"][0]["backgroundColor"] = \Drupal::service('ngdata.term.question')
      ->getRaidoQuestionColors($question_term, TRUE);
    $base_pie_data['chart_canvas_id'] = $block_definition['multiple_chart'][0]['chart_canvas_id'];
    $output['json_content'][] = $base_pie_data;

    //
    $base_pie_data = \Drupal::service('htmlpage.chartjsonbase.chartjstemplate')
      ->chartjsBasePiePure();
    $base_pie_data["content"]["data"]["labels"] = \Drupal::service('ngdata.atomic.atom')
      ->getRaidoQuestionLegend($question_term);;
    $base_pie_data["content"]["data"]["datasets"][0]["data"] = \Drupal::service('ngdata.node.evaluation')
      ->getRaidoQuestionDataWithReferOther($question_term, $meeting_nodes, 'Post');
    $base_pie_data["content"]["data"]["datasets"][0]["backgroundColor"] = \Drupal::service('ngdata.term.question')
      ->getRaidoQuestionColors($question_term, TRUE);
    $base_pie_data['chart_canvas_id'] = $block_definition['multiple_chart'][1]['chart_canvas_id'];
    $output['json_content'][] = $base_pie_data;

    return $output;
  }

}
