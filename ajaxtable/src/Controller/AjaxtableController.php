<?php

namespace Drupal\ajaxtable\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;


use Drupal\ajaxtable\Content\ajaxtableContentGenerator;

/**
 * An example controller.
 */
class AjaxtableController extends ControllerBase {

  /**
   *
   */
  public function debugControllerForm() {
    $form = \Drupal::formBuilder()->getForm('Drupal\ajaxtable\Form\DebugControllerForm');

    //
    return new JsonResponse($form);

    //
    // $jsonData = json_encode($form, JSON_PRETTY_PRINT);

    // or render
    $build = array(
      '#type' => 'markup',
      '#markup' => render($form),
      // '#markup' => str_replace("\n", "<br>", $jsonData),
    );

    return $build;
  }

}
