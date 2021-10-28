<?php

namespace Drupal\htmlpage\Atomic\Template;

use Drupal\htmlpage\Atomic\HtmlpageAtomic;

/**
 * Class HtmlpageAtomicTemplate.
 \Drupal::service('htmlpage.atomic.template')->demo()
 */
class HtmlpageAtomicTemplate extends HtmlpageAtomic {

  /**
   * Constructs a new HtmlpageAtomicTemplate object.
   */
  public function __construct() {
  }

  /**
   *
   */
  public function blockHtmlClearBoth() {
    $output = "";

    $output .= '<div class="block-html-clear-both-wrapper clear-both">';
    $output .= '</div>';
    $output .= '<div style="clear:both; height:1px;">';
    $output .= '</div>';

    return $output;
  }

}
