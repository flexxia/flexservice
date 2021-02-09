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
class GenpdfDrawHTMLSnippet {

  /**
   * added a parameter commentRowHeight
   */
  function drawHtmlSnippet($cx, $cy, $snippetContent, $pdf, $commentRowHeight, $frameSize = 600) {
    $count_lines = 0;

    for ($i = 0; $i < count($snippetContent); $i++) {
      //changed Y postion
      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetFont('Arial', '', 16);

      $comments_text_filter = $this->htmlTextFilter($snippetContent[$i]);
      $string_line = $comments_text_filter;

      $pdf->SetXY($cx + 20, $cy + $commentRowHeight * $count_lines);

      // replace special sign to standard one
      $string_line = str_replace("…", "...", $string_line);

      // added count lines and check if it is over 26 lines
      $count_lines ++;
      if ($count_lines > 26) {
        $pdf->AddPage();
        $count_lines = 0;
        $pdf->SetXY($cx + 20, $cy - 24);
      }

      $pdf->Write(200, $string_line);
    }
  }

  /**
   *
   */
  function htmlTextFilter($comments_text = NULL) {
    $output = $comments_text;

    $output = str_replace("↑", "^", $output);
    $output = str_replace("\n", '', $output);

    // $output = utf8_encode($output);
    // $output = mb_convert_encoding($comments_text, "UTF-8");
    // $output = html_entity_decode($comments_text);
    // $output = htmlentities($comments_text);
    // $output = stripslashes($comments_text);

    // setlocale(LC_CTYPE, 'en_US');

    // $output = iconv('UTF-8', 'ASCII//TRANSLIT', $output);
    // $output = iconv('UTF-8', 'ISO-8859-1', $output);
    // $output = iconv('UTF-8', 'ISO-8859-5', $output);


    // $output = iconv('UTF-8', 'windows-1252', $output);
    $output = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $output);

    return $output;
  }

}
