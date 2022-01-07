<?php

/**
 * @file
 */

namespace Drupal\htmlpage\ChartHtmlTemplate;

/**
 * \Drupal::service('htmlpage.charthtmltemplate.section.d3')->demo();
 */
class D3Section {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   * D3 Chart.
   */
  public function blockD3Template($block_data = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-d3-wrapper">';
      $html_template .= '<div id="' . $block_data['chart_canvas_id'] . '" class="padding-10">';
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartTemplate($block_data, $html_template);

    return $output;
  }

  /**
   * D3 Chart.
   */
  public function blockD3Template7Fairy($block_data = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-d3-wrapper">';
      $html_template .= '<div id="' . $block_data['chart_canvas_id'] . '" class="padding-10">';
      $html_template .= '</div>';
    $html_template .= '</div>';
    $html_template .= '<div class="line-path-chart-wrapper">';
      $html_template .= '<svg class="line-path-chart" width="760" height="200">';
        $html_template .= '<path>';
        $html_template .= '</path>';
      $html_template .= '</svg>';
    $html_template .= '</div>';
    $html_template .= '<div class="rect1-chart">';
      $html_template .= '<svg class="rect-chart" width="760" height="200">';
      $html_template .= '</svg>';
    $html_template .= '</div>';
    $html_template .= '<div  class="circle-chart" width="960" height="200">';
      // $html_template .= '<svg class="circle-chart" width="760" height="200">';
      // $html_template .= '</svg>';
    $html_template .= '</div>';
    $html_template .= '
      <svg width="300" height="180" >
        <path d="
          M 18,3
          L 46,3
          L 46,40
          L 61,40
          L 32,68
          L 3,40
          L 18,40
          Z
        " fill="orange">
        </path>
      </svg>

    ';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartTemplate($block_data, $html_template);

    return $output;
  }

  /**
   * D3 Axis.
   */
  public function blockD3TemplateAxis($block_data = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-d3-wrapper">';
      $html_template .= '<div id="' . $block_data['chart_canvas_id'] . '" class="padding-10">';
        $html_template .= '<svg class="axis-chart" width="760" height="200">';
        $html_template .= '</svg>';
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartTemplate($block_data, $html_template);

    return $output;
  }

  /**
   * D3 Chart.
   */
  public function blockD3TemplateMapSvg($block_data = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-d3-wrapper">';
      $html_template .= '<div id="' . $block_data['chart_canvas_id'] . '" class="padding-10">';
      $html_template .= '</div>';
      $html_template .= '<div id="content">
    <div class="info">Hover over a country</div>
    <svg width="620px" height="100px">
      <g class="map"></g>
      <g class="bounding-box"><rect></rect></g>
      <g class="centroid"><circle r="4"></circle></g>
    </svg>
  </div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartTemplate($block_data, $html_template);

    return $output;
  }

}
