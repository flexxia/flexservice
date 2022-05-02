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
  public function blockChartjsTemplateTabs($block_definition = []) {
    $template_row = '';
    $template_row .= '<div class="htmlpage-chartjs-section-wrapper">';
      $template_row .= '<div class="padding-10 height-500 width-500">';
        $template_row .= '<canvas id="' . $block_definition['tabs'][0]['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $template_row .= '</canvas>';
      $template_row .= '</div>';
    $template_row .= '</div>';
    $html_templates[] = $template_row;

    $template_row = '';
    $template_row .= '<div class="htmlpage-chartjs-section-wrapper">';
      $template_row .= '<div class="padding-2 height-500 width-500">';
        $template_row .= '<canvas id="' . $block_definition['tabs'][1]['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $template_row .= '</canvas>';
      $template_row .= '</div>';
    $template_row .= '</div>';
    $html_templates[] = $template_row;

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartSectionTemplateTabs($block_definition, $html_templates);

    return $output;
  }

  /**
   * Full size for Chart section
   */
  public function blockChartjsTemplate($block_definition = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-chartjs-section-wrapper">';
      $html_template .= '<div class="padding-2 height-500">';
        $html_template .= '<canvas id="' . $block_definition['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
        $html_template .= '</canvas>';
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartSectionTemplate($block_definition, $html_template);

    return $output;
  }

  /**
   * Chartjs standard block
   */
  public function blockChartjsTemplateForMeetingPage($block_definition = [], $meeting_nodes = [], $question_term = NULL) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-chartjs-section-wrapper">';
      $html_template .= '<div class="border-none">';
        $html_template .= '<div class="height-auto">';
          $html_template .= '<div class="col-xs-7">';
            $html_template .= '<div class="margin-top-42 margin-bottom-20 margin-left-12">';
              $html_template .= '<canvas id="' . $block_definition['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
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
      ->blockChartSectionTemplate($block_definition, $html_template);

    return $output;
  }

  /**
   * Chartjs, 两个Pie chart
   */
  public function blockChartjsTemplateForMeetingPageForPrePostPieChartColumn12($block_definition = [], $meeting_nodes = [], $question_term = NULL) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-chartjs-section-wrapper">';
      $html_template .= '<div class="border-none">';
        $html_template .= '<div class="height-auto">';

          $html_template .= '<div class="col-xs-12">';
            $html_template .= '<div class="text-center">';
              $html_template .= $block_definition['other_value']['prepost_diff'];
            $html_template .= '</div>';
          $html_template .= '</div>';

          $html_template .= '<div class="col-xs-6">';
            $html_template .= '<div class="col-xs-6 col-md-offset-3">';
              $html_template .= '<div class="margin-top-42 margin-bottom-20 margin-left-12">';
                $html_template .= '<canvas id="' . $block_definition['multiple_chart'][0]['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
                $html_template .= '</canvas>';
              $html_template .= '</div>';
            $html_template .= '</div>';
          $html_template .= '</div>';

          $html_template .= '<div class="col-xs-6">';
            $html_template .= '<div class="col-xs-6 col-md-offset-3">';
              $html_template .= '<div class="margin-top-42 margin-bottom-20 margin-left-12">';
                $html_template .= '<canvas id="' . $block_definition['multiple_chart'][1]['chart_canvas_id'] . '" class="htmlpage-chartjs-chart" width="100">';
                $html_template .= '</canvas>';
              $html_template .= '</div>';
            $html_template .= '</div>';
          $html_template .= '</div>';
        $html_template .= '</div>';
        $html_template .= '<div class="clear-both">';
        $html_template .= '</div>';

        $html_template .= $this->getLegendHorizontalForPrePostPieChartColumn12($meeting_nodes, $question_term);

        $html_template .= \Drupal::service('ngdata.atomic.molecule')
          ->getRaidoPrePostQuestionBottom($question_term, $meeting_nodes);
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartSectionTemplate($block_definition, $html_template);

    return $output;
  }

  /**
   *
   */
  public function getLegendHorizontalForPrePostPieChartColumn12($meeting_nodes = [], $question_term = NULL) {
    $output = "";
    $output .= '<div class="text-center width-pt-50 display-inline-block">';
      $output .= '<div class="display-inline-block">';
        $output .= \Drupal::service('ngdata.atomic.organism')
          ->getRaidoQuestionLegendHorizontalWithReferOther($question_term, $meeting_nodes, "Pre", "PRE-PROGRAM");
      $output .= '</div>';
    $output .= '</div>';
    $output .= '<div class="text-center width-pt-50 display-inline-block">';
      $output .= '<div class="display-inline-block">';
        $output .= \Drupal::service('ngdata.atomic.organism')
          ->getRaidoQuestionLegendHorizontalWithReferOther($question_term, $meeting_nodes, "Post", "POST-PROGRAM");
      $output .= '</div>';
    $output .= '</div>';
    $output .= '<div class="clear-both">';
    $output .= '</div>';

    return $output;
  }

}
