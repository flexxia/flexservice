<?php

/**
 * @file
 */

namespace Drupal\htmlpage\ChartHtmlTemplate;

/**
 * Chartjs.
 * \Drupal::service('htmlpage.charthtmltemplate.section.chartjs')->demo();
 */
class ChartjsSection {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   *
   */
  public function blockChartjsTemplateTabs($block_data = []) {
    $template_row = '';
    $template_row .= '<div class="htmlpage-chartjs-wrapper">';
      $template_row .= '<div class="padding-10 height-500 width-500">';
        $template_row .= '<canvas id="' . $block_data['tabs'][0]['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $template_row .= '</canvas>';
      $template_row .= '</div>';
    $template_row .= '</div>';
    $html_templates[] = $template_row;

    $template_row = '';
    $template_row .= '<div class="htmlpage-chartjs-wrapper">';
      $template_row .= '<div class="padding-10 height-500 width-500">';
        $template_row .= '<canvas id="' . $block_data['tabs'][1]['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $template_row .= '</canvas>';
      $template_row .= '</div>';
    $template_row .= '</div>';
    $html_templates[] = $template_row;

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartTemplateTabs($block_data, $html_templates);

    return $output;
  }

  /**
   * Chartjs
   */
  public function blockChartjsTemplate($block_data = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-chartjs-wrapper">';
      $html_template .= '<div class="padding-10 height-500">';
        $html_template .= '<canvas id="' . $block_data['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $html_template .= '</canvas>';
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartTemplate($block_data, $html_template);

    return $output;
  }

}
