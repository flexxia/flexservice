<?php

/**
 * @file
 * Contains \Drupal\genpdf\Controller\GenpdfImagesController.
 */

namespace Drupal\genpdf\Controller;

use Drupal\Core\Controller\ControllerBase;
use FPDF;

/**
 * An example controller.
 */
class GenpdfImagesController extends ControllerBase {

  /**
   *
   */
  public function pdfFromImages() {
    $image_path = drupal_get_path( 'module', 'genpdf') . '/images/';

    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->SetFont('Arial', '', 16);
    $pdf->SetTextColor(50, 178, 228);
    $pdf->Cell(0, 10, 'Hello World');

    // 还可以加链接
    $pdf->Image($image_path . '001.png', 20, 20, 120, 120, 'PNG', 'www.google.com');
    $pdf->Image($image_path . '002.png', 20, 160, 120, 120);

    $pdf->AddPage();
    $pdf->Image($image_path . '003.png', 20, 20, 120, 120);

    $pdf->Output();


    $markup = '';
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center">';
        $markup .= 'PDF File Generate Successful22';
        $markup .= '<br />';
        $markup .= $image_path;
      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

}
