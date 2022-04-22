<?php

/**
 * @file
 */

namespace Drupal\htmlpage\ChartHtmlTemplate;

/**
 * ECharts.
 * \Drupal::service('htmlpage.charthtmltemplate.section.html')->demo();
 */
class HtmlSection {

  /**
   * Constructs a new object.
   */
  public function __construct() {

  }

  /**
   *
   */
  public function blockHtmlTemplate($block_data = [], $html_body = NULL) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-html-section-wrapper">';
      $html_template .= '<div class="padding-10 padding-top-0 height-auto">';
        $html_template .= $html_body;
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockHtmlSectionTemplate($block_data, $html_template);

    return $output;
  }

}
