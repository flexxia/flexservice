<?php

/**
 * @file
 */
namespace Drupal\genpdf\Generator;

use FPDF;
use Drupal\genpdf\Service\PDFDraw;
use Drupal\genpdf\Content\GenpdfDrawBarChart;
use Drupal\genpdf\Content\GenpdfDrawDoughnutChart;
use Drupal\genpdf\Content\GenpdfDrawHTMLSnippet;
use Drupal\genpdf\Content\GenpdfDrawLayout;
use Drupal\genpdf\Content\GenpdfDrawLineChart;
use Drupal\genpdf\Content\GenpdfDrawPieChart;
use Drupal\genpdf\Content\GenpdfDrawProgramTile;
use Drupal\genpdf\Content\GenpdfDrawTable;
use Drupal\genpdf\Content\GenpdfJsonGenerator;

use Drupal\genpdfstyle\Content\ChartBlock;
use Drupal\genpdfstyle\Content\CommentBlock;
use Drupal\genpdfstyle\Content\TableBlock;

/**
 * An example controller.
 * $GenpdfContentGenerator = new GenpdfContentGenerator();
 * $GenpdfContentGenerator->runGenPdf();
 */
class GenpdfContentGenerator {

  /**
   * Sample debug JSON.
   * $json_data = $GenpdfJsonGenerator->getDebugMeetingJsonFromFileUrl($entity_id);
   */
  function runGenPdfMeeting($entity_id = NULL) {
    $GenpdfJsonGenerator = new GenpdfJsonGenerator();
    $json_data = $GenpdfJsonGenerator->meetingJson($entity_id);

    $output = $this->runDrawPdfPage($json_data, $entity_id, $entity_type = 'node');

    return $output;
  }

  /**
   * Sample debug JSON.
   * $json_data = $GenpdfJsonGenerator->getDebugMeetingJsonFromFileUrl($entity_id);
   */
  function runGenPdfProgram($entity_id = NULL) {
    $GenpdfJsonGenerator = new GenpdfJsonGenerator();
    $json_data = $GenpdfJsonGenerator->programJson($entity_id);

    $output = $this->runDrawPdfPage($json_data, $entity_id, $entity_type = 'taxonomy_term');

    return $output;
  }

  /**
   *
   */
  function runDrawPdfPage($json_data, $entity_id = NULL, $entity_type = 'node') {
    $pdf_file_url = $this->getPdfName();

    $PdfPage = new PdfPage();
    $PdfPage->drawPdfPage($pdf_file_url, $json_data, $entity_id, $entity_type);

    $base_path_url = \Drupal::service('flexinfo.setting.service')
      ->getHttpsBaseUrl();

    $output = '';
    $output .= '<br />';
    $output .= '<a href = ';
      $output .= $base_path_url . '/' . $pdf_file_url;
    $output .= '>';
      $output .= 'Click to download file';
    $output .= '</a>';

    return $output;
  }

  /**
   *
   */
  function getPdfName($entity_id = NULL) {
    $GenpdfJsonGenerator = new GenpdfJsonGenerator();
    $output = $GenpdfJsonGenerator->getPdfName($entity_id);

    return $output;
  }

}

class PdfPage {

  /**
   *
   */
  public function drawPdfPage($pdf_file_url, $json, $entity_id, $entity_type = 'node') {
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);
    if (!$entity) {
      return;
    }

    // creat objects
    $PieChart      = new GenpdfDrawPieChart();
    $DoughnutChart = new GenpdfDrawDoughnutChart();
    $BarChart      = new GenpdfDrawBarChart();
    $LineChart     = new GenpdfDrawLineChart();

    $draw_the_layout     = new GenpdfDrawLayout();
    $show_html_snippet   = new GenpdfDrawHTMLSnippet();

    $ProgramTile = new GenpdfDrawProgramTile();
    $Table = new GenpdfDrawTable();

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    // $response = \Drupal::httpClient()->get($json_file_path, array('timeout' => 60000, 'headers' => array('Accept' => 'text/plain')));
    // $pdf_link = NULL;

    // $data = $response->getBody();

    // $json = json_decode($data, TRUE);

    if (isset($json['pdfjson']['meeting']['tileSection'])) {
      $tileValue = $json['pdfjson']['meeting']['tileSection'];
    }

    if (isset($json['pdfjson']['meeting']['programName'])) {
      $programName  = $json['pdfjson']['meeting']['programName'];
    }

    if (isset($json['pdfjson']['chartSection'])) {
      $chartSection = $json['pdfjson']['chartSection'];
    }

    if (isset($json['pdfjson']['commentSection'])) {
      $commentSection = $json['pdfjson']['commentSection'];
    }

    if (isset($json['pdfjson']['tableSection'])) {
      $tableSection = $json['pdfjson']['tableSection'];
    }

    if (isset($json['fixedSection'])) {
      $fixedSection = $json['fixedSection'];
    }

    if (isset($json['programTitle'])) {
      $programTitle = $json['programTitle'];
    }

    /**
     * draw PDF
     */
    $pdf = new PDFDraw('L', 'pt', array(
      1540,
      1500,
    ));

    $pdf->SetAutoPageBreak(FALSE);
    $pdf->SetMargins(3, 3, 3);
    $pdf->AddPage();

    // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

    /**
     * draw tile details and program tile
     */
    if ($entity_type == 'taxonomy_term') {
      $draw_the_layout->drawTitle(128, 46, NULL, $programTitle, $pdf);
      $ProgramTile->drawProgramTile(158, 72, $fixedSection, $pdf);
    }
    else {
      $draw_the_layout->drawTitle(128, 56, $tileValue, $programName, $pdf);
    }

    // draw chart section
    $this->drawPdfChart($chartSection['question'], $pdf);

    // draw table section
    $Table = new GenpdfDrawTable();
    $draw_the_layout  = new GenpdfDrawLayout();

    if (isset($tableSection['question'])) {
      $this->drawPdfTable($tableSection['question'], $pdf);
    }

    // draw comments section
    if (isset($commentSection['question'])) {
      $this->drawPdfComment($commentSection['question'], $pdf);
    }

    /**
     * output pdf file
     */
    $pdf->Output($pdf_file_url, 'F');
  }

  /**
   * draw chart pdf
   */
  public function drawPdfChart($chartSection, $pdf) {
    $ChartBlock = new ChartBlock();

    $frameHeight = $ChartBlock->frameHeight;
    $frameWidth = $ChartBlock->frameWidth;
    $frameFirstRowYPosition = $ChartBlock->getFrameFirstRowYPosition();

    $chartRightXPositioncol6 = $ChartBlock->getChartRightXPositioncol6();
    $chartLeftXPosition = $ChartBlock->getChartLeftXPositioncol6();
    $frameLeftXPosition = $ChartBlock->getFrameLeftXPositionCol6();
    $frameRightXPositionCol6 = $ChartBlock->getFrameRightXPositionCol6();
    $currentPageNum = $ChartBlock->getCurrentPageNum();
    $barChartFirstRowYPositionCol = $ChartBlock->getBarChartFirstRowYPositionCol();
    $percentTextShowedOnPieChart = $ChartBlock->getPercentTextShowedOnPieChart();
    $barChartHeight = $ChartBlock->getBarChartHeight();
    $bottomTextSpaceXPosition = $ChartBlock->getBottomTextSpaceXPosition();
    $bottomTextSpaceYPosition = $ChartBlock->getBottomTextSpaceYPosition();
    $frameFirstRowBottomYPosition = $ChartBlock->getFrameFirstRowBottomYPosition();
    $spaceBetweenFrameX = $ChartBlock->getSpaceBetweenFrameX();
    $spaceBetweenFrameY = $ChartBlock->getSpaceBetweenFrameY();
    $enableBottom = $ChartBlock->getEnableBottom();

    $frameHeaderFillColorR = $ChartBlock->getHeaderFillColorR();
    $frameHeaderFillColorG = $ChartBlock->getHeaderFillColorG();
    $frameHeaderFillColorB = $ChartBlock->getHeaderFillColorB();
    $frameTextColorR = $ChartBlock->getTextColorR();
    $frameTextColorG = $ChartBlock->getTextColorG();
    $frameTextColorB = $ChartBlock->getTextColorB();

    // creat objects
    $PieChart      = new GenpdfDrawPieChart();
    $DoughnutChart = new GenpdfDrawDoughnutChart();
    $BarChart      = new GenpdfDrawBarChart();
    $draw_the_layout = new GenpdfDrawLayout();
    $LineChart = new GenpdfDrawLineChart();

    for ($i = 0; $i < count($chartSection); $i++) {
      $frameLeftXPosition = $ChartBlock->getFrameLeftXPositionCol6();
      $chartLeftXPosition = $ChartBlock->getChartLeftXPositioncol6();
      $bottomTextYPosition = $ChartBlock->getBottomTextYPosition();
      $chartLeftYPosition = $ChartBlock->getChartLeftYPosition();
      $barChartXPositionCol6L = $ChartBlock->getBarChartXPositionCol6L();

      // For Column12, Go to next row when $currentPageNum is odd number
      if ($chartSection[$i]['block']['styleWidth'] == 'col-md-12' && $currentPageNum % 2 != 0 ) {
        $currentPageNum++;
      }
      if ($currentPageNum == 4) {
        $pdf->AddPage();
        $currentPageNum = 0;
        $frameFirstRowYPosition = $ChartBlock->getFrameFirstRowYPosition();
        $barChartFirstRowYPositionCol = $ChartBlock->getBarChartFirstRowYPositionCol();
        $frameFirstRowBottomYPosition = $ChartBlock->getFrameFirstRowBottomYPosition();
      }

      // second row
      if ($currentPageNum > 1) {
        $chartLeftYPosition = $chartLeftYPosition + $frameHeight + $spaceBetweenFrameY;
      }

      // right side
      if ($currentPageNum % 2 != 0) {
        $frameLeftXPosition = $frameRightXPositionCol6;
        $barChartXPositionCol6L = $barChartXPositionCol6L + $frameWidth + 14;
      }

      // second row left side
      if ($currentPageNum > 0 && $currentPageNum % 2 == 0) {
        $frameFirstRowYPosition = $frameFirstRowYPosition + $frameHeight + $spaceBetweenFrameY;
        $frameFirstRowBottomYPosition = $frameFirstRowBottomYPosition + $frameHeight + $spaceBetweenFrameY;
        $barChartFirstRowYPositionCol = $barChartFirstRowYPositionCol + $frameHeight + 48;
      }

      // ----------- draw frame -------------
      $draw_the_layout->setTopFontSize($pdf);
      $frameWidthSize = $frameWidth;
      $headerLengthPerLine = $ChartBlock->getHeaderLengthPerLine();

      if ($chartSection[$i]['block']['styleWidth'] == 'col-md-12') {
        $frameWidthSize = $frameWidth * 2;
        $headerLengthPerLine = $headerLengthPerLine * 2;
      }

      if ($chartSection[$i]['block']['class'] == "PrePost Pie Chart Column12") {
        if ($currentPageNum > 0 && $currentPageNum % 2 != 0) {
          $frameLeftXPosition = $ChartBlock->getFrameLeftXPositionCol6();
          $frameFirstRowYPosition = $frameFirstRowYPosition + $frameHeight + $spaceBetweenFrameY;
        }
      }

      // draw Chart Frame
      $draw_the_layout->drawChartFrame(
        $frameLeftXPosition,
        $frameFirstRowYPosition,
        $chartSection[$i]['block']['title'],
        $pdf,
        $frameHeight,
        $frameWidthSize,
        $headerLengthPerLine,
        $enableBottom,
        $border_color_white = FALSE,
        $frameHeaderFillColorR,
        $frameHeaderFillColorG,
        $frameHeaderFillColorB,
        $frameTextColorR,
        $frameTextColorG,
        $frameTextColorB
      );

      // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
      // Combine left and right to one array
      $chartDataAndStyle[$i]['class'][] = $chartSection[$i]['block']['class'];
      $chartDataAndStyle[$i]['data'][] = $chartSection[$i]['data'];

      // rightChartClass
      if ($chartSection[$i]['block']['rightChartClass'] != NULL) {
       $chartDataAndStyle[$i]['class'][] = $chartSection[$i]['block']['rightChartClass'];
       $chartDataAndStyle[$i]['data'][] = $chartSection[$i]['rightChartData'];
      }

      foreach ($chartDataAndStyle[$i]['class'] as $key => $chart_block_class) {
        // right side
        if ($currentPageNum % 2 != 0) {
          $chartLeftXPosition = $chartRightXPositioncol6;
          $barChartXPositionCol6L = $barChartXPositionCol6L + $frameWidth + 14;
        }

        // VOCABULARY is ChartType, field is field_queslibr_charttype
        if ($chart_block_class == "Pie Chart") {
          $PieChart->drawCircleChart(
            $percentTextShowedOnPieChart,
            $chartLeftXPosition,
            $chartLeftYPosition,
            $chartDataAndStyle[$i]['data'][$key],
            $pdf
          );
        }
        elseif ($chart_block_class == "PrePost Pie Chart Column12") {
          if ($currentPageNum % 2 != 0) {
            $chartLeftXPosition = $ChartBlock->getChartLeftXPositioncol6();
          }

          // add one more for column 12 section
          $currentPageNum++;

          // grouped bar chart
          // $databar = array(
          //   array(
          //     array(
          //       "value" => 11,
          //       "color" => "#2fa9e0",
          //       "title" => "1(12)",
          //       "label" => "pre",
          //       "legend" => "ASO&AB",
          //     ),
          //     array(
          //       "value" => 22,
          //       "color" => "#f7d417",
          //       "title" => "1(12)",
          //       "label" => "post",
          //       "legend" => "ASO&AB",
          //     ),
          //   ),
          //   array(
          //     array(
          //       "value" => 33,
          //       "color" => "#2fa9e0",
          //       "title" => "1(12)",
          //       "label" => "pre",
          //       "legend" => "ASO&AB",
          //     ),
          //     array(
          //       "value" => 44,
          //       "color" => "#f7d417",
          //       "title" => "1(12)",
          //       "label" => "post",
          //       "legend" => "ASO&AB",
          //     ),
          //   ),
          // );

          // Draw left chart
          $PieChart->drawCircleChart(
            $percentTextShowedOnPieChart,
            $chartLeftXPosition,
            $chartLeftYPosition,
            $chartDataAndStyle[$i]['data'][$key]['Pre'],
            $pdf
          );

          // Draw right chart
          $PieChart->drawCircleChart(
            $percentTextShowedOnPieChart,
            $chartLeftXPosition + ($chartRightXPositioncol6 - $chartLeftXPosition),
            $chartLeftYPosition,
            $chartDataAndStyle[$i]['data'][$key]['Post'],
            $pdf
          );
        }
        elseif ($chart_block_class == "Line Chart") {
          // $databar = array(
          //   array(
          //     "value" => 1,
          //     "color" => "#2fa9e0",
          //     "title" => "1(12)",
          //     "label" => "Dermatology",
          //     "legend" => "JAN",
          //   ),
          //   array(
          //     "value" => 22,
          //     "color" => "#f7d417",
          //     "title" => "1(12)",
          //     "legend" => "FEB",
          //   ),
          //   array(
          //     "value" => 33,
          //     "color" => "#2fa9e0",
          //     "title" => "1(12)",
          //     "legend" => "MAR",
          //   ),
          //   array(
          //     "value" => 64,
          //     "color" => "#f7d417",
          //     "title" => "1(12)",
          //     "legend" => "APR",
          //   ),
          //   array(
          //     "value" => 33,
          //     "color" => "#2fa9e0",
          //     "title" => "1(12)",
          //     "legend" => "MAY",
          //   ),
          //   array(
          //     "value" => 84,
          //     "color" => "#f7d417",
          //     "title" => "1(12)",
          //     "legend" => "JUN",
          //   ),
          //   array(
          //     "value" => 22,
          //     "color" => "#f7d417",
          //     "title" => "1(12)",
          //     "legend" => "JUL",
          //   ),
          //   array(
          //     "value" => 33,
          //     "color" => "#2fa9e0",
          //     "title" => "1(12)",
          //     "legend" => "AGU",
          //   ),
          //   array(
          //     "value" => 64,
          //     "color" => "#f7d417",
          //     "title" => "1(12)",
          //     "legend" => "SEP",
          //   ),
          //   array(
          //     "value" => 33,
          //     "color" => "#2fa9e0",
          //     "title" => "1(12)",
          //     "legend" => "OCT",
          //   ),
          //   array(
          //     "value" => 284,
          //     "color" => "#f7d417",
          //     "title" => "1(12)",
          //     "legend" => "NOV",
          //   ),
          //   array(
          //     "value" => 33,
          //     "color" => "#2fa9e0",
          //     "title" => "1(12)",
          //     "legend" => "DEC",
          //   )
          // );

          $LineChart->drawLineChartNew($chartLeftXPosition - 120, $chartLeftYPosition + 200, $chartDataAndStyle[$i]['data'][$key], $pdf, $LineChartHeight = 240, $LineChartWidth = 540, $spaceBetweenLine = 36);
        }
        elseif ($chart_block_class == "Stacked Bar Chart Multiple Horizontal") {
          // $databar = array(
          //   "labels" => array(
          //     "JAN",
          //     "FEB",
          //     "MAR",
          //     "APR",
          //     "MAY",
          //     "JUN",
          //     "JUL",
          //     "AUG",
          //     "SEP",
          //     "OCT",
          //     "NOV",
          //     "DEC"
          //   ),
          //   "datasets" => array(
          //     array(
          //       "backgroundColor" => "#00134b",
          //       "legend" => "Digital",
          //       "pointColor" => "#00134b",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         1,
          //         0,
          //         0,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     ),
          //     array(
          //       "backgroundColor" => "#c6c6c6",
          //       "legend" => "MSL Lead",
          //       "pointColor" => "#c6c6c6",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     ),
          //     array(
          //       "backgroundColor" => "#00aeef",
          //       "legend" => "Preceptorship",
          //       "pointColor" => "#00aeef",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         1,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     ),
          //     array(
          //       "backgroundColor" => "#7dba00",
          //       "legend" => "Stand Alone",
          //       "pointColor" => "#7dba00",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         1,
          //         0,
          //         0,
          //         1,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     ),
          //     array(
          //       "backgroundColor" => "#0093d0",
          //       "legend" => "Symposium",
          //       "pointColor" => "#0093d0",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         1,
          //         0,
          //         0,
          //         1,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     )
          //   )
          // );

          // $BarChart->drawHorizontalStackedBarChart($chartDataAndStyle[$i]['data'][$key], $pdf, $chartLeftXPosition - 120, $chartLeftYPosition + 300, 500, 250, $barWidth = 44);

          // stacked bar chart new
          $BarChart->drawHorizontalStackedBarChartNew($chartDataAndStyle[$i]['data'][$key], $pdf, $chartLeftXPosition - 100, $chartLeftYPosition + 240, 500, 250, $barWidth = 36);
        }
        elseif ($chart_block_class == "Stacked Bar Chart Multiple Vertical") {
          // $databar = array(
          //   "labels" => array(
          //     "JANunary",
          //     "FEBuerary",
          //     "MARchrary",
          //     "APR",
          //     "MAY",
          //     "JUN",
          //     "JUL",
          //     "AUG",
          //     "SEP",
          //     "OCT",
          //     "NOV",
          //     "DEC"
          //   ),
          //   "datasets" => array(
          //     array(
          //       "fillColor" => "#00134b",
          //       "pointColor" => "#00134b",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         1,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     ),
          //     array(
          //       "fillColor" => "#c6c6c6",
          //       "pointColor" => "#c6c6c6",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         1,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     ),
          //     array(
          //       "fillColor" => "#00aeef",
          //       "pointColor" => "#00aeef",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         0,
          //         1,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     ),
          //     array(
          //       "fillColor" => "#7dba00",
          //       "pointColor" => "#7dba00",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         1,
          //         0,
          //         0,
          //         1,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     ),
          //     array(
          //       "fillColor" => "#0093d0",
          //       "pointColor" => "#0093d0",
          //       "data" => [
          //         0,
          //         0,
          //         0,
          //         1,
          //         0,
          //         0,
          //         1,
          //         1,
          //         0,
          //         0,
          //         0,
          //         0
          //       ]
          //     )
          //   )
          // );

          // stacked bar chart
          $BarChart->drawVerticalStackedBarChartNew($chartDataAndStyle[$i]['data'][$key], $pdf, $chartLeftXPosition - 120, $chartLeftYPosition + 440, 500, 200, $barWidth = 44);
        }
        elseif ($chart_block_class == "Bar Chart") {
          // grouped bar chart
          // $databar = array(
          //   array(
          //     array(
          //       "value" => 11,
          //       "color" => "#2fa9e0",
          //       "title" => "1(12)",
          //       "label" => "pre",
          //       "legend" => "ASO&AB",
          //     ),
          //     array(
          //       "value" => 22,
          //       "color" => "#f7d417",
          //       "title" => "1(12)",
          //       "label" => "post",
          //       "legend" => "ASO&AB",
          //     ),
          //   ),
          //   array(
          //     array(
          //       "value" => 33,
          //       "color" => "#2fa9e0",
          //       "title" => "1(12)",
          //       "label" => "pre",
          //       "legend" => "ASO&AB",
          //     ),
          //     array(
          //       "value" => 44,
          //       "color" => "#f7d417",
          //       "title" => "1(12)",
          //       "label" => "post",
          //       "legend" => "ASO&AB",
          //     ),
          //   ),
          // );

          //  vertical group bar
          // $BarChart->drawVerticalGroupBarChart($databar, $pdf, $barChartXPositionCol6 = $chartLeftXPosition - 100, $barChartFirstRowYPositionCol = $chartLeftYPosition + $frameHeight, $barChartHeight = 240, $barChartWidth = 460, $spaceBetweenBar = 48);

          // horizontal group bar
          // $BarChart->drawHorizontalGroupBarChart($databar, $pdf, $barChartXPositionCol6 = $chartLeftXPosition - 100, $barChartFirstRowYPositionCol = $chartLeftYPosition + 200, $barChartHeight = 240, $barChartWidth = 460, $spaceBetweenBar = 48);

          $BarChart->drawVerticalSingleBarChart($chartDataAndStyle[$i]['data'][$key], $pdf, $chartLeftXPosition - 120, $chartLeftYPosition + 200, 200, 500, $spaceBetweenBar = 6);
        }
        elseif ($chart_block_class == "Single Bar Chart Horizontal") {
          // grouped bar chart
          // $databar = array(
          //   array(
          //     "value" => 11,
          //     "color" => "#00134b",
          //     "title" => "1(12)",
          //     "label" => "Dermatology",
          //     "legend" => "ASO&AB",
          //   ),
          //   array(
          //     "value" => 22,
          //     "color" => "#c6c6c6",
          //     "title" => "1(12)",
          //     "legend" => "Gas",
          //   ),
          //   array(
          //     "value" => 33,
          //     "color" => "#7dba00",
          //     "title" => "1(12)",
          //     "legend" => "HCV&AB",
          //   ),
          //   array(
          //     "value" => 44,
          //     "color" => "#0093d0",
          //     "title" => "1(12)",
          //     "legend" => "Health&AB",
          //   )
          // );

          $BarChart->drawHorizontalSingleBarChart($chartDataAndStyle[$i]['data'][$key], $pdf, $chartLeftXPosition - 120, $chartLeftYPosition + 200, 260, 500, $spaceBetweenBar = 6);
        }
        elseif ($chart_block_class == "Donut Chart") {
          $cross_text = NULL;
          if (isset($packageContent[$i]['middle']['middleMiddle']['middleMiddleMiddle']['chartOptions']['crossText'])) {
            $cross_text = $packageContent[$i]['middle']['middleMiddle']['middleMiddleMiddle']['chartOptions']['crossText'];
          }

          $DoughnutChart->drawCircleChart(
            $percentTextShowedOnPieChart,
            $chartLeftXPosition,
            $chartLeftYPosition,
            $chartDataAndStyle[$i]['data'][$key],
            $pdf,
            $circleRadius = 140,
            $cross_text
          );
        }

        //
        $currentPageNum++;
      }

      /* set the bottom information */
      $draw_the_layout->setBottomTextPosition(
        $frameLeftXPosition,
        $frameFirstRowBottomYPosition + $bottomTextYPosition,
        $chartSection[$i]['block']['bottom'],
        $pdf,
        $bottomTextSpaceXPosition,
        $bottomTextSpaceYPosition,
        $spaceBetweenFrameX,
        $frameWidthSize
      );
    }
  }

  /**
   * draw table pdf
   */
  public function drawPdfTable($tableSection, $pdf) {
    if (empty($tableSection)) {
      return;
    }

    $Table = new GenpdfDrawTable();
    $draw_the_layout = new GenpdfDrawLayout();
    $TableBlock = new TableBlock();

    $chartLeftXPosition = $TableBlock->getContentXPositionCol12();
    $contentYFirstRowPositionCol12 = $TableBlock->getContentYFirstRowPositionCol12();
    $frameLeftXPosition = $TableBlock->getFrameLeftXPosition();
    $frameSizeCol12 = $TableBlock->getFrameSizeCol12();
    $tableRowHeight = $TableBlock->getTableRowHeight();
    $tableWidth = $TableBlock->getTableWidth();
    $title_row_height = $TableBlock->getTitleRowHeight();
    $total_page_height = $TableBlock->getTotalPageHeight();

    $tableHeaderHeight = $TableBlock->getTableHeaderHeight();
    $table_block_space = 80;

    $pdf->AddPage();
    $page_left_space = $total_page_height;
    $startY = $contentYFirstRowPositionCol12;

    foreach ($tableSection as $key => $table_block) {
      $table_block_tbody_num = count($table_block['data']['tbody']);
      $table_block_need_height = ($table_block_tbody_num * $tableRowHeight) + $title_row_height + $tableHeaderHeight;

      if ($table_block_need_height < $page_left_space) {
        $page_left_space = $page_left_space - $table_block_need_height;

      }
      else {
        $pdf->AddPage();
        $page_left_space = $total_page_height;
        $startY = $contentYFirstRowPositionCol12;
      }

      $draw_the_layout->drawChartFrame(
        $frameLeftXPosition,
        $startY + $title_row_height,
        $table_block['block']['title'],
        $pdf,
        $title_row_height + 20,
        $frameSizeCol12,
        "160",
        FALSE,
        $border_color_white = TRUE,
        $TableBlock->getHeaderFillColorR(),
        $TableBlock->getHeaderFillColorG(),
        $TableBlock->getHeaderFillColorB(),
        $TableBlock->getTextColorR(),
        $TableBlock->getTextColorG(),
        $TableBlock->getTextColorB()
      );

      $Table->drawTable(
        $chartLeftXPosition,
        $startY + $title_row_height + $tableHeaderHeight,
        $table_block['data']['thead'],
        $table_block['data']['tbody'],
        $pdf,
        $tableRowHeight,
        $tableWidth
      );

      $startY = $startY + $table_block_need_height + $table_block_space;
    }

    return;
  }

  /**
   * Draw comment pdf.
   */
  public function drawPdfComment($commentSection, $pdf) {
    if (isset($commentSection)) {
      $draw_the_layout   = new GenpdfDrawLayout();
      $show_html_snippet = new GenpdfDrawHTMLSnippet();
      $comment_block     = new CommentBlock();

      $frameLeftXPosition = $comment_block->frameLeftXPosition;
      $contentYFirstRowPositionCol12 = $comment_block->contentYFirstRowPositionCol12;
      $contentXPositionCol12 = $comment_block->contentXPositionCol12;
      $frameSizeCol12 = $comment_block->frameSizeCol12;
      $comment_row_height = $comment_block->eachCommentRowHeight;
      $title_row_height = $comment_block->titleRowHeight;

      $frameHeaderFillColorR = $comment_block->headerFillColorR;
      $frameHeaderFillColorG = $comment_block->headerFillColorG;
      $frameHeaderFillColorB = $comment_block->headerFillColorB;
      $frameTextColorR = $comment_block->textColorR;
      $frameTextColorG = $comment_block->textColorG;
      $frameTextColorB = $comment_block->textColorB;

      // get from __get()
      $total_page_height = $comment_block->totalPageHeight;

      $left_content = NULL;

      //
      $comment_multi_line_num = 0;

      // 如果改成0，程序循环可能出现无限循环
      $count_comment_num = -1;
      $page_count_block = -1;

      while (1 > 0) {
        $pdf->AddPage();
        $page_left_space = $total_page_height + 300;
        $startY = $contentYFirstRowPositionCol12;
        $page_count_block = -1;

        if ($left_content) {
          if (($comment_multi_line_num + count($left_content)) > ($page_left_space / 26)) {
            $left_space_lines = $this->getPageLeftSpaceToCommentLines($page_left_space, $comment_row_height);
            if ($left_space_lines < 1) {
              $left_space_lines = 0;
            }
            $print_content = array_slice($left_content, 0, $left_space_lines);

            $left_space_lines = $left_space_lines - $comment_multi_line_num;
            if ($left_space_lines < 1) {
              $left_space_lines = 0;
            }
            $print_content = array_slice($left_content, 0, $left_space_lines);
            $left_content = array_slice($left_content, $left_space_lines);

            $print_content = $this->cleanTextContent($print_content);

            // Draw Comment Answer text.
            $show_html_snippet->drawHtmlSnippet(
              $contentXPositionCol12,
              $contentYFirstRowPositionCol12,
              $print_content,
              $pdf,
              $comment_row_height
            );
          }
          else {
            $print_content = $left_content;
            $print_content = $this->cleanTextContent($print_content);

            $show_html_snippet->drawHtmlSnippet(
              $contentXPositionCol12,
              $contentYFirstRowPositionCol12,
              $print_content,
              $pdf,
              $comment_row_height
            );

            $page_left_space = $page_left_space - count($print_content) * $comment_row_height - $title_row_height;
            $startY = $startY + $title_row_height * 2 + ($comment_multi_line_num + count($print_content)) * $comment_row_height + 10;
            $left_content = NULL;

            if ($page_left_space > 0) {
              while (3 > 2) {
                $count_comment_num++;
                while (!isset($commentSection[$count_comment_num]['data'])) {
                  $count_comment_num++;
                  if ($count_comment_num > count($commentSection) || $count_comment_num == count($commentSection)) {
                    break 3;
                  }
                }

                $each_comment_value = count($commentSection[$count_comment_num]['data']);
                $currentHeight = $each_comment_value * $comment_row_height + $title_row_height;

                $left_space_lines = $this->getPageLeftSpaceToCommentLines($page_left_space, $comment_row_height);
                if ($currentHeight > $page_left_space) {
                  if ($left_space_lines < 1) {
                    $left_space_lines = 0;
                  }
                  $print_content = array_slice($commentSection[$count_comment_num]['data'], 0, $left_space_lines);

                  $left_space_lines = $left_space_lines - $comment_multi_line_num;
                  if ($left_space_lines < 1) {
                    $left_space_lines = 0;
                  }
                  $print_content = array_slice($commentSection[$count_comment_num]['data'], 0, $left_space_lines);
                  $left_content = array_slice($commentSection[$count_comment_num]['data'], $left_space_lines);

                  $page_count_block = $page_count_block + 1;

                  // Draw Comment Question Title.
                  $draw_the_layout->drawChartFrame(
                    $frameLeftXPosition,
                    $startY + $title_row_height + 26 * $page_count_block,
                    $commentSection[$count_comment_num]['block']['title'],
                    $pdf,
                    $title_row_height + 20,
                    $frameSizeCol12,
                    "160",
                    FALSE,
                    $border_color_white = TRUE,
                    $frameHeaderFillColorR,
                    $frameHeaderFillColorG,
                    $frameHeaderFillColorB,
                    $frameTextColorR,
                    $frameTextColorG,
                    $frameTextColorB
                  );

                  $print_content = $this->cleanTextContent($print_content);
                  $show_html_snippet->drawHtmlSnippet(
                    $contentXPositionCol12,
                    $startY + $title_row_height + 26 * $page_count_block,
                    $print_content,
                    $pdf,
                    $comment_row_height
                  );

                  break 1;
                }
                else {
                  $print_content = $commentSection[$count_comment_num]['data'];

                  $page_left_space = $page_left_space - (count($print_content) + $comment_multi_line_num) * $comment_row_height - $title_row_height;
                  $left_content = NULL;

                  if ($page_left_space > 0) {
                    $page_count_block = $page_count_block + 1;

                    // Draw Comment Question Title.
                    $draw_the_layout->drawChartFrame(
                      $frameLeftXPosition,
                      $startY + $title_row_height + 26 * $page_count_block,
                      $commentSection[$count_comment_num]['block']['title'],
                      $pdf,
                      $title_row_height + 20,
                      $frameSizeCol12,
                      "160",
                      FALSE,
                      $border_color_white = TRUE,
                      $frameHeaderFillColorR,
                      $frameHeaderFillColorG,
                      $frameHeaderFillColorB,
                      $frameTextColorR,
                      $frameTextColorG,
                      $frameTextColorB
                    );

                    $print_content = $this->cleanTextContent($print_content);

                    // Draw Comment Answer text.
                    $show_html_snippet->drawHtmlSnippet(
                      $contentXPositionCol12,
                      $startY + $title_row_height + 26 * $page_count_block,
                      $print_content,
                      $pdf,
                      $comment_row_height
                    );
                    $startY = $startY + $title_row_height * 2 + ($comment_multi_line_num + count($print_content)) * $comment_row_height + 10;
                  }
                  else {
                    $count_comment_num = $count_comment_num - 1;
                    break 1;
                  }
                }
              }
            }
          }
        }
        else {
          while (2 > 0) {
            $count_comment_num++;
            while (!isset($commentSection[$count_comment_num]['data'])) {
              $count_comment_num++;

              if ($count_comment_num > count($commentSection) || $count_comment_num == count($commentSection)) {
                break 3;
              }
            }

            $each_comment_value = count($commentSection[$count_comment_num]['data']);
            $currentHeight = $each_comment_value * $comment_row_height + $title_row_height;
            $left_space_lines = $this->getPageLeftSpaceToCommentLines($page_left_space, $comment_row_height);

            if ($currentHeight > $page_left_space) {
              if ($left_space_lines < 1) {
                $left_space_lines = 0;
              }

              $page_left_space = $page_left_space - $title_row_height;

              $print_content = array_slice($commentSection[$count_comment_num]['data'], 0, $left_space_lines);
              if ($left_space_lines < 1) {
                $left_space_lines = 0;
              }
              $print_content = array_slice($commentSection[$count_comment_num]['data'], 0, $left_space_lines);

              $left_space_lines = $left_space_lines - $comment_multi_line_num;
              if ($left_space_lines < 1) {
                $left_space_lines = 0;
              }
              $print_content = array_slice($commentSection[$count_comment_num]['data'], 0, $left_space_lines);
              $left_content = array_slice($commentSection[$count_comment_num]['data'], $left_space_lines);

              $page_count_block = $page_count_block + 1;

              // Draw Comment Question Title.
              $draw_the_layout->drawChartFrame(
                $frameLeftXPosition,
                $startY + $title_row_height + 26 * $page_count_block,
                $commentSection[$count_comment_num]['block']['title'],
                $pdf,
                $title_row_height + 20,
                $frameSizeCol12,
                "160",
                FALSE,
                $border_color_white = TRUE,
                $frameHeaderFillColorR,
                $frameHeaderFillColorG,
                $frameHeaderFillColorB,
                $frameTextColorR,
                $frameTextColorG,
                $frameTextColorB
              );

              $print_content = $this->cleanTextContent($print_content);

              // Draw Comment Answer text.
              $show_html_snippet->drawHtmlSnippet(
                $contentXPositionCol12,
                $startY + $title_row_height + 26 * $page_count_block,
                $print_content,
                $pdf,
                $comment_row_height
              );

              break 1;
            }
            else {
              $print_content = $commentSection[$count_comment_num]['data'];
              $page_left_space = $page_left_space - $currentHeight - ($comment_multi_line_num * $comment_row_height);

              $left_content = NULL;

              if ($page_left_space > 0) {

                $page_count_block = $page_count_block + 1;

                // Draw Comment Question Title.
                $draw_the_layout->drawChartFrame(
                  $frameLeftXPosition,
                  $startY + $title_row_height + 26 * $page_count_block,
                  $commentSection[$count_comment_num]['block']['title'],
                  $pdf,
                  $title_row_height + 20,
                  $frameSizeCol12,
                  "160",
                  FALSE,
                  $border_color_white = TRUE,
                  $frameHeaderFillColorR,
                  $frameHeaderFillColorG,
                  $frameHeaderFillColorB,
                  $frameTextColorR,
                  $frameTextColorG,
                  $frameTextColorB
                );

                $print_content = $this->cleanTextContent($print_content);

                // Draw Comment Answer text.
                $show_html_snippet->drawHtmlSnippet(
                  $contentXPositionCol12,
                  $startY + $title_row_height + 26 * $page_count_block,
                  $print_content,
                  $pdf,
                  $comment_row_height
                );
                $startY = $startY + $title_row_height * 2 + ($comment_multi_line_num + count($print_content)) * $comment_row_height + 10;
              }
              else {
                $count_comment_num = $count_comment_num - 1;

                break 1;
              }
            }
          }
        }
      }
    }
  }

  /**
   * Clean PDF Text Content.
   */
  public function cleanTextContent($print_content) {
    $output = str_replace("\n", '', $print_content);

    return $output;
  }

  /**
   *
   */
  public function getPageLeftSpaceToCommentLines($page_left_space, $comment_row_height) {
    $output = $page_left_space / $comment_row_height;

    return $output;
  }

}
