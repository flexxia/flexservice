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
class ChartBlock {

  public $value;
  public $color;
  public $title;
  public $legend;

  /**
   *
   */
  function __construct($value, $color, $title, $legend) {
    $this->value = $value;
    $this->color = $color;
    $this->title = $title;
    $this->legend = $legend;
  }

}

/**
 * pie charts and doughnut charts will inherit from this class
 */
class GenpdfCircleChartTemplate extends GenpdfLegendsTemplate{

  public $drawWhiteCenterCircle;

  /**
  * @param $cx
  */
  function drawCircleChart($percentTextShowedOnPieChart, $cx , $cy, $chartData, $pdf, $circleRadius = 140, $cross_text = NULL) {
    $crx = $cx + $percentTextShowedOnPieChart + 218;

    if (count($chartData) > 7) {
      $chartRecCY = 190;
    }
    elseif (count($chartData) == 2) {
      $chartRecCY = 260;
    }
    else {
      $chartRecCY = 230;
    }

    $totalCount = 0;
    $value      = array();
    $legend     = array();
    $charts     = array();

    foreach ($chartData as $val) {
      $charts[] = new ChartBlock($val['value'], $val['color'], $val['title'], $val['legend']);
    }

    foreach ($charts as $counter => $val) {
      $value[$counter] = (int) $val->value;
      $legend[$counter] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $val->legend);
      $totalCount      = $totalCount + $value[$counter];
      $color           = Color::getColorArray($val->color);

      if (!$this->drawWhiteCenterCircle) {

        $first_line_legend = Unicode::truncate($legend[$counter], 22, TRUE, FALSE);

        $rest_legend = str_replace($first_line_legend, "", $legend[$counter]);

        if ($rest_legend !== '') {
          $second_line_legend = Unicode::truncate($rest_legend, 22, TRUE, FALSE);
          if (strlen($rest_legend) > 22) {
            $second_line_legend .= '...';
          }

          $this->drawLegends($pdf, $crx - 66, $chartRecCY + 30 * $counter + $cy, $color, $first_line_legend, $value[$counter], true, false);

          $chartRecCY = $chartRecCY + 30;
          $this->drawLegends($pdf, $crx - 66, $chartRecCY + 30 * $counter + $cy, $color, $second_line_legend, $value[$counter], false, true);
        }
        else {
          $this->drawLegends($pdf, $crx - 66, $chartRecCY + 30 * $counter + $cy, $color, $first_line_legend, $value[$counter]);
        }

      }

      // $counter++;
    }

    $valuePerDegree = \Drupal::getContainer()
      ->get('flexinfo.calc.service')
      ->getPercentage(360, $totalCount) / 100;

    $startAngle = 0;
    foreach ($charts as $val) {
      $getChartValue = (int) $val->value;
      if ($getChartValue == 0) {
        continue;
      }
      $degrees  = ($getChartValue * $valuePerDegree);
      $percent  = round(100 / 360 * $degrees);
      $endAngle = $startAngle + $degrees;
      $color = Color::getColorArray($val->color);

      $cxChartPosition = $cx + 64;
      if ($this->drawWhiteCenterCircle) {
        $cxChartPosition += 68;
      }

      $cyChartPosition = $cy + 328;

      $pdf->setFillColor($color[0], $color[1], $color[2]);
      $pdf->Sector($cxChartPosition, $cyChartPosition, $circleRadius, $startAngle, $endAngle, $style = 'F', $cw = true, $o = 90);

      // add white circle
      $pdf->setFillColor(245, 245, 245);

      if ($this->drawWhiteCenterCircle) {
        $pdf->Sector($cxChartPosition, $cyChartPosition, 70, 0, 360, $style = 'F', $cw = true, $o = 90);
      }
      else {
        $pdf->Sector($cxChartPosition, $cyChartPosition, 0, 0, 360, $style = 'F', $cw = true, $o = 90);
      }

      $quad = (int)(($degrees / 2 + $startAngle) / 90);
      $ang  = ($degrees / 2 + $startAngle) % 90;
      if ($quad == 0) {
        $percentTextShowedOnPieChartYPostion =- 1 * cos(deg2rad (abs($ang))) * $percentTextShowedOnPieChart;
        $percentTextShowedOnPieChartXPostion =- 15 + sin(deg2rad ($ang)) * $percentTextShowedOnPieChart;
      }
      elseif ($quad == 1) {
        $percentTextShowedOnPieChartYPostion = sin(deg2rad($ang)) * $percentTextShowedOnPieChart;
        $percentTextShowedOnPieChartXPostion =- 15 + cos(deg2rad ($ang)) * $percentTextShowedOnPieChart;
      }
      elseif ($quad == 2) {
        $percentTextShowedOnPieChartYPostion = cos(deg2rad ($ang)) * $percentTextShowedOnPieChart;
        $percentTextShowedOnPieChartXPostion =- 1 * sin(deg2rad ($ang)) * $percentTextShowedOnPieChart;
      }
      elseif ($quad == 3) {
        $percentTextShowedOnPieChartYPostion =- 1 * sin(deg2rad ($ang)) * $percentTextShowedOnPieChart;
        $percentTextShowedOnPieChartXPostion =- 1 * cos(deg2rad ($ang)) * $percentTextShowedOnPieChart;
      }

      $pdf->SetTextColor(245, 245, 245);
      $pdf->SetXY($cxChartPosition + $percentTextShowedOnPieChartXPostion , $cyChartPosition + $percentTextShowedOnPieChartYPostion);
      if ($percent > 4) {
        $pdf->Write(9, $percent . "%");
      }
      $startAngle = $endAngle;
    }

    // doughnut center text
    if ($this->drawWhiteCenterCircle) {
      if ($cross_text && is_array($cross_text)) {
        krsort($cross_text);
        $cross_text = array_values($cross_text);

        foreach ($cross_text as $key => $value) {
          $pdf->SetTextColor(0, 0, 0);
          $pdf->SetFont('Arial', '', 20);
          $pdf->SetXY($cxChartPosition - 32 , $cyChartPosition - 28 + (28 * $key));
          $pdf->Write(30, $value);
        }
      }
    }

    return;
  }

}

