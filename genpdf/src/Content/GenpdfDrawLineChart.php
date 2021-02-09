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
class GenpdfDrawLineChart extends GenpdfBarAndLineTemplate {

  function drawLineChartNew($cx, $cy, $LineChartData, $pdf, $LineChartHeight = 300, $LineChartWidth = 500, $spaceBetweenLine = 36) {

    $LineChart = array();
    $LineChartLabel = array();

    foreach ($LineChartData as $key => $value) {
      $LineChart[$key] = $value["value"];
      $LineChartLabel[$key] = $value["legend"];
    }

    $labelCount = count($LineChartLabel);
    $spaceBetweenPoints = $LineChartWidth / $labelCount;
    $lineChartPointNum = $labelCount;
    $maxPoint = max($LineChart);

    for ($i=0; $i < $lineChartPointNum; $i++) {
      if ($i == 0) {
        $linePointXPosition[$i] = $cx;
      }
      else {
        $linePointXPosition[$i] = $linePointXPosition[$i - 1] + $spaceBetweenPoints;
      }
    }

    for ($i = 0; $i < $lineChartPointNum - 1; $i++) {
      $pointPrePercentHeight = $LineChartHeight * round($LineChart[$i] / $maxPoint, 2);
      $pointPostPercentHeight = $LineChartHeight * round($LineChart[$i + 1] / $maxPoint, 2);
      // draw lines
      $pdf->SetLineWidth(2.6);
      $pdf->SetDrawColor(86,191,181);
      $pdf->Line($linePointXPosition[$i], $cy + $LineChartHeight - $pointPrePercentHeight, $linePointXPosition[$i + 1], $cy + $LineChartHeight - $pointPostPercentHeight);
      //point and text
      $pdf->SetTextColor(0, 0, 0);
      $this->drawPointAndText($linePointXPosition[$i], $LineChart[$i], $cy + $LineChartHeight, $pointPrePercentHeight, $pdf);
      // line and label
      $this->drawVerticalLineAndLabel($linePointXPosition[$i], $LineChartLabel[$i], $cy, $LineChartHeight, $pdf, $spaceBetweenPoints);
    }

    $pdf->SetTextColor(0, 0, 0);
    // last point text
    $pointLastPercentHeight = $LineChartHeight * round($LineChart[$lineChartPointNum - 1] / $maxPoint, 2);
    $this->drawPointAndText($linePointXPosition[$lineChartPointNum - 1], $LineChart[$lineChartPointNum - 1], $cy + $LineChartHeight, $pointLastPercentHeight, $pdf);

     //last line and label
    $this->drawVerticalLineAndLabel($linePointXPosition[$lineChartPointNum - 1], $LineChartLabel[$lineChartPointNum - 1], $cy, $LineChartHeight, $pdf, $spaceBetweenPoints);
  }

  /**
   *
   */
  function drawLineChartCoordinateLines($cx, $cy, $horizontalSpaceBetweenFrame, $LineChartHeightStart, $LineChartHeightEnd, $frameSize, $pdf) {
    $pdf->SetXY($cx, $cy);
    $pdf->Line($cx + $horizontalSpaceBetweenFrame, $cy + $LineChartHeightEnd, $cx + $frameSize, $cy + $LineChartHeightEnd);
    $pdf->Line($cx + $horizontalSpaceBetweenFrame , $cy + $LineChartHeightEnd, $cx + $horizontalSpaceBetweenFrame, $cy + $LineChartHeightStart);
  }

  /**
   *
   */
  function drawPointAndText($lineChartXPosition, $LineChart, $LineChartHeightEnd, $pointPrePercentHeight, $pdf) {
    $pdf->SetXY($lineChartXPosition, $LineChartHeightEnd - $pointPrePercentHeight - 30);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(10, 30, $LineChart, 0, 0, 'C');
    $pdf->SetFillColor(86,191,181);
    $pdf->Circle($lineChartXPosition, $LineChartHeightEnd - $pointPrePercentHeight, 3, 0, 360, $style = 'F');
  }

  /**
   *
   */
  function drawVerticalLineAndLabel($lineChartXPosition, $LineChartLabel, $cy, $LineChartHeight, $pdf, $spaceBetweenPoints) {
    $pdf->SetDrawColor(217, 217, 217);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetLineWidth(0.6);
    $pdf->SetFont('Arial', '', 12);
    // $pdf->SetXY($lineChartXPosition, $cy + $LineChartHeightEnd);
    $pdf->Line($lineChartXPosition, $cy + $LineChartHeight, $lineChartXPosition, $cy);
    // $pdf->Cell(10, 30, $LineChartLabel, 0, 0, 'C');

    $first_line_legend = Unicode::truncate($LineChartLabel, 10, TRUE, FALSE);
    $rest_legend = str_replace($first_line_legend, "", $LineChartLabel);
    if ($rest_legend !== '') {
      $second_line_legend = Unicode::truncate($rest_legend, 10, TRUE, FALSE);
      if (strlen($rest_legend) > 10) {
        $second_line_legend .= '...';
      }
      $pdf->SetXY($lineChartXPosition, $cy + $LineChartHeight + 16);
      $pdf->Cell(10, 36, $second_line_legend, 0, 0, 'C');
    }
    $pdf->SetXY($lineChartXPosition, $cy + $LineChartHeight);
    $pdf->Cell(10, 36, $first_line_legend, 0, 0, 'C');
  }

  /**
   *
   */
  function drawHorizontalLine($cx, $cy, $horizontalSpaceBetweenFrame, $LineChartHeightEnd, $LineChartHeightStart, $frameSize, $maxPoint, $pdf) {
    $pdf->SetFont('Arial', '', 18);
    //horizontal line with midium value
    $pdf->Line($cx + $horizontalSpaceBetweenFrame, $cy + ($LineChartHeightEnd - $LineChartHeightStart)/ 2 + $LineChartHeightStart, $cx + $frameSize, $cy + $LineChartHeightStart + ($LineChartHeightEnd - $LineChartHeightStart) / 2);
    $pdf->SetXY($cx + 10, $cy + $LineChartHeightEnd / 2);
    $pdf->Cell(10, 10, $maxPoint / 2, 0, 0, 'C');

    //horizontal line with maxmum value
    $pdf->Line($cx + $horizontalSpaceBetweenFrame, $cy + $LineChartHeightStart , $cx + $frameSize, $cy + $LineChartHeightStart );
    $pdf->SetXY($cx + 10, $cy + $LineChartHeightStart );
    $pdf->Cell(10, 10, $maxPoint, 0, 0, 'C');
  }
}
