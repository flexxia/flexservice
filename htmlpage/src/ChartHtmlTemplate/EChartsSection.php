<?php

/**
 * @file
 */

namespace Drupal\htmlpage\ChartHtmlTemplate;

/**
 * ECharts.
 * \Drupal::service('htmlpage.charthtmltemplate.section.echarts')->demo();
 */
class EChartsSection {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   *
   */
  public function blockEChartsTemplate($block_data = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-echarts-section-wrapper">';
      $html_template .= '<div id="' . $block_data['chart_canvas_id'] . '" class="padding-10 height-500">';
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartSectionTemplate($block_data, $html_template);

    return $output;
  }

}
