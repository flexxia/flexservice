<?php

namespace Drupal\htmlpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class SavePngController.
 */
class PrintPdfController extends ControllerBase {

  /**
   * Hello.
   *
   * @return markup
   *   Return Hello World.
   */
  public function standardPrintPdf1($section, $entity_id, $start_timestamp, $end_timestamp) {
    $build = [
      '#type' => 'markup',
      '#markup' => 'Print Pdf',
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    // $build = $this->downloadPDF();
    // $build = $this->displayPdf();

    return $build;
  }

  /**
   * Hello.
   *
   * @return markup
   *   Return Hello World.
   */
  public function standardPrintPdf($section, $entity_id, $start_timestamp, $end_timestamp) {
    $pdf_result = \Drupal::service('htmlpage.nodejsapp.puppeteer.print.pdf')
      ->executeNodejsScriptFromPhp($section, $entity_id, $start_timestamp, $end_timestamp);

    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center print-pdf-file-generate-wrapper">';
        $markup .= 'PDF File Generate Successful ';
        $markup .= '<br />';
        $markup .= $pdf_result;
      $markup .= '</div>';
    $markup .= '</div>';

    $build = [
      '#type' => 'markup',
      '#markup' => $markup,
      '#cache' => [
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => [
          'htmlpage/puppeteer-print-debug-pdf',
        ],
        'drupalSettings' => [
          'htmlpage_pdf' => [
            'jsonUrl' => 'htmlpage/' . $section . '/json/' .$entity_id . '/' . $start_timestamp . '/' . $end_timestamp,
          ],
        ],
      ],
    ];

    return $build;
  }

  /**
   * @todo debug function, 强迫浏览器下载PDF.
   *
   */
  public function downloadPDF() {
    $file_uri = 'public://' . 'gitsync/sample_print_pdf.pdf';
    $file_name = "sample_download.pdf";

    $headers = array(
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'attachment;filename="' . $file_name . '"',
    );

    return new BinaryFileResponse($file_uri, 200, $headers, TRUE);
  }

  /**
   * @todo debug function, Browser Display Pdf.
   */
  public function displayPdf() {
    $base_url = \Drupal::service('flexinfo.setting.service')
      ->getHttpsBaseUrl();
    $path = $base_url . '/sites/default/files/gitsync/sample_print_pdf.pdf';

    $response = new Response();
    $response->setContent(file_get_contents($path));
    $response->headers->set('Content-Type', 'application/pdf');
    return $response;
  }

}
