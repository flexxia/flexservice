<?php

namespace Drupal\htmlpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultJsonController.
 */
class DefaultJsonController extends ControllerBase {

  /**
   * @return array
   *   Json Array.
   */
  public function standardJson($section, $entity_id, $start_timestamp, $end_timestamp) {
    $output = $this->chartjsBlockSamplePageJson();

    return new JsonResponse($output);

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Vue Page: hello with parameter(s): ') . $section,
    ];
  }

  /**
   * @return array
   *   Json Array.

   */
  public function chartjsBlockSamplePageJson() {
    $json_content = \Drupal::service('htmlpage.content.object.samplepage')
      ->samplePageContent()['json_content'];

    return $json_content;
  }

}
