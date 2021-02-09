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
   *
   */
  function drawTitleMultipleSpeakers($cx, $cy, $tileValue, $title, $pdf) {
    // write title
    $pdf->SetXY(128, 16);
    $pdf->SetFont('Arial', '', 16);
    $pdf->SetTextColor(50, 178, 228);

    $ShowHtmlSnippet = new GenpdfDrawHTMLSnippet();
    $title = $ShowHtmlSnippet->htmlTextFilter($title);
    $pdf->Write(10, $title);

    if ($tileValue) {
      // write tile
      for($i = 0; $i < count($tileValue); $i++) {
        $getTitleValue = $tileValue[$i];
        $tile[$i] = $getTitleValue['value'] . " : " . $getTitleValue['value_one'];
      }

      $pdf->SetFont('Arial', '', 14);
      $pdf->SetTextColor(13, 51, 77);

      $cxLineElement = $cx;
      for ($i = 0; $i < 4; $i++) {
        $cxline[$i] = $cxLineElement;
        $cxLineElement = $cxLineElement + 300;
      }

      $ShowHtmlSnippet = new GenpdfDrawHTMLSnippet();

      for ($i = 0; $i < (count($tile) - 1); $i++) {
        $lines = floor($i / 4);
        $number_position = $i;
        if ($lines > 0) {
          $number_position = $number_position - 4;
        }

        $tile[$i] = $ShowHtmlSnippet->htmlTextFilter($tile[$i]);

        $firstLineText = Unicode::truncate($tile[$i], 40, $wordsafe = TRUE, $add_ellipsis = FALSE);
        $restText = str_replace($firstLineText, "", $tile[$i]);

        $pdf->SetXY($cxline[$i % 4] + $number_position * 10, 28 * $lines + $cy);
        $pdf->Write(10, $firstLineText);

        if ($restText != '') {
          $restText = Unicode::truncate($restText, 40, $wordsafe = TRUE, $add_ellipsis = FALSE);
          $pdf->SetXY($cxline[$i % 4] + $number_position * 10 + 6, 28 * ($lines + 0.5) + $cy);
          $pdf->Write(10, $restText);
        }
      }

      $tile[count($tile) - 1] = $ShowHtmlSnippet->htmlTextFilter($tile[count($tile) - 1]);
      $firstLineText = Unicode::truncate($tile[count($tile) - 1], 186, $wordsafe = TRUE, $add_ellipsis = FALSE);
      $restText = str_replace($firstLineText, "", $tile[count($tile) - 1]);

      $pdf->SetXY($cxline[0], 28 * 2 + $cy);
      $pdf->Write(10, $firstLineText);

      if ($restText != '') {
        $restText = Unicode::truncate($restText, 192, $wordsafe = TRUE, $add_ellipsis = FALSE);
        $pdf->SetXY($cxline[0], 28 * 3 + $cy);
        $pdf->Write(10, $restText);
      }


    }
  }

  /**
   *
   */
  function drawTitle($cx, $cy, $tileValue, $title, $pdf) {
    // write title
    $pdf->SetXY(128, 16);
    $pdf->SetFont('Arial', '', 16);
    $pdf->SetTextColor(50, 178, 228);

    $ShowHtmlSnippet = new GenpdfDrawHTMLSnippet();
    $title = $ShowHtmlSnippet->htmlTextFilter($title);
    $pdf->Write(10, $title);

    if ($tileValue) {
      // write tile
      for($i = 0; $i < count($tileValue); $i++) {
        $getTitleValue = $tileValue[$i];
        $tile[$i] = $getTitleValue['value'] . " : " . $getTitleValue['value_one'];
      }

      $pdf->SetFont('Arial', '', 14);
      $pdf->SetTextColor(13, 51, 77);

      $cxLineElement = $cx;
      for ($i = 0; $i < 4; $i++) {
        $cxline[$i] = $cxLineElement;
        $cxLineElement = $cxLineElement + 300;
      }

      $ShowHtmlSnippet = new GenpdfDrawHTMLSnippet();

      for ($i = 0; $i < count($tile) ; $i++) {
        $lines = floor($i / 4);
        $number_position = $i;
        if ($lines > 0) {
          $number_position = $number_position - 4;
        }

        $tile[$i] = $ShowHtmlSnippet->htmlTextFilter($tile[$i]);

        $firstLineText = Unicode::truncate($tile[$i], 40, $wordsafe = TRUE, $add_ellipsis = FALSE);
        $restText = str_replace($firstLineText, "", $tile[$i]);

        $pdf->SetXY($cxline[$i % 4] + $number_position * 10, 36 * $lines + $cy);
        $pdf->Write(10, $firstLineText);

        if ($restText != '') {
          $restText = Unicode::truncate($restText, 40, $wordsafe = TRUE, $add_ellipsis = FALSE);
          $pdf->SetXY($cxline[$i % 4] + $number_position * 10, 37 * $lines + $cy * 2);
          $pdf->Write(10, $restText);
        }

        // $pdf->SetXY($cxline[$i % 4] + $number_position * 10, 36 * $lines + $cy);
        // $pdf->Write(10, $tile[$i]);
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