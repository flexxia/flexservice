<?php

namespace Drupal\htmlpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class SavePngController.
 */
class SavePngController extends ControllerBase {

  /**
   * Hello.
   *
   * @return png
   *   Return Hello png.
   */
  public function standardImage0($section, $entity_id, $start_timestamp, $end_timestamp) {
    $build = [
      '#type' => 'markup',
      '#markup' => 'ccc',
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return $build;
  }

  /**
   *
   */
  public function createImage() {
    $img_file = imagecreate(500, 300);
    $background_color = imagecolorallocate($img_file, 240, 56, 125);
    $text_color = imagecolorallocate($img_file, 255, 255, 255);

    imagefilledrectangle($img_file, 0, 0, 500, 300, $background_color);
    imagestring($img_file, 5, 50, 60, "Hello TEXT", $text_color);

    $filepath = 'sites/default/files/images/generate_png/generate.png';

    $output = imagepng($img_file, $filepath);
    imagedestroy($img_file);
  }

  /**
   * standardPdf
   */
  public function standardImage($section, $entity_id, $start_timestamp, $end_timestamp) {
    global $base_url;
    $filepath = $base_url . '/sites/default/files/images/generate_png/generate.png';
    $filename = "chart.png";

    $this->createImage();

    $response = new Response();
    $response->headers->set('Content-Type', 'image/png');

    // $response->headers->set('Content-Length', filesize($filepath));
    // $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);
    // $response->headers->set('Content-Disposition', $disposition);

    $file_content = file_get_contents($filepath);
    $response->setContent($file_content);

    return $response;
  }

  /**
   * downloadImage
   */
  public function downloadImage($section, $entity_id, $start_timestamp, $end_timestamp) {
    $file_uri = 'public://' . 'images/ab.png';
    $file_name = "sample_download.png";

    $headers = array(
      'Content-Type' => 'image/png',
      'Content-Disposition' => 'attachment;filename="' . $file_name . '"',
    );

    return new BinaryFileResponse($file_uri, 200, $headers, TRUE);
  }

  /**
   * downloadPDF
   */
  public function downloadPDF($section, $entity_id, $start_timestamp, $end_timestamp) {
    $file_uri = 'public://' . 'pdf/2021_07_10_15_53_06.pdf';
    $file_name = "sample_download.pdf";

    $headers = array(
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'attachment;filename="' . $file_name . '"',
    );

    return new BinaryFileResponse($file_uri, 200, $headers, TRUE);
  }

  /**
   * displayPdf
   */
  public function displayPdf($section, $entity_id, $start_timestamp, $end_timestamp) {
    global $base_url;
    $path = $base_url . '/sites/default/files/pdf/2021_07_10_15_53_06.pdf';

    $response = new Response();
    $response->setContent(file_get_contents($path));
    $response->headers->set('Content-Type', 'application/pdf');
    return $response;
  }

}
