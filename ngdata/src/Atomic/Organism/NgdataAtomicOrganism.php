<?php

namespace Drupal\ngdata\Atomic\Organism;

use Drupal\ngdata\Atomic\NgdataAtomic;

use Drupal\flexpage\Content\FlexpageEventLayout;

use Drupal\ngjson\Content\EventStandardLayoutContent;


/**
 * Class NgdataAtomicOrganism.

  \Drupal::service('ngdata.atomic.organism')->demo();
 */
class NgdataAtomicOrganism extends NgdataAtomic {

  private $atom;
  private $molecule;

  /**
   * Constructs a new NgdataAtomicOrganism object.
   */
  public function __construct() {
    $this->atom     = \Drupal::service('ngdata.atomic.atom');
    $this->molecule = \Drupal::service('ngdata.atomic.molecule');
  }

  /**
   * @todo Without Save Png
   */
  public function basicSectionPure($type = "htmlSnippt") {
    $blockId = uniqid(rand());

    $output = array(
      "tabShow" => "hide",
      "blockHeader" => "",
      "blockClass" => "col-md-12 margin-top-24",
      "blockClassSub" => NULL,
      'blockId' => $blockId,
      "blockContent" => array($this->basicTab($type)),
    );

    return $output;
  }

  /**
   *
   */
  public function basicSection($type = "htmlSnippt", $save_png_icon_style = "float-right margin-top-12 margin-right-16", $save_png_icon_enable = TRUE) {
    $blockId = uniqid(rand());

    $output = array(
      "tabShow" => "hide",
      "blockHeader" => "",
      "blockClass" => "col-md-12 margin-top-24",
      "blockClassSub" => NULL,
      'blockId' => $blockId,
      'blockIcon' => $this->molecule->savePngIcon($save_png_icon_style, $blockId, $save_png_icon_enable),
      "blockContent" => array($this->basicTab($type)),
    );
    return $output;
  }

  /**
   *
   */
  public function basicTab($type) {
    $output = array(
      "tabTitle" => "",
      "tabData" => array(
        "class" => "",
        "type" => $type,
        "top" => array(
          "value" => ""
        ),
        "bottom" => array(
          "value" => ""
        ),
      ),
    );

    return $output;
  }

  /**
   * middleMiddle Chart
   */
  public function basicMiddleChart($chart_type = "pie", $middle_class = "col-md-6", $right_class = "col-md-6") {
    $output = array(
      "class" => "min-height-320 padding-top-20 margin-bottom-20",
      "middleLeft" => [
        "value" => ""
      ],
      "middleMiddle" => [
        "value" => "",
        "type" => $chart_type,
        "styleClass" => $middle_class,
        "data" => [
          "labels" => [],
          "datasets" => [
            "label" => "",
            "data" => [],
            "backgroundColor" => []
          ]
        ],
        "options" => [
          "legend" => [
            "display" => FALSE
          ],
          "plugins" => [
            "labels" => [
              // "render" => "value",
              "fontColor" => "#fff",
              "fontSize" => 13,
              "position" => "border"
            ]
          ],
        ]
      ],
      "middleRight" => [
        "styleClass" => $right_class,
        "value" => ""
      ]
    );

    return $output;
  }

  /**
   * middleRight Chart
   */
  public function basicMiddleRightChart($chart_type = "pie", $middle_class = "col-md-6") {
    $output = array(
      'showChart' => true,
      'type' => 'bar',
      'styleClass' => 'col-md-6 margin-top-24 margin-bottom-20',
      'data' => array(
        'datasets' => array(
          0 => array(
            'backgroundColor' => array(
              0 => '#c6c6c6',
              1 => '#0093d0',
              2 => '#00aeef',
            ),
            'borderColor' => array(
              0 => '#c6c6c6',
              1 => '#0093d0',
              2 => '#00aeef',
            ),
            'borderWidth' => 1,
            'data' => array(
              0 => 123,
              1 => 256,
              2 => 325,
            ),
          ),
        ),
        'labels' => array(
          0 => 'pre',
          1 => 'post',
          2 => 'other',
        ),
      ),
      'options' => array(
        'layout' => array(
          'padding' => array(
            'top' => 30,
          ),
        ),
        'legend' => array(
          'display' => false,
        ),
        'maintainAspectRatio' => false,
        'plugins' => array(
          'labels' => array(
            'fontColor' => '#000000',
            'fontSize' => 14,
            'render' => 'value',
          ),
        ),
        'responsive' => false,
        'scales' => array(
          'xAxes' => array(
            0 => array(
              'gridLines' => array(
                'color' => '#f1f1f1',
              ),
              'stacked' => true,
              'ticks' => array(
                'fontSize' => 14,
              ),
            ),
          ),
          'yAxes' => array(
            0 => array(
              'gridLines' => array(
                'color' => '#f1f1f1',
              ),
              'stacked' => true,
              'ticks' => array(
                'fontSize' => 14,
                // 'max' => 400,
              ),
            ),
          ),
        ),
      ),
    );

    return $output;
  }

  public function basicMiddleRightChart1($chart_type = "pie", $middle_class = "col-md-6") {
    $output = array(
      "middleRight" => [
        "showChart" => TRUE,
        "type" => $chart_type,
        "styleClass" => $middle_class,
        "value" => "",
        "data" => [
          "labels" => ['pre', 'post'],
          "datasets" => [
            "data" => [
              123,
              250
            ],
            "backgroundColor" => [
              "#00134b",
              "#0093d0",
              "#00aeef",
              "#c6c6c6",
              "#7dba00",
              "#20ba00",
              "#",
              "#"
            ],
            "borderColor" => [
              "#00134b",
              "#0093d0",
              "#00aeef",
              "#c6c6c6",
              "#7dba00",
              "#20ba00",
              "#",
              "#"
            ],
            "borderWidth" => 1,
          ]
        ],
        "options" => [
          "legend" => [
            "display" => FALSE
          ],
          "plugins" => [
            "labels" => [
              // "render" => "value",
              "fontColor" => "#fff",
              "fontSize" => 13,
              "position" => "border"
            ]
          ],
        ]
      ],
    );

    return $output;
  }

  /**
   *
   */
  public function emptyRowSection() {
    $output = $this->basicSection();
    $output['blockClass'] = "col-xs-12";
    $output['blockHeader'] = '<div style="clear:both;"></div>';

    return $output;
  }

  /**
   *
   */
  public function tileSection($num, $tile_name, $css_class) {
    $output = $this->basicSection();
    $output['blockClass'] = "col-xs-12 col-sm-6 col-md-3 margin-top-12 height-90 color-fff";
    $output['blockHeader'] = $this->molecule->tileBlockHeader($num, $tile_name, $css_class, TRUE);

    return $output;
  }

  /**
   *
   */
  public function blockHeaderHtmlQuestionTitle($question_term = NULL) {
    $output = '<div class="block-comment-header-wrapper clear-both">';
      $output .= '<div class="panel-header block-header bg-0f69af color-fff line-height-px-42 padding-left-18 padding-right-24 margin-top-12">';
        $output .= '<span>';
          $output .= \Drupal::getContainer()->get('flexinfo.chart.service')->getChartTitleByQuestion($question_term);
        $output .= '</span>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getRaidoQuestionLegendHorizontal($question_term = NULL, $meeting_nodes = array()) {
    $output = $this->atom->renderLegendSquareHorizontal(
      $this->molecule->getRaidoQuestionLegendText($question_term, $meeting_nodes),
      \Drupal::service('ngdata.term.question')->getRaidoQuestionColors($question_term)
    );

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
      $table .= '<div class="panel-body padding-bottom-2 bg-ffffff font-size-12 margin-left-12">';
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
      $table .= '</div">';
    }

    return $table;
  }

  /**
   *
   */
  public function getHtmlTableByMultipleQuestionByReferUid($question_term = NULL, $meeting_nodes = array()) {
    $FlexpageEventLayout = new FlexpageEventLayout();
    $pool_data = $FlexpageEventLayout->getQuestionAnswerAllDataWithReferUid($meeting_nodes, $question_term->id());

    $table = NULL;
    if ($pool_data && count($pool_data) > 1) {
      $table .= '<div class="panel-body padding-bottom-2 bg-ffffff font-size-12 margin-left-12">';
        $table .= '<table class="table table-hover margin-bottom-0">';
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
      $table .= '</div">';
    }

    return $table;
  }

  /**
   *
   */
  public function tileSectionGroup($meeting_nodes = array(), $eight_tile = TRUE) {
    $output = array();

    $signature_total = array_sum(
      \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
    );
    $evaluation_nums = array_sum(
      \Drupal::getContainer()->get('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_evaluationnum')
    );

    $tile_array[] = array(
      'name'  => 'Total Events',
      'value' => count($meeting_nodes),
    );
    $tile_array[] = array(
      'name'  => 'HCP Reached',
      'value' => $signature_total,
    );
    $tile_array[] = array(
      'name'  => 'Evaluations Received',
      'value' => $evaluation_nums,
    );
    $tile_array[] = array(
      'name'  => 'HCP Response',
      'value' => \Drupal::getContainer()->get('flexinfo.calc.service')
        ->getPercentageDecimal($evaluation_nums, $signature_total, 0) . '%',
    );

    if ($eight_tile) {
      // $tile_array[] = array(
      //   'name'  => 'Overall program QUALITY',
      //   'value' => \Drupal::service('ngdata.term.question')
      //     ->getRaidoQuestionTidStatsAverage(120, $meeting_nodes),
      // );
      // $tile_array[] = array(
      //   'name'  => 'Overall General Satisfaction',
      //   'value' => \Drupal::service('ngdata.term.question')
      //     ->getRaidoQuestionTidsStatsAverage(array(130, 131, 132, 133), $meeting_nodes),
      // );
      // $tile_array[] = array(
      //   'name'  => ' Speaker Rating',
      //   'value' => \Drupal::service('ngdata.term.question')
      //     ->getRaidoQuestionTidStatsAverage(134, $meeting_nodes),
      // );
      // $tile_array[] = array(
      //   'name'  => 'Hospitality - Venue, Accommodation, Restoration',
      //   'value' => \Drupal::service('ngdata.term.question')
      //     ->getRaidoQuestionTidsStatsAverage(array(135, 136, 137), $meeting_nodes),
      // );
    }

    foreach ($tile_array as $key => $row) {
      $output[] = \Drupal::service('ngdata.atomic.organism')
        ->tileSection(
          $row['value'],
          $row['name'],
          ' bg-' . \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateForTile($key + 1, FALSE)
        );
    }

    return $output;
  }

  /**
   * @return string
   */
  public function getLegendTotalEventsByBU($meeting_nodes = array()) {
    $chartData = array_values(\Drupal::service('ngdata.node.meeting')
      ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
        ->meetingNodesByBU($meeting_nodes)
      )
    );

    $chartLabel = \Drupal::service('ngdata.term')
      ->getTermListByVocabulary('businessunit')['label'];

    $legend_text = array();
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . $chartData[$key] . ')';
      }
    }

    $legend_color = \Drupal::service('flexinfo.setting.service')
      ->colorPlateOutputKeyByPaletteName('colorPlatePieChartOne', $color_key = NULL, $pound_sign = FALSE, 'f6f6f6');

    $output = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderLegendSquare($legend_text, $legend_color, $max_length = NULL, 'font-size-14');

    return $output;
  }

  /**
   * @return string
   * only show filtered BU legend
   *
   * filter below string
   <div style="margin-top:110px;" class="legend-square-wrapper margin-left-12 width-pt-100 font-size-14">
     <div class="clear-both height-32 text-center fn-render-legend-square">
       <span class="legend-square bg-0093d0">
       </span>
       <span class="float-left legend-text">Immunology(0)</span>
     </div>
     <div class="clear-both height-32 text-center fn-render-legend-square">
       <span class="legend-square bg-002596">
       </span>
       <span class="float-left legend-text">Oncology(0)</span>
     </div>
     <div class="clear-both height-32 text-center fn-render-legend-square">
       <span class="legend-square bg-ff9933">
       </span>
       <span class="float-left legend-text">Specialty(1)</span>
     </div>
   </div>
   *
   *
   *
   <div style="margin-top:110px;" class="legend-square-wrapper margin-left-12 width-pt-100 font-size-14"><div class="clear-both height-32 text-center fn-render-legend-square"><span class="legend-square bg-0093d0"></span><span class="float-left legend-text">Immunology(0)</span></div><div class="clear-both height-32 text-center fn-render-legend-square"><span class="legend-square bg-002596"></span><span class="float-left legend-text">Oncology(0)</span></div><div class="clear-both height-32 text-center fn-render-legend-square"><span class="legend-square bg-ff9933"></span><span class="float-left legend-text">Specialty(1)</span></div></div>
   */
   public function getLegendTotalEventsByBUWithLegendRelevant($meeting_nodes = array()) {
     $output = $this->getLegendTotalEventsByBU($meeting_nodes);

     $user_default_businessunit_tids = \Drupal::service('user.data')
       ->get('navinfo', \Drupal::currentUser()->id(), 'default_term_businessunit');
     if ($user_default_businessunit_tids) {

       $trees = \Drupal::entityTypeManager()
         ->getStorage('taxonomy_term')
         ->loadTree('businessunit', 0);

       $temp_match = [];
       foreach ($trees as $key => $term) {
         $preg_string = '/<div class="clear-both.+' . $term->name . '\(\w*\)<\/span><\/div>/';

         if (in_array($term->tid, $user_default_businessunit_tids)) {
           preg_match($preg_string, $output, $matches);
           $temp_match["temp_match_" . $key] = $matches[0];
           $output = preg_replace($preg_string, "temp_match_" . $key, $output);
         }
         else {
           $output = preg_replace($preg_string, "", $output);
         }
       }

       if ($temp_match) {
         foreach ($temp_match as $temp_match_key => $row) {
           $output = str_replace($temp_match_key, $row, $output);
         }
       }
     }

     return $output;
   }

  /**
   *
   */
  public function getLegendHorizontalTotalEventsByBU($meeting_nodes = array()) {
    $chartData = array_values(\Drupal::service('ngdata.node.meeting')
      ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
        ->meetingNodesByBU($meeting_nodes)
      )
    );
    $chartLabel = \Drupal::service('ngdata.term')
      ->getTermListByVocabulary('businessunit')['label'];

    $legend_text = array();
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . $chartData[$key] . ')';
      }
    }

    $legend_color = \Drupal::service('flexinfo.setting.service')
      ->colorPlateOutputKeyByPaletteName('colorPlatePieChartOne', $color_key = NULL, $pound_sign = FALSE, 'f6f6f6');

    $output = \Drupal::service('ngdata.atomic.atom')
      ->renderLegendSquareHorizontal($legend_text, $legend_color, $max_length = NULL, 'font-size-12');

    return $output;
  }

  /**
   *
   */
  public function getLegendTotalEventsByTherapeuticArea($meeting_nodes = array(), $businessunit_tid = NULL) {
    $legend_text = [];

    $chartData = \Drupal::service('ngdata.node.meeting')
      ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
        ->meetingNodesByTherapeuticArea($meeting_nodes, $businessunit_tid));
    $chartLabel = \Drupal::service('ngdata.term')
      ->getTermTherapeuticAreaListByBu($businessunit_tid)['label'];
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . $chartData[$key] . ')';
      }
    }

    $legend_color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlatePieChartOne(NULL, FALSE);
    $legends = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color, $max_length = NULL, 'font-size-14');

    return $legends;
  }

  /**
   *
   */
  public function getLegendTotalEventsDiseaseState($meeting_nodes = array(), $diseasestate_tid = NULL) {
    $legend_text = [];

    $chartData = \Drupal::service('ngdata.node.meeting')
      ->countMeetingNodesArray(\Drupal::service('ngdata.node.meeting')
        ->meetingNodesByDiseaseState($meeting_nodes, $diseasestate_tid));

    $chartLabel = \Drupal::service('ngdata.term')
      ->getTermListByVocabulary('DiseaseState')['label'];

    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . $chartData[$key] . ')';
      }
    }

    $legend_color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlatePieChartOne(NULL, FALSE);
    $legends = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color, $max_length = NULL, 'font-size-14');

    return $legends;
  }

  /**
   *
   */
  public function legendTotalEventsByEventType($meeting_nodes = array(), $by_event = TRUE) {
    $legend_text = [];

    $chartData = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByMonthByEventType($meeting_nodes, $by_event);
    $chartLabel = \Drupal::service('ngdata.term')->getTermListByVocabulary('eventtype')['label'];
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . array_sum($row['data']) . ')';
      }
    }

    $legend_color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne(NULL, FALSE);
    $legends = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color, $max_length = NULL, 'font-size-14');

    return $legends;
  }

  /**
   *
   */
  public function legendTotalEventsByFundingSource($meeting_nodes = array(), $by_event = TRUE) {
    $chartData = \Drupal::service('ngdata.chart.chartjs')
      ->chartBarDataByEventsByMonthByFundingSource($meeting_nodes, $by_event);
    $chartLabel = \Drupal::service('ngdata.term')->getTermListByVocabulary('fundingsource')['label'];
    if ($chartData && is_array($chartData)) {
      foreach ($chartData as $key => $row) {
        $legend_text[] = $chartLabel[$key] . '(' . array_sum($row['data']) . ')';
      }
    }

    $legend_color = \Drupal::getContainer()->get('baseinfo.setting.service')->colorPlateLineChartOne(NULL, FALSE);
    $legends = \Drupal::getContainer()
      ->get('flexinfo.chart.service')
      ->renderLegendSquareColorKeyPlusOne($legend_text, $legend_color, $max_length = NULL, 'font-size-14');

    return $legends;
  }

  /**
   *
   */
  public function legendTotalEventsByFundingSourceWithLegendRelevant($meeting_nodes = array(), $by_event = TRUE) {
    $output = $this->legendTotalEventsByFundingSource($meeting_nodes, $by_event);

    $user_default_term_tids = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_term_fundingsource');
    if ($user_default_term_tids) {

      $trees = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree('fundingsource', 0);

      $temp_match = [];
      foreach ($trees as $key => $term) {
        $preg_string = '/<div class="clear-both.+' . $term->name . '\(\w*\)<\/span><\/div>/';

        if (in_array($term->tid, $user_default_term_tids)) {
          preg_match($preg_string, $output, $matches);
          $temp_match["temp_match_" . $key] = $matches[0];
          $output = preg_replace($preg_string, "temp_match_" . $key, $output);
        }
        else {
          $output = preg_replace($preg_string, "", $output);
        }
      }

      if ($temp_match) {
        foreach ($temp_match as $temp_match_key => $row) {
          $output = str_replace($temp_match_key, $row, $output);
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function htmlSectionBasicTableTemplate($header = NULL, $thead = NULL, $tbody = NULL, $color_box_palette = FALSE, $bg_color_class = 'bg-0f69af') {
    $table = $this->molecule->getBlockHeader($header, $color_box_palette, $bg_color_class);
    $table .= '<div class="html-basic-table-wrapper">';
      $table .= '<table class="table margin-bottom-0">';
        $table .= '<thead>';
          $table .= '<tr>';
            $table .= $thead;
          $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
          $table .= $tbody;
        $table .= '</tbody>';
      $table .= '</table>';
    $table .= '</div>';

    return $table;
  }

  /**
   *
    $tableData = $this->molecule->tableDataByEventList($meeting_nodes);
   */
  public function tableContentEventList($meeting_nodes = array(), $table_data_template_name = 'tableDataByEventList') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
    $tableData = $this->molecule->tableDataByTopProgram($meeting_nodes);
   */
  public function tableContentProgramList($meeting_nodes = array(), $table_data_template_name = 'tableDataByTopProgram') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);
    // $output['tSortField'] = "Program";

    return $output;
  }

  /**
   * @see $tableMiddle['tabledata']
      foreach ($speakers as $key => $value) {
        $tableMiddle['tabledata'][$key] = array(
          'Speaker' => $value,
          'Events' => rand(30, 100),
          'Reach' => rand(200, 300),
          'Rating' => rand(100, 200),
        );
      }

    $tableData = $this->molecule->tableDataByTopSpeaker($meeting_nodes, $limit_row, $question_tid);
   */
  public function tableContentSpeakerList($meeting_nodes = array(), $limit_row = NULL, $question_tid = NULL, $table_data_template_name = 'tableDataByTopSpeaker') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes, $limit_row, $question_tid);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
    $tableData = $this->molecule->tableDataByTermQuestion($meeting_nodes);
   */
  public function tableContentQuestionList($meeting_nodes = array(), $table_data_template_name = 'tableDataByTermQuestion') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);
    // $output['tSortField'] = "Program";

    return $output;
  }

  /**
   *
   */
  public function tableContentStandardnode($entity_id, $start, $end) {
    $tableData = $this->molecule->tableDataByStandardnode($entity_id, $start, $end);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomNodeMeeting($meeting_nodes, $entity_id, $start, $end, $table_data_template_name = 'tableDataByCustomNodeByMeeting') {
    $tableData = $this->molecule->{$table_data_template_name}($meeting_nodes, $entity_id, $start, $end);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomUserForAdminSection() {
    $tableData = $this->molecule->tableDataByCustomUserForAdminSection();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermProgram() {
    $tableData = $this->molecule->tableDataByCustomTermByProgram();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermQuestion() {
    $tableData = $this->molecule->tableDataByCustomTermByQestion();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermEvaluationForm() {
    $tableData = $this->molecule->tableDataByCustomTermEvaluationForm();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermBusinessunit() {
    $tableData = $this->molecule->tableDataByCustomTermBusinessunit();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermTherapeuticarea() {
    $tableData = $this->molecule->tableDataByCustomTermTherapeuticarea();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentCustomTermQuestionEvaluationForm() {
    $tableData = $this->molecule->tableDataByCustomTermQuestionEvaluationForm();
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentStandardterm($entity_id, $start, $end) {
    $tableData = $this->molecule->tableDataByStandardterm($entity_id, $start, $end);
    $tableMiddleFields = $this->molecule->tableHeaderGenerateFromTableDataArrayKeys($tableData);

    $output = $this->tableContentStandardTemplate($tableMiddleFields, $tableData);

    return $output;
  }

  /**
   *
   */
  public function tableContentStandardTemplate($tableMiddleFields, $tableData) {
    $output['tSortField'] = "";
    $output['tfields'] = $tableMiddleFields;
    $output['thead']   = $tableMiddleFields;

    $output['tabledata'] = $tableData;

    return $output;
  }

}
