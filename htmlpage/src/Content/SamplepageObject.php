<?php

/**
 * @file
 */

namespace Drupal\htmlpage\Content;

/**
 * \Drupal::service('htmlpage.content.samplepage')->demo().
 */
class SamplepageObject {

  /**
   * Chartjs.
   */
  public function chartjsBar() {
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

  /**
   *
   */
  public function chartjsBarMultiple() {
    $output = [];
    $block_definition = [
      'title' => 'Sample Bar Chart',
      'block_id' => 'chartjs-tabs-sample-block-1',
      'tabs' => [
        [
          'name' => 'Bar Chart',
          'tab_id' => 'chartjs-bar-sample-block-1',
          'chart_canvas_id' => 'chartjs-bar-sample-1',
        ],
        [
          'name' => 'Dounut Chart',
          'tab_id' => 'chartjs-doughnut-sample-block-1',
          'chart_canvas_id' => 'chartjs-doughnut-sample-1',
        ],
      ],
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.chartjs')
      ->blockChartjsTemplateTabs($block_definition);
    $output['json_content'][] = \Drupal::service('htmlpage.chartjsonbase.chartjstemplate')->chartjsBaseBar();
    $output['json_content'][] = \Drupal::service('htmlpage.chartjsonbase.chartjstemplate')->chartjsBaseDoughnut();

    return $output;
  }

  /**
   * @see block_id is for save png
   */
  public function d3Bar() {
    $d3_canvas_id = 'd3-bar-sample-1';
    $block_definition = [
      'title' => 'D3 Js Bar Chart and Average line',
      'block_id' => 'd3-bar-sample-block-1',
      'chart_canvas_id' => $d3_canvas_id,
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.d3')
      ->blockD3Template($block_definition);
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.d3template')
      ->d3BaseJson('bar', $d3_canvas_id);

    return $output;
  }

  /**
   *
   */
  public function d3WordCloud() {
    $d3_canvas_id = 'd3-cloud-sample-1';
    $block_definition = [
      'title' => 'D3Js Word Cloud Layout',
      'block_id' => 'd3-cloud-sample-block-1',
      'chart_canvas_id' => $d3_canvas_id,
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.d3')
      ->blockD3Template($block_definition);
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.d3template')
      ->d3BaseJson('word_cloud', $d3_canvas_id);

    return $output;
  }

  /**
   * D3 Cloud.
   */
  public function d3WordCloudMeetingComments($block_data = []) {
    $output = [];

    $d3_canvas_id = 'd3-cloud-sample-meeting-1';
    $block_definition = [
      'title' => 'Program -- "I just want to be my old self again" - Overcoming the Status Quo in MDD',
      'block_id' => 'd3-cloud-meeting-block-1',
      'chart_canvas_id' => $d3_canvas_id,
    ];

    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.d3')
      ->blockD3Template($block_definition);

    $json_content = \Drupal::service('htmlpage.chartjsonbase.d3template')
      ->d3BaseJson('word_cloud', $d3_canvas_id);

    // Two words
    $json_content['content']['data']['datasets'] = [
      ["name" => "thank you", "count_field" => 6,],
      ["name" => "management of", "count_field" => 4,],
      ["name" => "of the ", "count_field" => 3,],
      ["name" => "anxiety and", "count_field" => 3,],
      ["name" => "more time", "count_field" => 3,],
      ["name" => "for bipolar", "count_field" => 3,],
      ["name" => "of this", "count_field" => 2,],
      ["name" => "the topic", "count_field" => 2,],
      ["name" => "topic of ", "count_field" => 2,],
      ["name" => "will use ", "count_field" => 2,],
      ["name" => "use switch ", "count_field" => 2,],
      ["name" => "switch rx", "count_field" => 2,],
      ["name" => "rx more", "count_field" => 2,],
      ["name" => "of anxiety ", "count_field" => 2,],
      ["name" => "and depression ", "count_field" => 2,],
      ["name" => "on the ", "count_field" => 2,],
      ["name" => "list of", "count_field" => 2,],
      ["name" => "case studies ", "count_field" => 2,],
      ["name" => "studies very ", "count_field" => 2,],
      ["name" => "look at", "count_field" => 2,],
      ["name" => "importance of", "count_field" => 2,],
      ["name" => "great presentation ", "count_field" => 2,],
      ["name" => "of medication", "count_field" => 2,],
      ["name" => "n a", "count_field" => 2,],
      ["name" => "role of", "count_field" => 2,],
      ["name" => "adhd treatments", "count_field" => 2,],
      ["name" => "what i’m ", "count_field" => 2,],
      ["name" => "for mdd", "count_field" => 2,],
      ["name" => "more about ", "count_field" => 2,],
      ["name" => "for certain", "count_field" => 2,],
    ];

    // One Words
    $json_content['content']['data']['datasets'] = [
      ["name" => "anxiety", "count_field" => 10,],
      ["name" => "very ", "count_field" => 8,],
      ["name" => "will ", "count_field" => 6,],
      ["name" => "thank", "count_field" => 6,],
      ["name" => "depression ", "count_field" => 6,],
      ["name" => "adhd ", "count_field" => 6,],
      ["name" => "program", "count_field" => 5,],
      ["name" => "time ", "count_field" => 5,],
      ["name" => "excellent", "count_field" => 5,],
      ["name" => "treatment", "count_field" => 5,],
      ["name" => "mdd", "count_field" => 5,],
      ["name" => "list ", "count_field" => 4,],
      ["name" => "change ", "count_field" => 4,],
      ["name" => "none ", "count_field" => 4,],
      ["name" => "symptoms ", "count_field" => 4,],
      ["name" => "meds ", "count_field" => 4,],
      ["name" => "management ", "count_field" => 4,],
      ["name" => "would", "count_field" => 4,],
      ["name" => "discussion ", "count_field" => 4,],
      ["name" => "practice ", "count_field" => 3,],
      ["name" => "well ", "count_field" => 3,],
      ["name" => "better ", "count_field" => 3,],
      ["name" => "great", "count_field" => 3,],
      ["name" => "presentation ", "count_field" => 3,],
      ["name" => "nil", "count_field" => 3,],
      ["name" => "effects", "count_field" => 3,],
      ["name" => "doing", "count_field" => 3,],
      ["name" => "trintellix ", "count_field" => 3,],
      ["name" => "between", "count_field" => 3,],
      ["name" => "bipolar", "count_field" => 3,],
    ];
    $output['json_content'] = $json_content;

    return $output;
  }

  /**
   * D3 7 Fairy.
   */
  public function d37Fairy() {
    $output = [];
    $d3_canvas_id = 'd3-7fairy-sample-1';
    $block_definition = [
      'title' => 'Sample 7 Fairy plot D3',
      'block_id' => 'd3-7fairy-sample-block-1',
      'chart_canvas_id' => $d3_canvas_id,
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.d3')
      ->blockD3Template7Fairy($block_definition);
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.d3template')
      ->d3BaseJson('7fairy', $d3_canvas_id);

    return $output;
  }

  /**
   *
   */
  public function d3Map() {
    $d3_canvas_id = 'd3-map-sample-1';
    $block_definition = [
      'title' => 'Sample map D3',
      'block_id' => 'd3-map-sample-block-1',
      'chart_canvas_id' => $d3_canvas_id,
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.d3')
      ->blockD3TemplateMapSvg($block_definition);
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.d3template')
      ->d3BaseJson('map', $d3_canvas_id);

    return $output;
  }

  /**
   *
   */
  public function d3Scatterplot() {
    $d3_canvas_id = 'd3-scatterplot-sample-1';
    $block_definition = [
      'title' => 'Sample Scatterplot D3',
      'block_id' => 'd3-scatterplot-sample-block-1',
      'chart_canvas_id' => $d3_canvas_id,
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.d3')
      ->blockD3Template($block_definition);
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.d3template')
      ->d3BaseJson('scatter_plot', $d3_canvas_id);

    return $output;
  }

  /**
   *
   */
  public function d3Text() {
    $d3_canvas_id = 'd3-text-sample-1';
    $block_definition = [
      'title' => 'Sample Text D3',
      'block_id' => 'd3-text-sample-block-1',
      'chart_canvas_id' => 'd3-text-sample-1',
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.d3')
      ->blockD3Template($block_definition);
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.d3template')
    ->d3BaseJson('text', $d3_canvas_id);

    return $output;
  }

  /**
   *
   */
  public function eChartsBar() {
    $block_definition = [
      'title' => 'ECharts Bar ',
      'block_id' => 'echarts-bar-sample-block-1',
      'chart_canvas_id' => 'echarts-bar-sample-1',
      'chart_canvas_id' => 'echarts-bar-sample-1',
    ];
    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.echarts')
      ->blockEChartsTemplate($block_definition);
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.echartstemplate')
      ->echartsBaseBar();

    return $output;
  }

  /**
   *
   */
  public function samplePageContent($block_data = []) {
    $output = [];
    $output['html_content'] = NULL;
    $output['json_content'] = [];

    // Tile
    $output['html_content'] .= \Drupal::service('htmlpage.atomic.block')
      ->blockTileSectionAdmindashboardSample();
    $output['html_content'] .= \Drupal::service('htmlpage.atomic.template')
      ->blockHtmlClearBoth();

    // Jqvmap
    // $output['html_content'] .= \Drupal::service('htmlpage.charthtmltemplate.section.map')
    //   ->blockMapJqvmapTemplate(['title' => 'Sample Map', 'block_id' => 'jqvmap-sample-block-1']);

    // Chartjs.
    $output['html_content'] .= $this->chartjsBar()['html_content'];
    $output['json_content'][] = $this->chartjsBar()['json_content'];

    // Chartjs Multiple Tab.
    // $output['html_content'] .= $this->chartjsBarMultiple()['html_content'];
    // $output['json_content'][] = $this->chartjsBarMultiple()['json_content'][0];
    // $output['json_content'][] = $this->chartjsBarMultiple()['json_content'][1];

    // D3.
    // $output['html_content'] .= $this->d37Fairy()['html_content'];
    // $output['json_content'][] = $this->d37Fairy()['json_content'];

    // D3 Bar.
    $output['html_content'] .= $this->d3Bar()['html_content'];
    $output['json_content'][] = $this->d3Bar()['json_content'];

    // D3 Map.
    // $output['html_content'] .= $this->d3Map()['html_content'];
    // $output['json_content'][] = $this->d3Map()['json_content'];

    // D3 Scatterplot.
    // $output['html_content'] .= $this->d3Scatterplot()['html_content'];
    // $output['json_content'][] = $this->d3Scatterplot()['json_content'];

    // D3 Text.
    // $output['html_content'] .= $this->d3Text()['html_content'];
    // $output['json_content'][] = $this->d3Text()['json_content'];

    // D3 EChart.
    $output['html_content'] .= $this->eChartsBar()['html_content'];
    $output['json_content'][] = $this->eChartsBar()['json_content'];

    // D3 Cloud.
    $output['html_content'] .= $this->d3WordCloud()['html_content'];
    $output['json_content'][] = $this->d3WordCloud()['json_content'];

    // D3 Cloud for meeting comments.
    $output['html_content'] .= $this->d3WordCloudMeetingComments()['html_content'];
    $output['json_content'][] = $this->d3WordCloudMeetingComments()['json_content'];

    // Bootstrap Table.
    // $output['html_content'] .= \Drupal::service('htmlpage.charthtmltemplate.section.bootstraptable')
    //   ->blockBootstrapTableTemplate();

    return $output;
  }

}
