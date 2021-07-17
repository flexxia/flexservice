<?php

/**
 * @file
 */

namespace Drupal\genpdf\Content;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Unicode;

use FPDF;
use Drupal\genpdf\Service\PDFDraw;

/**
 *
 */
class GenpdfDrawProgramTile {

  /**
   * split ProgramTile($fixedSection) array to multiple chunk group, each group have 4 tiles as one row
   */
  function drawProgramTile($cx, $cy, $fixedSection, $pdf) {
    $tile_row = [];

    if ($fixedSection && count($fixedSection) > 4) {
      $tile_row[] = array_slice($fixedSection, 0, 4);
      $tile_row[] = array_slice($fixedSection, 4, count($fixedSection));
    }
    else {
      $tile_row[] = $fixedSection;
    }

    foreach ($tile_row as $key => $value) {
      $this->drawProgramTileOneRow($cx, $cy + ($key * 100), $value, $pdf);
    }
  }

  /**
   *
   */
  function drawProgramTileOneRow($cx, $cy, $fixedSection, $pdf) {
    $pdf->SetFont('Arial', '', 16);
    $pdf->SetTextColor(255,255,255);

    for ($i = 0; $i < count($fixedSection) ; $i++) {
      if (isset($fixedSection[$i]['value']['header']['value']['value'])) {
        $headerValue = strip_tags($fixedSection[$i]['value']['header']['value']['value']);
        $headerValueOne = $fixedSection[$i]['value']['header']['valueOne']['value'];
        $headerValueOne = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $headerValueOne);

        $getColor = $fixedSection[$i]['value']['header']['class'];

        $color = Color::getColorArrayFromFixedSection($getColor);
        $pdf->SetFillColor($color[0], $color[1], $color[2]);

        $pdf->Rect($cx + $i * 10 - 38, $cy - 32, 272, 94, 'F');
        $pdf->SetXY($cx + $i * 10, $cy - 10);
        $pdf->Write(10, $headerValueOne);
        $pdf->SetXY($cx + $i * 10, $cy + 30);
        $pdf->Write(10, $headerValue);

        $cx = $cx + 300;
      }
    }
  }

}

/**
 *
 */
class Color {

  /**
   * @static
   */
  public static function getColorArrayFromFixedSection($color){
    $subStrColor = substr($color, 3, 6);
    return array(
      hexdec(substr($subStrColor, 0, 2)),
      hexdec(substr($subStrColor, 2, 2)),
      hexdec(substr($subStrColor, 4, 2)),
    );
  }

  /**
   * @static
   */
  public static function getColorArray($color) {
    $t = str_replace("#", "", $color);
    return array(
      hexdec(substr($t, 0, 2)),
      hexdec(substr($t, 2, 2)),
      hexdec(substr($t, 4, 2)),
    );
  }

}
