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
class GenpdfDrawBarChart extends GenpdfBarAndLineTemplate {

  /**
   *
   */
  function drawVerticalSingleBarChart($chartData, $pdf, $barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartHeight, $barChartWidth, $spaceBetweenBar) {
    $this->drawChartCoordinateLines($barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartHeight, $barChartWidth, $pdf);

    $barPos   = 0;
    $barCount = count($chartData);
    $barWidth = 36;
    // $barWidth = ($barChartWidth - $barCount * $spaceBetweenBar) / $barCount;
    $spaceBetweenBar = ($barChartWidth - $barCount * $barWidth) / $barCount;

    if ($spaceBetweenBar < 2) {
      $spaceBetweenBar = 3;
      $barWidth = ($barChartWidth - $spaceBetweenBar * $barCount) / $barCount;
    }

    if ($barWidth > 40) {
      $barWidth = 40;
      $spaceBetweenBar = ($barChartWidth - $barWidth * $barCount) / $barCount;
    }

    // put bar chart value into array
    $barChartValue = array();
    if ($chartData) {
      foreach ($chartData as $val) {
        $barChartValue[] = $val['value'];
      }

      // get maxmum value of bar chart
      $maxValue = 1;
      if ($barChartValue) {
        $maxValue = max($barChartValue);
      }

      foreach ($chartData as $val) {
        // $barCount = count($val['value']);
        $barCount = 1;
        $pdf->SetTextColor(0, 0, 0);
        $getBarColors = $val['color'];
        $barColors = Color::getColorArray($getBarColors);
        $barData = $val['value'];
        $first_line_legend = Unicode::truncate($val['legend'], 10, TRUE, FALSE);
        $rest_legend = str_replace($first_line_legend, "", $val['legend']);
        if ($rest_legend !== '') {
          $second_line_legend = Unicode::truncate($rest_legend, 10, TRUE, FALSE);
          if (strlen($rest_legend) > 10) {
            $second_line_legend .= '...';
          }

          $pdf->SetXY($barChartXPositionCol6 + $barPos - $barWidth/2, $barChartFirstRowYPositionCol + $barChartHeight + 20);
          $pdf->SetFont('Arial', '', 12);
          $pdf->Cell(76, 36, $second_line_legend, 0, 0, 'C');
        }
        $pdf->SetXY($barChartXPositionCol6 + $barPos - $barWidth/2, $barChartFirstRowYPositionCol + $barChartHeight);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(76, 36, $first_line_legend, 0, 0, 'C');

        // calculate percentage
        $barheightPercent = round((int)$barData / $maxValue, 4);
        $barheight = $barChartHeight * $barheightPercent;

        $this->drawBar($pdf, $barColors, $barChartXPositionCol6 + $barPos, $barChartFirstRowYPositionCol + $barChartHeight - $barheight, $barWidth, $barheight, $barChartXPositionCol6 + $barPos - $barWidth/2,  $barChartFirstRowYPositionCol + $barChartHeight - $barheight- 28, NULL, NULL, $barData, 76, 36);

        $barPos = $barPos + $barWidth + $spaceBetweenBar;

        $barCount++;
      }
    }
  }
  /**
   *
   */
  function drawHorizontalSingleBarChart($chartData, $pdf, $barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartHeight, $barChartWidth, $spaceBetweenBar, $barWidth = 36) {
    // $this->drawChartCoordinateLines($barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartHeight, $barChartWidth, $pdf);

    $barPos   = 0;
    $barCount = count($chartData);
    $spaceBetweenBar = ($barChartHeight - $barWidth * $barCount) / $barCount;
    if ($spaceBetweenBar < 2) {
      $spaceBetweenBar = 3;
      $barWidth = ($barChartHeight - $spaceBetweenBar * $barCount) / $barCount;
    }

    if ($barWidth > 40) {
      $barWidth = 40;
      $spaceBetweenBar = ($barChartHeight - $barWidth * $barCount) / $barCount;
    }

    // put bar chart value into array
    $barChartValue = array();
    if ($chartData) {
      foreach ($chartData as $val) {
        $barChartValue[] = $val['value'];
      }
      // get maxmum value of bar chart
      $maxValue = 1;
      if ($barChartValue) {
        $maxValue = max($barChartValue);
      }

      foreach ($chartData as $val) {
        $barCount = 1;
        $pdf->SetTextColor(0, 0, 0);
        $getBarColors = $val['color'];
        $barColors = Color::getColorArray($getBarColors);
        $barData = $val['value'];

        $first_line_legend = Unicode::truncate($val['legend'], 10, TRUE, FALSE);
        $rest_legend = str_replace($first_line_legend, "", $val['legend']);
        if ($rest_legend !== '') {
          $second_line_legend = Unicode::truncate($rest_legend, 10, TRUE, FALSE);
          if (strlen($rest_legend) > 10) {
            $second_line_legend .= '...';
          }
          $pdf->SetXY($barChartXPositionCol6 - 56, $barChartFirstRowYPositionCol + $barPos + 10);
          $pdf->SetFont('Arial', '', 10);
          $pdf->Cell(56, $barWidth, $second_line_legend, 0, 0, 'C');
        }

        $pdf->SetXY($barChartXPositionCol6 - 56, $barChartFirstRowYPositionCol + $barPos);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(56, $barWidth, $first_line_legend, 0, 0, 'C');

        // calculate percentage
        $barLengthPercent = round((int)$barData / $maxValue, 4);
        $barLength = $barChartWidth * $barLengthPercent;

        $this->drawBar($pdf, $barColors, $barChartXPositionCol6, $barChartFirstRowYPositionCol + $barPos, $barLength, $barWidth, $barChartXPositionCol6 + $barLength + 8,  $barChartFirstRowYPositionCol + $barPos, NULL, NULL, $barData, 10, 36);

        $barPos = $barPos + $barWidth + $spaceBetweenBar;

        $barCount++;
      }
    }
  }

  /**
   *
   */
  function drawVerticalGroupBarChart($chartDataSum, $pdf, $barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartHeight, $barChartWidth, $spaceBetweenBar) {
    $pdf->SetDrawColor(107, 107, 107);
    $pdf->Line($barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartXPositionCol6, $barChartFirstRowYPositionCol - $barChartHeight);
    $pdf->Line($barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartXPositionCol6 + $barChartWidth, $barChartFirstRowYPositionCol);

    $pdf->SetFont('Arial' , 'I',12);

    $barPosistion   = 0;
    $bottomPostion = 0;

    $groupbarSum = count($chartDataSum);

    $groupbarWidth = ($barChartWidth - $groupbarSum * $spaceBetweenBar) / $groupbarSum;

    // put bar chart value into array
    $maxValue = $this->getMaxValue($chartDataSum);

    $barWidth = 18;

    foreach ($chartDataSum as $key => $chartData) {

      $barCount = count($chartData);
      $spaceBetweenBar = ($barChartWidth - $barWidth * $barCount * $groupbarSum) / $groupbarSum;

      if ($spaceBetweenBar > 16) {
        $barWidth = 22;
        $spaceBetweenBar = 16;
      }

      if ($spaceBetweenBar < 1) {
        $spaceBetweenBar = 3;
        $barWidth = ($barChartWidth - $spaceBetweenBar * $barCount * $groupbarSum) / $groupbarSum;
      }

      foreach ($chartData as $val) {
        $barColors = Color::getColorArray($val['color']);
        $pdf->SetTextColor(0, 0, 0);

        // calculate percentage
        $barheightPercent = round((int)$val['value'] / $maxValue, 4);
        $barheight = $barChartHeight * $barheightPercent;
        $legend = $val['legend'];

        $this->drawBar($pdf, $barColors, $barChartXPositionCol6 + $barPosistion, $barChartFirstRowYPositionCol - $barheight, $barWidth, $barheight, $barChartXPositionCol6 + $barPosistion - 6,  $barChartFirstRowYPositionCol - $barheight - 18, null, null,$val['value']);

        $barPosistion = $barPosistion + $barWidth;
      }

        $first_line_legend = Unicode::truncate($legend, 5, FALSE, FALSE);
        $pdf->SetXY($barChartXPositionCol6 + $bottomPostion, $barChartFirstRowYPositionCol + 12);
        $pdf->Cell(36, 10, $first_line_legend, 0, 0, 'C');
        $rest_legend = str_replace($first_line_legend, "", $legend);

        if ($rest_legend != "") {
          $second_line_legend = Unicode::truncate($rest_legend, 5, FALSE, FALSE);
          $pdf->SetXY($barChartXPositionCol6 + $bottomPostion, $barChartFirstRowYPositionCol + 20);

          $pdf->Cell(36, 10, $second_line_legend, 0, 0, 'C');
        }

      $barPosistion = $barPosistion + $spaceBetweenBar;
      $bottomPostion = $barPosistion;

    }
  }

  /**
   *
   */
  function getMaxValue($chartDataSum) {
    $barChartValue = array();
    foreach ($chartDataSum as $key => $chartData) {
      if ($chartData) {
        foreach ($chartData as $val) {
          $barChartValue[] = $val['value'];
        }
      }
    }

    // get maxmum value of bar chart
    $maxValue = 1;
    if ($barChartValue) {
      $maxValue = max($barChartValue);
    }
    return $maxValue;
  }

  /**
   *
   */
  function drawHorizontalGroupBarChart(array $chartDataSum = array(), $pdf, $barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartHeight, $barChartWidth, $spaceBetweenBar) {
    $pdf->SetDrawColor(107, 107, 107);

    $this->drawChartCoordinateLines($barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartHeight, $barChartWidth, $pdf);

    $groupbarSum = count($chartDataSum);

    if ($groupbarSum < 3) {
      $barChartFirstRowYPositionCol = $barChartFirstRowYPositionCol + 22 * (5 - $groupbarSum);
    }

    // put bar chart value into array
    $maxValue = $this->getMaxValue($chartDataSum);
    $barheight = 18;

    $pdf->SetFont('Arial' , 'I',12);

    foreach ($chartDataSum as $key => $chartData) {
      $barCount = count($chartData);
      $groupedBarHeight = $barheight * $barCount;
      $barPosistion   = $barheight;
      $spaceBetweenBar = ($barChartHeight - $barheight * $barCount * $groupbarSum) / $groupbarSum;

      if ($spaceBetweenBar > 16) {
        $barheight = 22;
        $spaceBetweenBar = 16;
      }

      foreach ($chartData as $val) {
        $barColors = Color::getColorArray($val['color']);
        $pdf->SetTextColor(0, 0, 0);

        // calculate percentage
        $barWidthPercent = round((int)$val['value'] / $maxValue, 4);
        $barWidth = $barChartWidth * $barWidthPercent;

        $legend = $val['legend'];
        $label = $val['label'];

        $this->drawBar($pdf, $barColors, $barChartXPositionCol6, $barChartFirstRowYPositionCol + $barPosistion, $barWidth, $barheight, $barChartXPositionCol6 + $barWidth + 4,  $barChartFirstRowYPositionCol + $barPosistion - 8, $val['label'], ':' ,$val['value']);

        $barPosistion = $barPosistion + $barheight;
      }

      $first_line_legend = Unicode::truncate($legend, 5, FALSE, FALSE);
      $pdf->SetXY($barChartXPositionCol6 - 46, $barChartFirstRowYPositionCol + $barheight * ($barCount - 1) + 4);
      $pdf->Cell(36, 36, $first_line_legend, 0, 0, 'C');

      $rest_legend = str_replace($first_line_legend, "", $legend);

      if ($rest_legend != "") {
        $second_line_legend = Unicode::truncate($rest_legend, 5, FALSE, FALSE);
        $pdf->SetXY($barChartXPositionCol6 - 46, $barChartFirstRowYPositionCol + $barheight * $barCount + 4);
        $pdf->Cell(36, 26, $second_line_legend, 0, 0, 'C');
      }

      $barChartFirstRowYPositionCol = $barChartFirstRowYPositionCol + $groupedBarHeight + $spaceBetweenBar;
    }
  }

  /**
   *
   */
  function drawHorizontalStackedBarChartNew(array $chartDataSum = array(), $pdf, $barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartLength = 400, $barChartHeight = 200, $barWidth = 44) {
    // $this->drawChartCoordinateLines($barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartHeight, $barChartLength, $pdf);

    $cx = $barChartXPositionCol6;
    $cy = $barChartFirstRowYPositionCol;
    $legends = NULL;

    // calculate space between bar
    $barCount = count($chartDataSum['labels']);

    $spaceBetweenBar = ($barChartHeight - $barWidth * $barCount) / $barCount;
    if ($spaceBetweenBar < 1) {
      $spaceBetweenBar = 3;
      $barWidth = ($barChartHeight - $spaceBetweenBar * $barCount) / $barCount;
    }

    if ($barCount < 3) {
      $barWidth = 44;
      $spaceBetweenBar = ($barChartHeight - $barWidth * $barCount) / $barCount;
    }

    if ($barWidth > 40) {
      $barWidth = 40;
      $spaceBetweenBar = ($barChartHeight - $barWidth * $barCount) / $barCount;
    }

    $yAxisLegends = $chartDataSum['labels'];

    $counter = 0;
    $legend_value = array();

    for ($i = 0; $i < count($yAxisLegends); $i++) {
      $barChartValue = array();
      $barPos = 0;
      foreach ($chartDataSum['datasets'] as $chartDataSetNum => $chartData) {
        if ($chartData['legend']) {
          $legends[] = $chartData['legend'];
        }

        for ($j = 0; $j < count($chartData['data']); $j++) {
          if ($chartDataSetNum === 0) {
            $barChartValue[$j] = $chartData['data'][$j];
          }
          else {
            $barChartValue[$j] += $chartData['data'][$j];
          }

          if ($j == 0) {
            $legend_value[$chartDataSetNum] = 0;
          }
          $legend_value[$chartDataSetNum] = $legend_value[$chartDataSetNum] + $chartData['data'][$j];
        }

        if (!isset($chartData['data'][$i])) {
          $chartData['data'][$i] = 0;
        }

        if (!isset($legend_value[$chartDataSetNum])) {
          $legend_value[$chartDataSetNum] = 0;
        }

        $legend_value[$chartDataSetNum] += $chartData['data'][$i];

        if ($i == 0) {
          $legendColors[$counter] = Color::getColorArray($chartData['fillColor']);
          $counter++;
        }
      }

      $legend_first_line_length = 10;
      $first_line_legend = Unicode::truncate($yAxisLegends[$i], $legend_first_line_length, TRUE, FALSE);
      $rest_legend = str_replace($first_line_legend, "", $yAxisLegends[$i]);
      $pdf->SetFont('Arial', 'I', 12);
      $pdf->SetTextColor(0, 0, 0);
      if ($rest_legend !== '') {
        $second_line_legend = Unicode::truncate($rest_legend, $legend_first_line_length, TRUE, FALSE);
        if (strlen($rest_legend) > 10) {
          $second_line_legend .= '...';
        }

        $pdf->SetXY($barChartXPositionCol6 - 36, $barChartFirstRowYPositionCol + 20);
        $pdf->Cell(36, 20, $second_line_legend, 0, 0, 'R');
      }
      $pdf->SetXY($barChartXPositionCol6 - 36, $barChartFirstRowYPositionCol);
      $pdf->Cell(36, 20, $first_line_legend, 0, 0, 'R');

      foreach ($chartDataSum['datasets'] as $chartDataSetNum => $chartData) {
        // calculate percentage
        $barLengthPercent = round((int)$chartData['data'][$i] / max($barChartValue), 4);
        $barLength = $barChartLength * $barLengthPercent;
        $barLength = round($barLength, 2);

        $barColors = Color::getColorArray($chartData['fillColor']);

        $this->drawBar($pdf, $barColors, $barChartXPositionCol6 + $barPos, $barChartFirstRowYPositionCol, $barLength, $barWidth, $barChartXPositionCol6 + $barPos,  $barChartFirstRowYPositionCol, NULL, NULL, NULL, 36, 36, [255, 255, 255]);

        if ($barLengthPercent * 100 > 5) {
          $this->drawBar($pdf, $barColors, $barChartXPositionCol6 + $barPos, $barChartFirstRowYPositionCol, $barLength, $barWidth, $barChartXPositionCol6 + $barPos,  $barChartFirstRowYPositionCol, NULL, NULL, $chartData['data'][$i], 36, 46, [255, 255, 255]);
        }
        $barPos = $barPos + $barLength;
      }

      $barChartFirstRowYPositionCol = $barChartFirstRowYPositionCol + $spaceBetweenBar + $barWidth;
    }

    $legendLenth = 0;
    $countLengedsPositionX = 0;
    for($counter = 0; $counter < count($chartDataSum['datasets']); $counter++) {
      if ($legendLenth * 6 + 70 * $countLengedsPositionX > 350) {
        $cy = $cy + 30;
        $legendLenth = 0;
        $countLengedsPositionX = 0;
      }
      if (!$legends[$counter]) {
        $legends[$counter] = $counter;
      }
      $this->drawLegends($pdf, $cx + $legendLenth * 6 + 70 * $countLengedsPositionX, $cy - 60, $legendColors[$counter], $legends[$counter], $legend_value[$counter]);
      $legendLenth = strlen($legends[$counter]) + $legendLenth;
      $countLengedsPositionX++;
    }
  }

  /**
   * $BarChart->drawVerticalStackedBarChartNew($chartDataAndStyle[$i]['data'][$key], $pdf, $chartLeftXPosition - 120, $chartLeftYPosition + 440, 500, 200, $barWidth = 44);
   */
  function drawVerticalStackedBarChartNew(array $chartDataSum = array(), $pdf, $barChartXPositionCol6, $barChartFirstRowYPositionCol, $barChartLength = 400, $barChartHeight = 200, $barWidth = 44) {

    $cx = $barChartXPositionCol6;
    $cy = $barChartFirstRowYPositionCol;

    // calculate space between bar
    $barCount = count($chartDataSum['labels']);

    $spaceBetweenBar = ($barChartLength - $barWidth * $barCount) / $barCount;
    if ($spaceBetweenBar < 1) {
      $spaceBetweenBar = 3;
      $barWidth = ($barChartLength - $spaceBetweenBar * $barCount) / $barCount;
    }

    if ($barCount < 3) {
      $barWidth = 44;
      $spaceBetweenBar = ($barChartLength - $barWidth * $barCount) / $barCount;
    }

    if ($barWidth > 40) {
      $barWidth = 40;
      $spaceBetweenBar = ($barChartLength - $barWidth * $barCount) / $barCount;
    }

    $xAxisLegends = $chartDataSum['labels'];

    $counter = 0;
    $legend_value = array();

    for ($i = 0; $i < count($xAxisLegends); $i++) {
      $barChartValue = array();
      $barPos = 0;
      foreach ($chartDataSum['datasets'] as $chartDataSetNum => $chartData) {
        if ($chartData['legend']) {
          $legends[] = $chartData['legend'];
        }

        for ($j = 0; $j < count($chartData['data']); $j++) {
          if ($chartDataSetNum === 0) {
            $barChartValue[$j] = $chartData['data'][$j];
          }
          else {
            $barChartValue[$j] += $chartData['data'][$j];
          }

          if ($j == 0) {
            $legend_value[$chartDataSetNum] = 0;
          }
          $legend_value[$chartDataSetNum] = $legend_value[$chartDataSetNum] + $chartData['data'][$j];
        }

        if (!isset($chartData['data'][$i])) {
          $chartData['data'][$i] = 0;
        }

        if (!isset($legend_value[$chartDataSetNum])) {
          $legend_value[$chartDataSetNum] = 0;
        }

        $legend_value[$chartDataSetNum] += $chartData['data'][$i];

        if ($i == 0) {
          $legendColors[$counter] = Color::getColorArray($chartData['fillColor']);
          $counter++;
        }
      }

      $legend_first_line_length = 10;

      $first_line_legend = Unicode::truncate($xAxisLegends[$i], $legend_first_line_length, TRUE, FALSE);
      $rest_legend = str_replace($first_line_legend, "", $xAxisLegends[$i]);
      $pdf->SetFont('Arial', 'I', 12);
      $pdf->SetTextColor(0, 0, 0);
      if ($rest_legend !== '') {
        $second_line_legend = Unicode::truncate($rest_legend, $legend_first_line_length, TRUE, FALSE);
        if (strlen($rest_legend) > 10) {
          $second_line_legend .= '...';
        }

        $pdf->SetXY($barChartXPositionCol6, $barChartFirstRowYPositionCol + 20);
        $pdf->Cell(36, 20, $second_line_legend, 0, 0, 'R');
      }
      $pdf->SetXY($barChartXPositionCol6, $barChartFirstRowYPositionCol);
      $pdf->Cell(36, 20, $first_line_legend, 0, 0, 'R');

      foreach ($chartDataSum['datasets'] as $chartDataSetNum => $chartData) {
        // calculate percentage
        $barLengthPercent = round((int)$chartData['data'][$i] / max($barChartValue), 4);
        $barLength = $barChartHeight * $barLengthPercent;
        $barLength = round($barLength, 2);

        $barColors = Color::getColorArray($chartData['fillColor']);
        $barPos = $barPos + $barLength;


        $this->drawBar($pdf, $barColors, $barChartXPositionCol6 , $barChartFirstRowYPositionCol - $barPos, $barWidth, $barLength, $barChartXPositionCol6,  $barChartFirstRowYPositionCol - $barPos, NULL, NULL, NULL, 36, 36, [255, 255, 255]);

        if ($barLengthPercent * 100 > 5) {
          $this->drawBar($pdf, $barColors, $barChartXPositionCol6, $barChartFirstRowYPositionCol - $barPos, $barWidth, $barLength, $barChartXPositionCol6,  $barChartFirstRowYPositionCol - $barPos, NULL, NULL, $chartData['data'][$i], 36, 46, [255, 255, 255]);
        }

      }

      $barChartXPositionCol6 = $barChartXPositionCol6 + $spaceBetweenBar + $barWidth;
    }

    $legendLenth = 0;
    $countLengedsPositionX = 0;
    for($counter = 0; $counter < count($chartDataSum['datasets']); $counter++) {
      if ($legendLenth * 6 + 70 * $countLengedsPositionX > 350) {
        $cy = $cy + 30;
        $legendLenth = 0;
        $countLengedsPositionX = 0;
      }
      if (!$legends[$counter]) {
        $legends[$counter] = $counter;
      }
      $this->drawLegends($pdf, $cx + $legendLenth * 6 + 70 * $countLengedsPositionX, $cy - 260, $legendColors[$counter], $legends[$counter], $legend_value[$counter]);
      $legendLenth = strlen($legends[$counter]) + $legendLenth;
      $countLengedsPositionX++;
    }

  }

}
