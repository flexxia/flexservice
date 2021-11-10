<?php

namespace Drupal\htmlpage\Atomic\Molecule;

use Drupal\htmlpage\Atomic\HtmlpageAtomic;

/**
 * Class HtmlpageAtomicMolecule.
  \Drupal::service('htmlpage.atomic.molecule')->demo();
 */
class HtmlpageAtomicMolecule extends HtmlpageAtomic {

  /**
   * Constructs a new HtmlpageAtomicMolecule object.
   */
  public function __construct() {
  }

  /**
   *
   */
  public function getTileBlockHeader($num, $tile_name = NULL, $css_class = NULL) {
    $output = "";
    $output .= '<div class="tile-block-header-molecule line-height-1 padding-12 ' . $css_class . '">';
      $output .= '<div class="margin-top-12 font-size-16">';
        $output .= $num;
      $output .= "</div>";

      $output .= '<div class="margin-top-24 line-height-2 margin-bottom-6 font-size-14">';
        $output .= $tile_name;
      $output .= "</div>";
    $output .= "</div>";

    return $output;
  }

}
