<?php

/**
 * @file
 */

namespace Drupal\genpdf\Content;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Unicode;

use Drupal\flexpage\Content\FlexpageBaseJson;
use Drupal\flexpage\Content\FlexpageEventLayout;
use Drupal\flexpage\Content\FlexpageJsonGenerator;
use Drupal\flexpage\Content\FlexpageSampleDataGenerator;

use Drupal\dashpage\Content\DashpageObjectContent;
use Drupal\taxonomy\Entity\Term;

/**
 * An example controller.
 $GenpdfJsonGenerator = new GenpdfJsonGenerator();
 $GenpdfJsonGenerator->runGenPdf();
 */
class GenpdfJsonGenerator extends ControllerBase {

  public $FlexpageBaseJson;
  public $FlexpageJsonGenerator;
  public $FlexpageSampleDataGenerator;

 /**
  *
  */
  public function __construct() {
   $this->FlexpageBaseJson = new FlexpageBaseJson();
   $this->FlexpageJsonGenerator = new FlexpageJsonGenerator();
   $this->FlexpageSampleDataGenerator = new FlexpageSampleDataGenerator();
  }

  /**
   *
   */
  public function programJson($entity_id = NULL, $meeting_nodes = array()) {
    $program_entity = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($entity_id);

    $evaluationform_tid = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetId($program_entity, 'field_program_evaluationform');

    $evaluationform_term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($evaluationform_tid);

    $output = $this->eventsData($meeting_nodes, $evaluationform_term);

    return $output;
  }

  /**
  *
  */
  public function meetingJson($entity_id = NULL) {
    $node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load($entity_id);

    $evaluationform_term = \Drupal::getContainer()
      ->get('flexinfo.node.service')
      ->getMeetingEvaluationformTerm($node);

    $output = $this->eventsData(array($node), $evaluationform_term);

    return $output;
  }

  /**
   *
   */
  public function eventsData($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = array();

    if (empty($evaluationform_term)) {
      return $output;
    }
    if ($evaluationform_term->getVocabularyId() != 'evaluationform') {
      return $output;
    }

    $question_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    $output['meeting'] = $this->blockEventInfo($meeting_nodes[0]);

    $output['chartSection']   = $this->blockEventsChart($meeting_nodes, $evaluationform_term, $question_tids);

    $output['commentSection'] = $this->blockEventsComments($meeting_nodes, $evaluationform_term, $question_tids);

    $tableSection = $this->blockEventsTableForRelatedFieldQuestion($meeting_nodes, $evaluationform_term, $question_tids);
    $output['tableSection']['question'] = array_merge(
      $tableSection,
      $this->blockEventsTableForSelectkeyQuestion($meeting_nodes, $evaluationform_term, $question_tids)
    );

    return $output;
  }

  /**
   *
   */
  public function blockEventInfo($meeting_node = NULL) {
    $output = array();

    $DashpageObjectContent = new DashpageObjectContent();

    $output = array(
      'programName' => \Drupal::getContainer()
        ->get('flexinfo.field.service')
        ->getFieldFirstTargetIdTermName($meeting_node, 'field_meeting_program'),
      'tileSection' => $DashpageObjectContent->blockTileMeetingValue($meeting_node),
    );

    return $output;
  }

  /**
   *
   */
  public function getTermNameByFliedMachineName($term, $field_name) {
    $objective_name = NULL;

    $objective_goal_term = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetIdToEntity($term, 'taxonomy_term', $field_name);

    if ($objective_goal_term) {
      $objective_name = $objective_goal_term->getName();
    }

    return $objective_name;
  }

  /**
   *
   */
  public function getEventChartColorPalette($question_scale) {
    $color_palette = array(
      '1' => 'f24b99',
      '2' => 'f3c848',
      '3' => 'c6c6c6',
      '4' => '05d23e',
      '5' => '2fa9e0',
      '6' => 'bfbfbf',
      '7' => 'd6006e',
    );

    if ($question_scale == 3) {
      $color_palette = array(
        '3' => 'f24b99',
        '2' => '05d23e',
        '1' => '2fa9e0',
      );
    }

    if ($question_scale == 10) {
      $color_palette = array(
        '9' => '2fa9e0',
        '8' => '0099ff',
        '7' => '05d23e',
        '6' => '009900',
        '5' => 'c6c6c6',
        '4' => 'f7d417',   // 5
        '3' => 'ff9933',
        '2' => 'ff66ff',
        '1' => 'ff66cc',
        '0' => 'f24b99',
      );
    }

    return $color_palette;
  }

  /**
   *
   */
  public function blockEventsChart($meeting_nodes = array(), $evaluationform_term = NULL, $question_tids = array()) {
    $output = array();

    $filter_tids = \Drupal::getContainer()->get('baseinfo.queryterm.service')->wrapperQuestionTidsOnlyRadios($question_tids, FALSE);

    $question_tids = array_intersect($question_tids, $filter_tids);

    if (is_array($question_tids) && $question_tids) {
      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);
      foreach ($question_terms as $question_term) {
        if (isset($question_term)) {

          // radios is 2493
          $question_tid  = $question_term->id();
          $question_scale = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_scale');

          // $chart_render_method like "renderChartPieDataSet"
          // $chart_render_method = \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTypeRenderFunctionByQuestion($question_term);
          $chart_render_method = "renderChartNewPieDataSet";

          $chart_type_id = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetId($question_term, 'field_queslibr_charttype');

          $chart_type_name = \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_charttype');

          $pre_question_answer = [];
          $post_question_answer = [];
          $pool_data = [];
          $pool_label = [];
          for ($i = 0; $i < $question_scale; $i++) {
            if ($chart_type_name == 'Stacked Bar Chart Multiple Horizontal') {
              $pre_question_answer[$i] = $this->filterPrePostQuestionData($meeting_nodes, $question_tid, ($i), 'Pre');
              $post_question_answer[$i] = $this->filterPrePostQuestionData($meeting_nodes, $question_tid, ($i), 'Post');
            }
            $pool_label[$i + 1] = $i + 1;
          }

          // new
          if (\Drupal::service('ngdata.node.evaluation')
            ->getRaidoQuestionData($question_term, $meeting_nodes)) {

            $pool_data = array_reverse(\Drupal::service('ngdata.node.evaluation')
              ->getRaidoQuestionData($question_term, $meeting_nodes));
          }

          $color_palette = array();
          // $color_palette = $this->getEventChartColorPalette($question_scale);

          if (\Drupal::service('ngdata.term.question')
            ->getRaidoQuestionColors($question_term, TRUE)) {

            $color_palette = array_reverse(\Drupal::service('ngdata.term.question')
              ->getRaidoQuestionColors($question_term, TRUE));

            if ($question_scale > 5) {
              if (count($color_palette) < 6) {
                $color_palette = $this->getEventChartColorPalette($question_scale);
              }
            }
          }
          else {
            $color_palette = $this->getEventChartColorPalette($question_scale);
          }

          $data_legend = array();
          if (\Drupal::service('ngdata.atomic.atom')
            ->getRaidoQuestionLegend($question_term)) {

            $data_legend = array_reverse(\Drupal::service('ngdata.atomic.atom')
              ->getRaidoQuestionLegend($question_term));

            $temp_date_legend = array();
            foreach ($data_legend as $key => $value) {
              $temp_date_legend[$key + 1] = $value;
            }
            $data_legend = $temp_date_legend;
          }
          else {
            $data_legend = $this->defaultChartLegend($question_scale);
            // $color_palette = $this->getEventChartColorPalette($question_scale);
          }

          $temp_color_palette = array();
          foreach ($color_palette as $key => $value) {
            $temp_color_palette[$key + 1] = $value;
          }
          $color_palette = $temp_color_palette;

          $rightChartClass = NULL;
          $styleWidth = NULL;

          if ($chart_type_name) {
            $chartClass = $chart_type_name;
          } else {
            $chartClass = 'Pie Chart';
          }

          if ($chart_type_name == 'Stacked Bar Chart Multiple Horizontal') {

            // RIGHT chart character
            // $rightChartClass = 'Stacked Bar Chart Multiple Horizontal';
            // $styleWidth = 'col-12';
            $chart_render_method = 'renderChartHorizontalStackedBarDataSet';

            // $chartClass = $chart_type_name;
            $chart_data['data'] = \Drupal::getContainer()
              ->get('flexinfo.chart.service')
              ->{$chart_render_method}(array($pre_question_answer, $post_question_answer), array_reverse($data_legend));

            // RIGHT chart character
            $chart_data['rightChartData'] = NULL;
          }
          else {
            $chart_data['data'] = \Drupal::getContainer()
              ->get('flexinfo.chart.service')
              ->{$chart_render_method}($pool_data, $pool_label, NULL, $question_term, $color_palette, $data_legend);
          }

          $chart_data['block'] = array(
            'type'  => 'chart',
            'class' => $chartClass,
            'rightChartClass' => $rightChartClass,
            'styleWidth' => $styleWidth,
            'title' => \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
            'middle' => \Drupal::getContainer()->get('flexinfo.chart.service')->renderChartBottomFooterValue($pool_data, $question_term, TRUE, TRUE),
            'bottom' => array(
              array_sum($pool_data),
              t('RESPONSES'),
              \Drupal::service('ngdata.atomic.atom')->renderChartBottomFooterAnswerValue($question_term, $meeting_nodes),
              \Drupal::getContainer()->get('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_chartfooter')
            ),
          );

          $output['question'][] = $chart_data;

        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function defaultChartLegend($question_scale) {
    $default_legend = array();
    for ($i = 1; $i < $question_scale + 1; $i++) {
      $default_legend[$i] = $i;
    }

    return $default_legend;
  }

  /**
   *
   */
  public function renderPrePostDataSet($pool_data = array(), $chart_label = array(), $max_length = NULL, $question_term = NULL, $color_palette = array(), $data_legend = array(), $chart_Pre_Post_Label = array()) {
    $chart_data = array();
    if (is_array($pool_data)) {
      krsort($pool_data);
    }

    if (!$color_palette) {
      $color_palette_name = 'colorPlateFive';
      if (\Drupal::hasService('baseinfo.chart.service')) {
        if(method_exists(\Drupal::getContainer()->get('baseinfo.chart.service'), 'renderChartPieDataSetColorPlate')){
          $color_palette_name = \Drupal::getContainer()->get('baseinfo.chart.service')->renderChartPieDataSetColorPlate();
        }
      }

      $color_palette = \Drupal::getContainer()->get('flexinfo.setting.service')->{$color_palette_name}();
    }

    for ($i = 0; $i < count($pool_data); $i++) {
      foreach ($pool_data[$i] as $key => $value) {
        $chart_data[$i][$key] = array(
          "value" => $value,
          "color" => '#' . $color_palette[$key + 1],
          "title" => "1(12)",
          "legend" => isset($data_legend[$key + 1]) ? $data_legend[$key + 1] : NULL,
          "label" => $chart_Pre_Post_Label[$i],
        );

        if ($max_length) {
          if (($key + 2) > $max_length) {
            break;
          }
        }
      }
    }

    return $chart_data;
  }

  /**
   * Desperated
   */
  public function getEvaluationNids($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL) {
    $meeting_nids = \Drupal::getContainer()->get('flexinfo.node.service')->getNidsFromNodes($meeting_nodes);

    // query container
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('evaluation');

    $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_meetingnid', $meeting_nids, 'IN');
    $query->condition($group);


    // $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_reactset.question_tid', $question_tid);
    // $query->condition($group);

    // $group = $query_container->groupStandardByFieldValue($query, 'field_evaluation_reactset.question_answer', $question_answer);
    // $query->condition($group);

    $nids = $query_container->runQueryWithGroup($query);

    return $nids;
  }

  /**
   *
   */
  public function filterQuestionData($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL) {
    $evaluationNids = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperEvaluationNidsByQuestion($meeting_nodes);

    $evaluation_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($evaluationNids);

    $output = 0;
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid && $row['question_answer'] == $question_answer + 1) {
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
  public function filterPrePostQuestionData($meeting_nodes = array(), $question_tid = NULL, $question_answer = NULL, $question_pre_or_post = NULL) {
    $evaluationNids = \Drupal::getContainer()->get('flexinfo.querynode.service')->wrapperEvaluationNidsByQuestion($meeting_nodes);
    $evaluation_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($evaluationNids);

    $output = 0;
    if ($evaluation_nodes && is_array($evaluation_nodes)) {
      foreach ($evaluation_nodes as $evaluation_node) {
        $result = $evaluation_node->get('field_evaluation_reactset')->getValue();

        foreach ($result as $row) {
          if ($row['question_tid'] == $question_tid) {
            if ($row['question_answer'] == $question_answer + 1) {
              if ($row['refer_other'] == $question_pre_or_post) {
                $output++;
              }
            }
          }
        }
      }
    }
    return $output;
  }

  /**
   * Deprecated
   */
  public function getTextfieldQuestionAllData($meeting_nodes = array(), $question_tid = NULL) {
    $evaluationNids = $this->getEvaluationNids($meeting_nodes);
    $evaluation_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($evaluationNids);

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

  /**
   *
   */
  public function blockEventsComments($meeting_nodes = array(), $evaluationform_term = NULL, $question_tids = array()) {
    $output = array();

    $textfield_tid= \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidByTermName($term_name = 'textfield', $vocabulary_name = 'fieldtype');

    $textfield_question_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')
      ->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $textfield_tid);

    $sort_tids = array_intersect($question_tids, $textfield_question_tids);

    if (is_array($sort_tids)) {

      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($sort_tids);

      foreach ($question_terms as $question_term) {

        $question_answers = \Drupal::service('ngdata.term.question')
        ->getTextfieldQuestionAllData($meeting_nodes, $question_term->id());

        // $output['question'][] = $this->getQuestionDataByTextfield($question_term, $meeting_nodes);
        if ($question_answers) {
          $output['question'][] = $this->getQuestionDataByTextfieldNew($question_term, $question_answers);
        }
      }
    }

    return $output;
  }

  /**
   * Deprecated
   */
  public function getTextfieldQuestionComments($textfield_question_tids = array(), $meeting_nodes = array()) {
    $output = array();

    if (is_array($textfield_question_tids)) {
      $textfield_question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($textfield_question_tids);

      foreach ($textfield_question_terms as $textfield_question_term) {

        // $pool_data = \Drupal::getContainer()->get('flexinfo.querynode.service')
        //   ->wrapperPoolAnswerTextDataByQuestionTid($meeting_nodes, $textfield_question_term->id());
        $pool_data = $this->getTextfieldQuestionAllData($meeting_nodes, $textfield_question_term->id());

        $question_comments = NULL;
        if (isset($pool_data) && count($pool_data) > 0) {
          foreach ($pool_data as $key => $row) {
            $question_comments[] = $row;
          }

          $output[] = $question_comments;
        }
      }
    }

    return $output;
  }

  /**
   *  Deprecated
   * for Comments
   * new function getQuestionDataByTextfieldNew()
   */
  public function getQuestionDataByTextfield($textfield_question_term = NULL, $meeting_nodes = array()) {
    $output = array();

    $output['block'] = array(
      'type'  => 'comments',
      'class' => 'comments',
      'title' => $textfield_question_term->getName(),
    );

    // $pool_datas = \Drupal::getContainer()->get('flexinfo.querynode.service')
    //   ->wrapperPoolAnswerTextDataByQuestionTid($meeting_nodes, $textfield_question_term->id());

    $pool_data = $this->getTextfieldQuestionAllData($meeting_nodes, $textfield_question_term->id());

    if (isset($pool_data) && count($pool_data) > 0) {
      foreach ($pool_data as $key => $row) {
        $question_comments[] = $row;
      }

      $output['data'] = $question_comments;
    }

    return $output;
  }

  /**
   * New
   * for Comments
   */
  public function getQuestionDataByTextfieldNew($comment_header = NULL, $comment_content = array()) {
    $output = array();
    $question_comments = array();

    $output['block'] = array(
      'type'  => 'comments',
      'class' => 'comments',
      'title' => \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($comment_header),
    );

    foreach ($comment_content as $key => $eachCommentAnswer) {
      $rest_text =  NULL;
      $second_comment_text = NULL;
      $first_line_comment_text = Unicode::truncate($eachCommentAnswer, 128, TRUE, FALSE);
      $question_comments[] = '* ' . $first_line_comment_text;

      $second_comment_text = str_replace($first_line_comment_text, "", $eachCommentAnswer);
      if (strlen($second_comment_text) > 0) {
        $rest_text = $second_comment_text;

        if (strlen($second_comment_text) > 128) {
          $rest_text =  Unicode::truncate($second_comment_text, 128, TRUE, FALSE);
          $rest_text .=  '...';

        }
        $question_comments[] = $rest_text;
      }
    }
    $output['data'] = $question_comments;


    return $output;
  }

  /**
   *
   */
  public function blockEventsTableForRelatedFieldQuestion($meeting_nodes = array(), $evaluationform_term = NULL, $question_tids = array()) {
    $output = array();

    $question_tids = \Drupal::getContainer()
      ->get('baseinfo.queryterm.service')
      ->wrapperMultipleQuestionTidsFromEvaluationform($evaluationform_term);

    $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);
    if ($question_terms) {
      foreach ($question_terms as $question_term) {
        $result = $this->getQuestionDataByTableForRelatedFieldQuestion($meeting_nodes, $question_term);

        if ($result) {
          $output[] = $result;
        }
      }
    }

    return $output;
  }

  /**
   * for table
   */
  public function getQuestionDataByTableForRelatedFieldQuestion($meeting_nodes = array(), $question_term = NULL) {
    $output = array();

    $FlexpageEventLayout = new FlexpageEventLayout();
    $pool_data = $FlexpageEventLayout->getQuestionAnswerAllDataWithReferUid($meeting_nodes, $question_term->id());

    $tbody = array();
    if ($pool_data && count($pool_data) > 1) {
      foreach ($pool_data as $key => $row) {
        if ($key) {
          $user = \Drupal::entityTypeManager()
            ->getStorage('user')
            ->load($key);

          if ($user) {
            $count_values = array_count_values($row);

            $result = array(
              $user->getUserName(),
              count($row)
            );
            for ($i = 5; $i > 0; $i--) {
              $cell_value = isset($count_values[$i]) ? $count_values[$i] : 0;
              $cell_value .= ' (' . \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($cell_value, count($row), 0) . '%)';
              $result[] = $cell_value;
            }

            $tbody[] = $result;
          }
        }
      }

      $output['block'] = array(
        'type'  => 'table',
        'class' => 'table',
        'title' => \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
      );

      $output['data']["thead"] = [
        "Name",
        "Total",
      ];
      for ($i = 5; $i > 0; $i--) {
        $output['data']["thead"][] = $i;
      }

      $output['data']["tbody"] = $tbody;

    }
    // sample tbody
    // $output['data']["tbody"] = [
    //   [
    //     "Family Physician",
    //     9,
    //     "90%"
    //   ],
    //   [
    //     "Dietitian",
    //     1,
    //     "10%"
    //   ],
    // ];

    return $output;
  }

  /**
   *
   */
  public function blockEventsTableForSelectkeyQuestion($meeting_nodes = array(), $evaluationform_term = NULL, $question_tids = array()) {
    $output = array();

    // old

    $selectkey_tid= \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getTidByTermName($term_name = 'selectkey', $vocabulary_name = 'fieldtype');

    $question_tids = \Drupal::getContainer()
      ->get('flexinfo.queryterm.service')->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $selectkey_tid);

    // new need test
    // $filter_tids = \Drupal::getContainer()
    // ->get('baseinfo.queryterm.service')
    // ->wrapperFieldtypeQuestionTidsFromEvaluationform('selectkey', $evaluationform_term);

    // $question_tids = array_intersect($question_tids, $filter_tids);

    //  $outputss = \Drupal::service('ngdata.atomic.blockgroup')
    //   ->getBlockGroupChartBySelectKeyQuestion($meeting_nodes, $question_tids);


    if (is_array($question_tids)) {
      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {
        $output[] = $this->getQuestionDataByTableForSelectkeyQuestion($meeting_nodes, $question_term);
      }
    }

    return $output;
  }

  /**
   * for table
   */
  public function getQuestionDataByTableForSelectkeyQuestion($meeting_nodes = array(), $question_term = NULL) {
    $output = array();

    $output['block'] = array(
      'type'  => 'table',
      'class' => 'table',
      'title' => \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
    );

    $all_answer_tids = $this->getSelectkeyQuestionAllAnswerTids($meeting_nodes, $question_term);

    $count_answer_tids = array_count_values($all_answer_tids);

    $key_answer_tids = array_keys($count_answer_tids);
    $key_answer_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($key_answer_tids);

    $answer_tids_sum = count($all_answer_tids);

    $result = array();
    if (isset($key_answer_terms) && count($key_answer_terms) > 0) {
      foreach ($key_answer_terms as $term) {
        $result[] = array(
          $term->getName(),
          $count_answer_tids[$term->id()],
          \Drupal::getContainer()->get('flexinfo.calc.service')->getPercentageDecimal($count_answer_tids[$term->id()], $answer_tids_sum, 0) . '%',
        );
      }
    }

    $output['data']["thead"] = [
      "Name",
      "Number",
      "Percentage",
    ];
    $output['data']["tbody"] = $result;

    // sample tbody
    // $output['data']["tbody"] = [
    //   [
    //     "Family Physician",
    //     9,
    //     "90%"
    //   ],
    //   [
    //     "Dietitian",
    //     1,
    //     "10%"
    //   ],
    // ];

    return $output;
  }

  /**
   *
   */
  public function getSelectkeyQuestionAllAnswerTids($meeting_nodes = array(), $question_term = NULL) {
    $answer_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldAllTargetIds($question_term, 'field_queslibr_selectkeyanswer');


    $evaluationNids = $this->getEvaluationNids($meeting_nodes);
    $evaluation_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($evaluationNids);

    $answer_tids = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getReactsetFieldAllValueCollection($evaluation_nodes, $field_name = 'field_evaluation_reactset', $subfield = 'question_answer', $question_term->id());

    return $answer_tids;
  }

 }
