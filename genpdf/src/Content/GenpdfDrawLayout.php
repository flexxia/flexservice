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
class GenpdfDrawLayout {

  /*
   *
   */
  function setTopFontSize($pdf) {
    $pdf->SetFont('Arial', '', 16);
    $pdf->SetTextColor(245, 245, 245);
  }

  /*
   *
   */
  function setButtomNumFont($pdf) {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 12);
  }

  /**
   *
   */
  function setButtomTextFont($pdf) {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 14);
  }

  /**
   * Draw meeting title
   */
  function drawTitle($cx, $cy, $tileValue, $title, $pdf) {
    $pdf->SetXY(128, 16);
    $pdf->SetFont('Arial', '', 16);
    $pdf->SetTextColor(50, 178, 228);

    $ShowHtmlSnippet = new GenpdfDrawHTMLSnippet();
    $title = $ShowHtmlSnippet->htmlTextFilter($title);
    $pdf->Write(10, $title);

    $section_max_length = 40;

    if ($tileValue) {
      $pdf->SetFont('Arial', '', 14);
      $pdf->SetTextColor(13, 51, 77);

      $cxLineElement = $cx;
      for ($i = 0; $i < 4; $i++) {
        $cxline[$i] = $cxLineElement;
        $cxLineElement = $cxLineElement + 300;
      }

      $ShowHtmlSnippet = new GenpdfDrawHTMLSnippet();

      for ($i = 0; $i < count($tileValue); $i++) {

        $lines = floor($i / 4);
        $number_position = $i;
        if ($lines > 0) {
          $number_position = $number_position - (4 * $lines);
        }

        if (isset($tileValue[$i]['pdf_col_length'])) {
          $section_max_length = $tileValue[$i]['pdf_col_length'];
        }

        $tile_content_raw = $tileValue[$i]['value'] . " : " . $tileValue[$i]['value_one'];
        $tile_content = $ShowHtmlSnippet->htmlTextFilter($tile_content_raw);
        $tile_content_first_line = Unicode::truncate($tile_content, $section_max_length, $wordsafe = TRUE, $add_ellipsis = FALSE);
        $tile_content_second_line = str_replace($tile_content_first_line, "", $tile_content);

        $pdf->SetXY($cxline[$i % 4] + $number_position * 10, 32 * $lines + $cy);
        $pdf->Write(10, $tile_content_first_line);

        if ($tile_content_second_line != '') {
          $tile_content_second_line = Unicode::truncate($tile_content_second_line, $section_max_length, $wordsafe = TRUE, $add_ellipsis = FALSE);
          $pdf->SetXY($cxline[$i % 4] + $number_position * 10 + 6, 32 * ($lines + 0.6) + $cy);
          $pdf->Write(10, $tile_content_second_line);
        }
      }
    }
  }

  /**
   *
   */
  function drawChartFrame($cx, $cy, $topValue, $pdf, $frameHeight, $frameWidth, $textLengthPerLine = "76", $bottom = TRUE, $border_color_white = FALSE, $headerFillColorR = 0, $headerFillColorG = 157, $headerFillColorB = 223, $textColorR = 255, $textColorG = 255, $textColorB = 255) {
    $pdf->setFillColor($headerFillColorR, $headerFillColorG, $headerFillColorB);
    $pdf->SetFont('Arial', '', 16);

    $pdf->SetDrawColor(224, 224, 224);
    if ($border_color_white) {
      $pdf->SetDrawColor(255, 255, 255);
    }

    $pdf->Rect($cx, $cy, $frameWidth, $frameHeight, 'D', $border_style = null, array(
      200,
      180,
      200,
    ));

    if ($bottom) {
      $pdf->Rect($cx, $cy + $frameHeight - 64, ($frameWidth / 2), 64, 'D', $border_style = null, array(
        200,
        180,
        200,
      ));
      $pdf->Rect($cx + ($frameWidth / 2), $cy + $frameHeight - 64, ($frameWidth / 2), 64, 'D', $border_style = null, array(
        200,
        180,
        200,
      ));
    }

    // $topValue = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $topValue);

    $firstLineText = Unicode::truncate($topValue, $textLengthPerLine, $wordsafe = TRUE, $add_ellipsis = FALSE);
    $restText = str_replace($firstLineText, "", $topValue);
    $secondLineText = Unicode::truncate($restText, $textLengthPerLine, $wordsafe = TRUE, $add_ellipsis = TRUE);

    $firstLineText = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $firstLineText);
    $secondLineText = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $secondLineText);

    // replace special sign to standard one
    $secondLineText = str_replace("…", "...", $secondLineText);

    $pdf->SetTextColor($textColorR, $textColorG, $textColorB);
    $pdf->SetXY($cx, $cy);
    $pdf->Cell($frameWidth, 36, "    " . $firstLineText, 0, 1, 'L', 1);
    $pdf->SetXY($cx, $cy + 36);
    $pdf->Cell($frameWidth, 36, "    " . $secondLineText, 0, 1, 'L', 1);
    if ($secondLineText == "") {
      $pdf->SetXY($cx, $cy);
      $pdf->Cell($frameWidth, 36 + 36, "    " . $firstLineText, 0, 1, 'L', 1);
    }
  }

  /**
   *
   */
  function setBottomTextPosition($cx, $cy, $chartBottomText, $pdf, $bottomTextSpaceXPosition, $bottomTextSpaceYPosition, $spaceBetweenFrame, $frameWidthSize) {
    $leftFirstTextLength = $pdf->GetStringWidth($chartBottomText[0]);
    $pdf->SetXY($cx + ($frameWidthSize / 4) - $leftFirstTextLength / 2, $bottomTextSpaceYPosition + $cy - $spaceBetweenFrame);
    $this->setButtomNumFont($pdf);
    if (isset($chartBottomText[0])) {
      $pdf->Write(12, $chartBottomText[0]);
    }

    $leftSecondTextLength = $pdf->GetStringWidth($chartBottomText[1]);
    $pdf->SetXY($cx + ($frameWidthSize / 4) - $leftSecondTextLength / 2, $bottomTextSpaceYPosition + $cy);
    $this->setButtomTextFont($pdf);
    if (isset($chartBottomText[1])) {
      $pdf->Write(12, (iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $chartBottomText[1])));
    }

    $rightFirstTextLength = $pdf->GetStringWidth($chartBottomText[2]);
    $pdf->SetXY($cx + ($frameWidthSize / 2) + ($frameWidthSize / 4) - $rightFirstTextLength / 2, $bottomTextSpaceYPosition + $cy - $spaceBetweenFrame);
    $this->setButtomNumFont($pdf);
    if (isset($chartBottomText[2])) {
      $pdf->Write(12, $chartBottomText[2]);
    }

    if (isset($chartBottomText[3])) {
      $rightSecondTextLength = $pdf->GetStringWidth($chartBottomText[3]);
      $pdf->SetXY($cx + ($frameWidthSize / 2) + ($frameWidthSize / 4) - $rightSecondTextLength / 2, $bottomTextSpaceYPosition + $cy);
      $this->setButtomTextFont($pdf);
      $pdf->Write(12, (iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $chartBottomText[3])));
    }

  }

}
