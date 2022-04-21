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
    $template_row .= '<div class="htmlpage-chartjs-section-wrapper">';
      $template_row .= '<div class="padding-10 height-500 width-500">';
        $template_row .= '<canvas id="' . $block_data['tabs'][0]['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $template_row .= '</canvas>';
      $template_row .= '</div>';
    $template_row .= '</div>';
    $html_templates[] = $template_row;

    $template_row = '';
    $template_row .= '<div class="htmlpage-chartjs-section-wrapper">';
      $template_row .= '<div class="padding-2 height-500 width-500">';
        $template_row .= '<canvas id="' . $block_data['tabs'][1]['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $template_row .= '</canvas>';
      $template_row .= '</div>';
    $template_row .= '</div>';
    $html_templates[] = $template_row;

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartSectionTemplateTabs($block_data, $html_templates);

    return $output;
  }

  /**
   * Full size for Chart section
   */
  public function blockChartjsTemplate($block_data = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-chartjs-section-wrapper">';
      $html_template .= '<div class="padding-2 height-500">';
        $html_template .= '<canvas id="' . $block_data['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $html_template .= '</canvas>';
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartSectionTemplate($block_data, $html_template);

    return $output;
  }

  /**
   * Chartjs
   */
  public function blockChartjsTemplateForMeetingPage($block_data = [], $meeting_nodes = array(), $question_term = NULL) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-chartjs-section-wrapper">';
      $html_template .= '<div class="border-none">';
        $html_template .= '<div class="height-auto">';
          $html_template .= '<div class="col-xs-7">';
            $html_template .= '<div class="margin-top-42 margin-bottom-20 margin-left-12">';
              $html_template .= '<canvas id="' . $block_data['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
              $html_template .= '</canvas>';
            $html_template .= '</div>';
          $html_template .= '</div>';
          // Flex 居中, 水平和垂直居中分着写，才都工作.
          $html_template .= '<div class="col-xs-5 display-flex justify-content-center">';
            $html_template .= '<div class="display-flex align-items-center min-height-320">';
              $html_template .= \Drupal::service('ngdata.atomic.molecule')
                ->getRaidoQuestionHtmlLegend($question_term, $meeting_nodes);
            $html_template .= '</div>';
          $html_template .= '</div>';
        $html_template .= '</div>';

        $html_template .= \Drupal::service('ngdata.atomic.molecule')
          ->getRaidoQuestionBottom($question_term, $meeting_nodes);
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartSectionTemplate($block_data, $html_template);

    return $output;
  }

}
