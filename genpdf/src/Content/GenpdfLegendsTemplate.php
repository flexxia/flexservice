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
 * GenpdfCircleChartTemplate will inherit from this class
 */
class GenpdfLegendsTemplate {

  public $drawWhiteCenterCircle;

  function drawLegends($pdf, $legendRecPositionX, $legendRecPositionY, $legendRecColor, $legendText, $legendValue, $displayColorRect = true, $displayLegendValue = true) {

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 12);

    if ($displayColorRect) {
      $pdf->Rect($legendRecPositionX, $legendRecPositionY, 12, 12, 'F', $border_style = null, $legendRecColor);
      $pdf->SetXY($legendRecPositionX + 14, $legendRecPositionY);
    }
    else {
      $pdf->SetXY($legendRecPositionX, $legendRecPositionY);
    }
    if ($displayLegendValue) {
      $pdf->Write(10, $legendText . " (" . $legendValue . ")");
    }
    else {
      $pdf->Write(10, $legendText);

    }
  }
}

