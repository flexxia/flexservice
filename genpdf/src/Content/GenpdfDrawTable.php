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
class GenpdfDrawTable {
  function drawTable($cx, $cy, $tableHead, $tableBody, $pdf, $tableRowHeight, $tableWidth, $firstCellSpace = 328) {

    if (isset($tableHead)) {
      $tHeadLen = count($tableHead);
    }
    else {
      $tHeadLen = count($tableBody[0]);
    }

    $tBodyLen = count($tableBody);
    $cellWidth = floor(($tableWidth - $firstCellSpace) / $tHeadLen);
    $cellStartPositionX = $cx - 220;

    // assign thead
    $XelementPosition[0] = $cellStartPositionX;

    for ($i = 0; $i < $tHeadLen; $i++) {

      if ($i == 0) {
        $XelementPosition[0] = $cellStartPositionX + $firstCellSpace;
      }
      else {
        // added first cell's space
        $XelementPosition[$i] = $cellStartPositionX + $firstCellSpace + $cellWidth * $i + 200;
      }
      $pdf->SetLineWidth(2.5);

      // $pdf->Line($XelementPosition[$i] - 28 , $cy + 40, $XelementPosition[$i] - 28 , $cy + $tableRowHeight * ($tBodyLen + 1));
      // $pdf->Line($cx, $cy + $tableRowHeight, $cx + $tableWidth, $cy + $tableRowHeight );

      // $$tableHead[$i] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $$tableHead[$i]);

      if (isset($tableHead)) {
        $pdf->SetXY($XelementPosition[$i], $cy - 40);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Write(200, $tableHead[$i]);
      }
    }

    // tbody
    for ($i = 0; $i < $tBodyLen; $i++) {
      $pdf->SetLineWidth(2.5);
      $pdf->Line($cx, $cy+ ($i + 2) * $tableRowHeight, $cx + $firstCellSpace + $cellWidth * $tHeadLen, $cy+ ($i + 2) * $tableRowHeight);
      $nextLinePositionY = $tableRowHeight * $i;

      for ($j = 0; $j < count($tableBody[$i]); $j++) {
        $pdf->SetXY($XelementPosition[$j], $cy + $nextLinePositionY);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 16);

        // convert extra text into ellipsis
        $cell_content = Unicode::truncate($tableBody[$i][$j], 46, $wordsafe = TRUE, $add_ellipsis = TRUE);
        // replace special sign to standard one
        $cell_content = str_replace("…", "...", $cell_content);

        $cell_content = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $cell_content);
        $pdf->Write(200, $cell_content);
      }
    }
  }
}