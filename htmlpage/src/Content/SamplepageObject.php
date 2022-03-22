<?php

/**
 * @file
 */

namespace Drupal\htmlpage\Content;

/**
 * \Drupal::service('htmlpage.object.samplepage')->demo().
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
    $output['json_content'] = \Drupal::service('htmlpage.chartjsonbase.chartjstemplate')->chartjsBaseMultipleBar();

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
  public function d3Cloud() {
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
      ->blockTileSectionAdmindashboard();
    $output['html_content'] .= \Drupal::service('htmlpage.atomic.template')
      ->blockHtmlClearBoth();

    // Jqvmap
    // $output['html_content'] .= \Drupal::service('htmlpage.charthtmltemplate.section.map')
    //   ->blockMapJqvmapTemplate(['title' => 'Sample Map', 'block_id' => 'jqvmap-sample-block-1']);

    // Chartjs.
    // $output['html_content'] .= $this->chartjsBar()['html_content'];
    // $output['json_content'][] = $this->chartjsBar()['json_content'];

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
    $output['html_content'] .= $this->d3Cloud()['html_content'];
    $output['json_content'][] = $this->d3Cloud()['json_content'];

    // Bootstrap Table.
    // $output['html_content'] .= \Drupal::service('htmlpage.charthtmltemplate.section.bootstraptable')
    //   ->blockBootstrapTableTemplate();

    return $output;
  }

}
