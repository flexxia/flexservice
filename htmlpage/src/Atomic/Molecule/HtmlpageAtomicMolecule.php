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

  /**
   * @param $save_png_icon_style is css style, need include "float-right" and margin-top-12 margin-right-16
   */
  public function savePngIcon($save_png_icon_style = NULL, $save_block_id = NULL, $save_png_icon_enable = TRUE) {
    $output = "";
    $output .= '<div class="drop-down-icon-wrapper dropdown show ' . $save_png_icon_style . '">';

      if ($save_png_icon_enable) {
        $output .= '<a class="drop-down-icon-toggle dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
          $output .= '<i class="fa fa-angle-down color-fff"></i>';
        $output .= '</a>';

        $output .= '<div class="drop-down-icon-menu dropdown-menu padding-20 margin-left-n-86 text-align-center" aria-labelledby="dropdownMenuLink">';
          $output .= '<a onclick="saveHtmlToPng(\'' . $save_block_id . '\')" class="dropdown-item color-000 font-size-14" href="javascript:void(0);">';
            $output .= 'SAVE PNG';
          $output .= '</a>';
        $output .= '</div>';
      }

    $output .= '</div>';

    return $output;
  }

}
