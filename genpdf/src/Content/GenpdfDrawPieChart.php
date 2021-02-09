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
 *  draw pie charts
 */
class GenpdfDrawPieChart extends GenpdfCircleChartTemplate {

  /**
   *
   */
  function __construct() {
    $this->drawWhiteCenterCircle = FALSE;
  }

}