<?php

namespace Drupal\htmlpage\Atomic\Organism;

use Drupal\htmlpage\Atomic\HtmlpageAtomic;

/**
 * Class HtmlpageAtomicOrganism.
  \Drupal::service('htmlpage.atomic.organism')->demo();
 */
class HtmlpageAtomicOrganism extends HtmlpageAtomic {

  /**
   * Constructs a new HtmlpageAtomicOrganism object.
   */
  public function __construct() {
  }

  /**
   *
   */
  public function tileSection($num, $tile_name, $css_class = NULL) {
    $output = '';
    $output .= '<div class="tile-block-section-wrapper col-xs-12 col-sm-6 col-md-3 margin-top-12">';
      $output .= \Drupal::service('htmlpage.atomic.molecule')
        ->getTileBlockHeader($num, $tile_name, $css_class, TRUE);
    $output .= "</div>";

    return $output;
  }

}
