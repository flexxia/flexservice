<?php

/**
 * @file
 * Contains \Drupal\genpdf\Controller\GenpdfController.
 */

namespace Drupal\genpdf\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;

use Drupal\genpdfstyle\Content\GenpdfContentGenerator;
use Drupal\genpdf\Content\GenpdfJsonGenerator;

/**
 * An example controller.
 */
class GenpdfController extends ControllerBase {

  /**
   *
   */
  public function generateMeetingPage($entity_id) {
    $GenpdfContentGenerator = new GenpdfContentGenerator();
    $result = $GenpdfContentGenerator->runGenPdfMeeting($entity_id);

    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center">';
        $markup .= 'PDF File Generate Successful ';
        $markup .= $result;
      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   *
   */
  public function generateProgramPage($entity_id) {
    $GenpdfContentGenerator = new GenpdfContentGenerator();
    $result = $GenpdfContentGenerator->runGenPdfProgram($entity_id);

    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center">';
        $markup .= 'Program PDF File Generate Successful ';
        $markup .= $result;
      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   *
   */
  public function downloadPdffile($entity_id) {
    // $GenpdfContentGenerator = new GenpdfContentGenerator();
    // $result = $GenpdfContentGenerator->runGenPdf();

    $markup = NULL;
    $markup .= '<div class="row padding-0">';
      $markup .= '<div class="text-center">';
        $markup .= 'PDF';
        $markup .= '<br />';
        $markup .= $entity_id;
      $markup .= '</div>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   *
   */
  public function jsonMeeting($entity_id) {
    $GenpdfJsonGenerator = new GenpdfJsonGenerator();
    $object_content_data = $GenpdfJsonGenerator->meetingJson($entity_id);

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

  /**
   *
   */
  public function jsonProgram($entity_id) {
    $GenpdfJsonGenerator = new GenpdfJsonGenerator();
    $object_content_data = $GenpdfJsonGenerator->programJson($entity_id);

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

}
