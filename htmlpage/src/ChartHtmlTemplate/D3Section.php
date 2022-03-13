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
