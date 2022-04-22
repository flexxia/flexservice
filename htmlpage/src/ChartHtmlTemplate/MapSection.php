<?php

/**
 * @file
 */

namespace Drupal\htmlpage\ChartHtmlTemplate;

/**
 * BootstrapTable.
 * \Drupal::service('htmlpage.charthtmltemplate.section.map')->demo();
 */
class MapSection {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   * BootstrapTable.
   */
  public function blockMapJqvmapTemplate(array $block_data = []) {
    $html_template = '';
    $html_template .= '<div class="htmlpage-map-section-wrapper">';
      $html_template .= '<div id="" class="htmlpage-jqvmap-wrapper padding-10 height-500">';
      $html_template .= '</div>';
    $html_template .= '</div>';

    $output = \Drupal::service('htmlpage.atomic.block')
      ->blockChartSectionTemplate($block_data, $html_template);

    return $output;
  }

}
