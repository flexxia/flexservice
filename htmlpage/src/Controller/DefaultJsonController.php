<?php

namespace Drupal\htmlpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Return Htmlpage Json.
 */
class DefaultJsonController extends ControllerBase {

  /**
   * Standard Page Json.
   *
   * @return array
   *   Json Array.
   */
  public function standardHtmlJson($section, $entity_id, $start_timestamp, $end_timestamp) {
    $output = [];
    if ($section == 'meeting') {
      $output = \Drupal::service('htmlpage.content.object')
        ->meetingPageContent($entity_id, 'from-standardJson')['json_content'];
    }
    elseif ($section == 'sample' ||$section == 'samplepage' || $section == 'samplechart') {
      // Sample Page.
      $output = \Drupal::service('htmlpage.content.samplepage')
        ->samplePageContent()['json_content'];
    }

    return new JsonResponse($output);
  }

}
