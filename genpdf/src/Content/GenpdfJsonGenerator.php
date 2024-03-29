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
use Drupal\ngjson\Content\NgjsonObjectContent;

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
  public function programJson($entity_id = NULL) {
    $output = [];

    $FlexpageBaseJson = new FlexpageBaseJson();
    $DashpageObjectContent = new DashpageObjectContent();

    $meeting_nodes = $this->queryProgramNodes($entity_id);

    $program_entity = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($entity_id);

    $evaluationform_tid = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetId($program_entity, 'field_program_evaluationform');

    $evaluationform_term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($evaluationform_tid);

    $output['pdfjson'] = $this->eventsData($meeting_nodes, $evaluationform_term, 'program', $entity_id);

    $output['fixedSection'] = $FlexpageBaseJson->generateTileStyleOne(
      $DashpageObjectContent->pageTopFixedSectionData($meeting_nodes)
    );

    $output['programTitle'] = $program_entity->getName();

    return $output;
  }

  /**
   *
   */
  public function meetingJson($entity_id = NULL) {
    $node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load($entity_id);

    $evaluationform_term = \Drupal::service('flexinfo.node.service')
      ->getMeetingEvaluationformTerm($node);

    $output['pdfjson'] = $this->eventsData(array($node), $evaluationform_term);

    return $output;
  }

  /**
   * @internal only for debug use
   */
  public function getDebugMeetingJsonFromFileUrl() {
    $file_url = '/modules/custom/flexservice/genpdf/json/jsonv6.json';

    $output = \Drupal::service('flexinfo.json.service')
      ->fetchConvertJsonToArray($file_url);

    return $output;
  }

  /**
   *
   */
  public function getPdfName($entity_id = NULL) {
    $pdf_folder_path = 'sites/default/files/pdf/';
    $output = $pdf_folder_path . date("Y_m_d_H_i_s") . ".pdf";

    return $output;
  }

  /**
   *
   */
  public function queryProgramNodes($entity_id = NULL) {
    $NgjsonObjectContent = new NgjsonObjectContent();

    $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::service('flexinfo.setting.service')->userEndTime();

    $meeting_nodes = $NgjsonObjectContent->querySnapshotMeetingsNodes('program', $entity_id, $start, $end);
    // indexes the array numerically.
    $meeting_nodes = array_values($meeting_nodes);

    return $meeting_nodes;
  }

  /**
   * @param $type is 'meeting' or 'program'
   */
  public function eventsData($meeting_nodes = array(), $evaluationform_term = NULL, $type = 'meeting', $entity_id = NULL) {
    $output = array();

    if (empty($evaluationform_term)) {
      return $output;
    }
    if ($evaluationform_term->bundle() != 'evaluationform') {
      return $output;
    }

    $question_tids = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIds($evaluationform_term, 'field_evaluationform_questionset');

    $output['meeting'] = [];
    if (isset($meeting_nodes[0])) {
      $output['meeting'] = $this->blockEventInfo($meeting_nodes[0]);
    }

    $output['chartSection']   = $this->blockEventsChart($meeting_nodes, $evaluationform_term, $question_tids);

    $output['commentSection'] = $this->blockEventsComments($meeting_nodes, $evaluationform_term, $question_tids);

    $table_data = [];
    if ($type == 'program') {
      $table_data = $this->blockEventsTableForMeetingFieldQuestion($meeting_nodes, $evaluationform_term, $entity_id);
    }
    $output['tableSection']['question'] = array_merge(
      $table_data,
      $this->blockEventsTableForRelatedFieldQuestion($meeting_nodes, $evaluationform_term),
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
      'programName' => \Drupal::service('flexinfo.field.service')
        ->getFieldFirstTargetIdTermName($meeting_node, 'field_meeting_program'),
      'tileSection' => $DashpageObjectContent->blockTileMeetingValue($meeting_node),
    );

    return $output;
  }

  /**
   * @param $chart_render_method like "renderChartPieDataSet".
   * $chart_render_method = \Drupal::service('flexinfo.chart.service')
   *   ->getChartTypeRenderFunctionByQuestion($question_term);
   */
  public function getBlockEventsChartData($pool_data = array(), $chart_label = array(), $max_length = NULL, $question_term = NULL, $color_plate = array(), $data_legend = array(), $chart_render_method = "renderChartNewPieDataSet") {
    $output = array();

    // reverse order
    if ($pool_data) {
      $pool_data = array_reverse($pool_data);
    }

    $output = \Drupal::service('flexinfo.chart.service')
      ->{$chart_render_method}($pool_data, $chart_label, $max_length, $question_term, $color_plate, $data_legend);

    return $output;
  }

  /**
   *
   */
  public function blockEventsChart($meeting_nodes = array(), $evaluationform_term = NULL, $question_tids = array()) {
    $output = array();

    $filter_tids = \Drupal::service('baseinfo.queryterm.service')
      ->wrapperQuestionTidsOnlyRadios($question_tids, FALSE);

    $question_tids = array_intersect($question_tids, $filter_tids);

    // Override tids by specify order
    if (\Drupal::hasService('baseinfo.queryterm.service')) {
      if (method_exists(\Drupal::service('baseinfo.queryterm.service'), 'getRadioTidsBySpecifyOrder')) {
        $question_tids = \Drupal::service('baseinfo.queryterm.service')->getRadioTidsBySpecifyOrder($question_tids);
      }
    }

    if (is_array($question_tids) && $question_tids) {
      $question_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadMultiple($question_tids);
      foreach ($question_terms as $question_term) {
        if (isset($question_term)) {

          // radios is 2493
          $question_tid  = $question_term->id();
          $question_scale = \Drupal::service('flexinfo.field.service')
            ->getFieldFirstValue($question_term, 'field_queslibr_scale');

          $chart_type_name = \Drupal::service('flexinfo.field.service')
            ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_charttype');

          // $pool_label
          $pool_label = [];
          $pre_question_answer = [];
          $post_question_answer = [];


          for ($i = 0; $i < $question_scale; $i++) {
            if ($chart_type_name == 'Stacked Bar Chart Multiple Horizontal') {
              $pre_question_answer[$i] = $this->filterPrePostQuestionData($meeting_nodes, $question_tid, ($i), 'Pre');
              $post_question_answer[$i] = $this->filterPrePostQuestionData($meeting_nodes, $question_tid, ($i), 'Post');
            }
            $pool_label[$i + 1] = $i + 1;
          }

          // $pool_data
          $pool_data = \Drupal::service('ngdata.node.evaluation')
            ->getRaidoQuestionData($question_term, $meeting_nodes);

          // $color_palette
          $color_palette = \Drupal::service('ngdata.term.question')
            ->getRaidoQuestionColors($question_term, TRUE);
          if ($color_palette) {
            $color_palette = array_reverse(\Drupal::service('ngdata.term.question')
              ->getRaidoQuestionColors($question_term, TRUE));
          }
          $color_palette = \Drupal::service('flexinfo.setting.service')
            ->colorPlateOutputKeyPlusOne($color_palette);

          // $data_legend
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
          }

          // chartClass
          $chartClass = 'Pie Chart';
          if ($chart_type_name) {
            $chartClass = $chart_type_name;
          }

          // $chart_data
          $chart_data = [];
          $chart_data['data'] = $this->getBlockEventsChartData($pool_data, $pool_label, NULL, $question_term, $color_palette, $data_legend);

          if ($chart_type_name == 'Stacked Bar Chart Multiple Horizontal') {
            // RIGHT chart character
            // $styleWidth = 'col-md-12';
            $chart_render_method = 'renderChartHorizontalStackedBarDataSet';

            // do not reverse order
            $chart_data['data'] = \Drupal::service('flexinfo.chart.service')
              ->{$chart_render_method}(array($pre_question_answer, $post_question_answer), array_reverse($data_legend));

            // RIGHT chart character
            $chart_data['rightChartData'] = NULL;
          }
          elseif ($chart_type_name == 'PrePost Pie Chart Column12') {
            $chart_data['data'] = [];
            $pool_data = \Drupal::service('ngdata.node.evaluation')
              ->getRaidoQuestionDataWithReferOther($question_term, $meeting_nodes, 'Pre');
            $chart_data['data']['Pre'] = $this->getBlockEventsChartData($pool_data, $pool_label, NULL, $question_term, $color_palette, $data_legend);
            $pool_data = \Drupal::service('ngdata.node.evaluation')
              ->getRaidoQuestionDataWithReferOther($question_term, $meeting_nodes, 'Post');
            $chart_data['data']['Post'] = $this->getBlockEventsChartData($pool_data, $pool_label, NULL, $question_term, $color_palette, $data_legend);
          }

          //
          $styleWidth = NULL;
          if ($chart_type_name == 'PrePost Pie Chart Column12') {
            // Two chart in one section Column12
            $styleWidth = 'col-md-12';
          }

          //
          $chart_data['block'] = array(
            'type'  => 'chart',
            'class' => $chartClass,
            'rightChartClass' => NULL,
            'styleWidth' => $styleWidth,
            'title' => \Drupal::service('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
            'middle' => \Drupal::service('flexinfo.chart.service')->renderChartBottomFooterValue($pool_data, $question_term, TRUE, TRUE),
            'bottom' => array(
              array_sum($pool_data),
              t('RESPONSES'),
              \Drupal::service('ngdata.atomic.atom')->renderChartBottomFooterAnswerValue($question_term, $meeting_nodes),
              \Drupal::service('flexinfo.field.service')->getFieldFirstValue($question_term, 'field_queslibr_chartfooter')
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
        if (method_exists(\Drupal::service('baseinfo.chart.service'), 'renderChartPieDataSetColorPlate')) {
          $color_palette_name = \Drupal::service('baseinfo.chart.service')->renderChartPieDataSetColorPlate();
        }
      }

      $color_palette = \Drupal::service('flexinfo.setting.service')->{$color_palette_name}();
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
    $meeting_nids = \Drupal::service('flexinfo.node.service')->getNidsFromNodes($meeting_nodes);

    // query container
    $query_container = \Drupal::service('flexinfo.querynode.service');
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
    $evaluationNids = \Drupal::service('flexinfo.querynode.service')->wrapperEvaluationNidsByQuestion($meeting_nodes);

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
    $evaluationNids = \Drupal::service('flexinfo.querynode.service')->wrapperEvaluationNidsByQuestion($meeting_nodes);
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
   *
   */
  public function blockEventsComments($meeting_nodes = array(), $evaluationform_term = NULL, $question_tids = array()) {
    $output = array();

    $textfield_tid= \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = 'textfield', $vocabulary_name = 'fieldtype');

    $textfield_question_tids = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $textfield_tid);

    $sort_tids = array_intersect($question_tids, $textfield_question_tids);

    if (is_array($sort_tids)) {

      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($sort_tids);

      foreach ($question_terms as $question_term) {

        $question_answers = \Drupal::service('ngdata.term.question')
        ->getTextfieldQuestionAllData($meeting_nodes, $question_term->id());

        if ($question_answers) {
          $output['question'][] = $this->getQuestionDataByTextfield($question_term, $question_answers);
        }
      }
    }

    return $output;
  }

  /**
   * for Comments
   */
  public function getQuestionDataByTextfield($comment_header = NULL, $comment_content = array()) {
    $output = [];
    $question_comments = [];

    $output['block'] = array(
      'type'  => 'comments',
      'class' => 'comments',
      'title' => \Drupal::service('flexinfo.chart.service')->getChartTitleByQuestion($comment_header),
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
   * Breakdown table for specify quesitons on the program term.
   */
  public function blockEventsTableForMeetingFieldQuestion($meeting_nodes = array(), $evaluationform_term = NULL, $entity_id = NULL) {
    $output = [];

    $field_exist = \Drupal::service('flexinfo.field.service')
      ->checkBundleHasField('node', 'meeting', 'field_meeting_module');
    if ($field_exist) {
      $breakdown_values = \Drupal::service('flexinfo.field.service')
        ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_module');

      if ($breakdown_values && count($breakdown_values) > 1) {
        $program_term = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->load($entity_id);

        $question_terms = \Drupal::service('flexinfo.field.service')
          ->getFieldAllTargetIdsEntitys($program_term, 'field_program_breakdown_question');

        if ($question_terms) {
          foreach ($question_terms as $question_term) {
            $result = $this->getQuestionDataByTableForMeetingFieldQuestion($meeting_nodes, $question_term);

            if ($result) {
              $output[] = $result;
            }
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function blockEventsTableForRelatedFieldQuestion($meeting_nodes = array(), $evaluationform_term = NULL) {
    $output = [];

    // ForMeetingSpeaker
    $question_tids_meeting_speaker = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperMultipleQuestionTidsFromEvaluationformForMeetingSpeaker($evaluationform_term);
    $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids_meeting_speaker);
    if ($question_terms) {
      foreach ($question_terms as $question_term) {
        $result = $this->getQuestionDataByTableForRelatedFieldQuestionForMeetingSpeaker($meeting_nodes, $question_term);

        if ($result) {
          $output[] = $result;
        }
      }
    }

    // For Relatedtype
    $question_tids_relatedtype = \Drupal::service('flexinfo.queryterm.service')
      ->wrapperMultipleQuestionTidsFromEvaluationformForRelatedtype($evaluationform_term);
    $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids_relatedtype);
    if ($question_terms) {
      foreach ($question_terms as $question_term) {
        $result = $this->getQuestionDataByTableForRelatedFieldQuestionForRelatedtype($meeting_nodes, $question_term);

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
  public function getQuestionDataByTableForRelatedFieldQuestionForMeetingSpeaker($meeting_nodes = array(), $question_term = NULL) {
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
              $user->getAccountName(),
              count($row)
            );
            for ($i = 5; $i > 0; $i--) {
              $cell_value = isset($count_values[$i]) ? $count_values[$i] : 0;
              $cell_value .= ' (' . \Drupal::service('flexinfo.calc.service')->getPercentageDecimal($cell_value, count($row), 0) . '%)';
              $result[] = $cell_value;
            }

            $tbody[] = $result;
          }
        }
      }

      $output['block'] = array(
        'type'  => 'table',
        'class' => 'table',
        'title' => \Drupal::service('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
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
   * for table
   */
  public function getQuestionDataByTableForMeetingFieldQuestion($meeting_nodes = array(), $question_term = NULL) {
    $output = array();

    $FlexpageEventLayout = new FlexpageEventLayout();
    $pool_data = $FlexpageEventLayout->getQuestionAnswerAllDataWithProgramBreakDownField($meeting_nodes, $question_term->id());
    $tbody = array();
    if ($pool_data && count($pool_data) > 1) {
      foreach ($pool_data as $key => $row) {
        if ($key) {
          $count_values = array_count_values($row);

          $result = array(
            $key,
            count($row)
          );
          for ($i = 5; $i > 0; $i--) {
            $cell_value = isset($count_values[$i]) ? $count_values[$i] : 0;
            $cell_value .= ' (' . \Drupal::service('flexinfo.calc.service')->getPercentageDecimal($cell_value, count($row), 0) . '%)';
            $result[] = $cell_value;
          }

          $tbody[] = $result;
        }
      }

      $output['block'] = array(
        'type'  => 'table',
        'class' => 'table',
        'title' => \Drupal::service('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
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

    return $output;
  }

  /**
   * for table
   */
  public function getQuestionDataByTableForRelatedFieldQuestionForRelatedtype($meeting_nodes = array(), $question_term = NULL) {
    $output = array();

    $FlexpageEventLayout = new FlexpageEventLayout();
    $pool_data = $FlexpageEventLayout->getQuestionAnswerAllDataWithReferOther($meeting_nodes, $question_term->id());
    $tbody = array();
    if ($pool_data && count($pool_data) > 1) {
      foreach ($pool_data as $key => $row) {
        if ($key) {
          $count_values = array_count_values($row);

          $result = array(
            $key,
            count($row)
          );
          for ($i = 5; $i > 0; $i--) {
            $cell_value = isset($count_values[$i]) ? $count_values[$i] : 0;
            $cell_value .= ' (' . \Drupal::service('flexinfo.calc.service')->getPercentageDecimal($cell_value, count($row), 0) . '%)';
            $result[] = $cell_value;
          }

          $tbody[] = $result;
        }
      }

      $output['block'] = array(
        'type'  => 'table',
        'class' => 'table',
        'title' => \Drupal::service('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
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

    $selectkey_tid= \Drupal::service('flexinfo.term.service')
      ->getTidByTermName($term_name = 'selectkey', $vocabulary_name = 'fieldtype');

    $question_tids = \Drupal::service('flexinfo.queryterm.service')->wrapperStandardTidsByTidsByField($question_tids, 'questionlibrary', 'field_queslibr_fieldtype', $selectkey_tid);

    // new need test
    // $filter_tids = \Drupal::service('baseinfo.queryterm.service')
    // ->wrapperFieldtypeQuestionTidsFromEvaluationform('selectkey', $evaluationform_term);

    // $question_tids = array_intersect($question_tids, $filter_tids);

    //  $outputss = \Drupal::service('ngdata.atomic.blockgroup')
    //   ->getBlockGroupChartBySelectKeyQuestion($meeting_nodes, $question_tids);


    if (is_array($question_tids)) {
      $question_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($question_tids);

      foreach ($question_terms as $question_term) {
        $block_row = $this->getQuestionDataByTableForSelectkeyQuestion($meeting_nodes, $question_term);
        // Only append not empty block.
        if ($block_row) {
          $output[] = $block_row;
        }
      }
    }

    return $output;
  }

  /**
   * for table
   * @return array
   $output['data']["tbody"] = [
     [
       "Family Physician",
       9,
       "90%"
     ],
     [
       "Dietitian",
       1,
       "10%"
     ],
   ];
   */
  public function getQuestionDataByTableForSelectkeyQuestion($meeting_nodes = array(), $question_term = NULL) {
    $output = [];

    $all_answer_tids = $this->getSelectkeyQuestionAllAnswerTids($meeting_nodes, $question_term);

    $count_answer_tids = array_count_values($all_answer_tids);

    $key_answer_tids = array_keys($count_answer_tids);
    $key_answer_terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($key_answer_tids);

    $answer_tids_sum = count($all_answer_tids);

    $result = [];
    if (isset($key_answer_terms) && count($key_answer_terms) > 0) {
      foreach ($key_answer_terms as $term) {
        $result[] = [
          $term->getName(),
          $count_answer_tids[$term->id()],
          \Drupal::service('flexinfo.calc.service')->getPercentageDecimal($count_answer_tids[$term->id()], $answer_tids_sum, 0) . '%',
        ];
      }
    }
    if ($result) {
      $output['block'] = [
        'type'  => 'table',
        'class' => 'table',
        'title' => \Drupal::service('flexinfo.chart.service')->getChartTitleByQuestion($question_term),
      ];

      $output['data']["thead"] = [
        "Name",
        "Number",
        "Percentage",
      ];
      $output['data']["tbody"] = $result;
    }

    return $output;
  }

  /**
   *
   */
  public function getSelectkeyQuestionAllAnswerTids($meeting_nodes = array(), $question_term = NULL) {
    $answer_tids = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIds($question_term, 'field_queslibr_selectkeyanswer');


    $evaluationNids = $this->getEvaluationNids($meeting_nodes);
    $evaluation_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($evaluationNids);

    $answer_tids = \Drupal::service('flexinfo.field.service')
      ->getReactsetFieldAllValueCollection($evaluation_nodes, $field_name = 'field_evaluation_reactset', $subfield = 'question_answer', $question_term->id());

    return $answer_tids;
  }

 }
