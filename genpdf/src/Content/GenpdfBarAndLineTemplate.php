<?php

/**
 * @file
 */

namespace Drupal\genpdf\Content;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Unicode;

use FPDF;
use Drupal\genpdf\Service\PDFDraw;

class GenpdfBarAndLineTemplate extends GenpdfCircleChartTemplate{

  /**
   *
   */
  function getChartBarTitle($contentSectionSub) {
    $output = NULL;

    if (isset($contentSectionSub['middle']['middleBottom'])) {
      $output = $contentSectionSub['middle']['middleBottom'];

      $output = strip_tags($output);

      // replace special sign to standard one
      $output = str_replace("’", "'", $output);    // single quote
      $output = str_replace("“", '"', $output);    // double quote start
      $output = str_replace("”", '"', $output);    // double quote end
    }

    return $output;
  }

  /**
   *
   */
  function drawChartCoordinateLines($chartXPositionCol6, $chartYPosition, $ChartHeight, $chartWidth, $pdf) {
    $pdf->SetXY($chartXPositionCol6, $chartYPosition);
    $pdf->Line($chartXPositionCol6, $chartYPosition + $ChartHeight, $chartXPositionCol6 + $chartWidth, $chartYPosition + $ChartHeight);
    $pdf->Line($chartXPositionCol6, $chartYPosition + $ChartHeight, $chartXPositionCol6, $chartYPosition);
  }

  /**
   *
   */
  function drawBar($pdf, $barColor, $barXPositionCol6, $barFirstRowYPositionCol, $barWidth, $barheight, $labelXPosition, $labelYPosition, $labelName, $labelConnection, $labelValue, $labelCellWidth = 36, $labelCellHeight = 36, $labelColor = [0 , 0, 0]) {
    $pdf->Rect($barXPositionCol6, $barFirstRowYPositionCol, $barWidth, $barheight, 'F', $border_style  = null, $barColor);
    $pdf->SetTextColor($labelColor[0], $labelColor[1], $labelColor[2]);
    $pdf->SetXY($labelXPosition, $labelYPosition - 10);
    $pdf->Cell($labelCellWidth, $labelCellHeight, $labelName . $labelConnection . $labelValue, 0, 0, 'C');
  }
}