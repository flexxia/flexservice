<?php

/**
 * @file
 */

namespace Drupal\genpdf\Content;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Utility\Unicode;
use Drupal\genpdf\Content\GenpdfCircleChartTemplate;

use FPDF;
use Drupal\genpdf\Service\PDFDraw;

/**
 *  draw Doughnut charts
 */
class GenpdfDrawDoughnutChart extends GenpdfCircleChartTemplate {

  /**
   *
   */
  function __construct() {
    $this->drawWhiteCenterCircle = TRUE;
  }

}