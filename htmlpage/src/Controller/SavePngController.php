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
  public function standardImage1($section, $entity_id, $start_timestamp, $end_timestamp) {
    $build = [
      '#type' => 'markup',
      '#markup' => 'Save Png function',
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    $this->createImage();

    return $build;
  }

  /**
   * 先创建，然后Display Image
   *
   */
  public function standardImage($section, $entity_id, $start_timestamp, $end_timestamp) {
    global $base_url;

    $this->createImage();

    $filepath = $base_url . '/sites/default/files/images/generate_png/generate.png';
    $filename = "chart.png";

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
   * imagecreate 创建一个空白的画布并输出一个 PNG 格式的图片
   */
  public function createImage() {
    $img_file = imagecreate(500, 300);
    $background_color = imagecolorallocate($img_file, 240, 56, 125);
    $text_color = imagecolorallocate($img_file, 255, 255, 255);

    imagefilledrectangle($img_file, 0, 0, 500, 300, $background_color);
    imagestring($img_file, 5, 50, 60, "Hello Save Png", $text_color);

    $filepath = 'sites/default/files/images/generate_png/generate.png';

    $output = imagepng($img_file, $filepath);
    imagedestroy($img_file);
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

}
