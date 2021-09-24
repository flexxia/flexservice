<?php

/**
 * @file
 * Contains \Drupal\flexform\Controller\FlexformController.
 */

namespace Drupal\flexform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\JsonResponse;


use Drupal\flexform\Content\FlexformContentGenerator;

/**
 * An example controller.
 */
class FlexformController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function entityAdd($entity_type, $entity_bundle) {
    $FlexformContentGenerator = new FlexformContentGenerator();
    $entity_form = $FlexformContentGenerator->entityAdd($entity_type, $entity_bundle);

    // return \Drupal::formBuilder()->getForm($entity_form);

    // or render
    $form = \Drupal::formBuilder()->getForm($entity_form);
    $build = array(
      '#type' => 'markup',
      '#markup' => render($form),
      '#attached' => array(
        'library' => array(
          'flexform/entity_form',
        ),
      ),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function entityEdit($entity_type, $entity_id) {
    $FlexformContentGenerator = new FlexformContentGenerator();
    $entity_form = $FlexformContentGenerator->entityEdit($entity_type, $entity_id);

    // return \Drupal::formBuilder()->getForm($entity_form);

    // or render
    $form = \Drupal::formBuilder()->getForm($entity_form);
    $build = array(
      '#type' => 'markup',
      '#markup' => render($form),
      '#attached' => array(
        'library' => array(
          'flexform/entity_form',
        ),
      ),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function entityView($entity_type, $entity_bundle, $entity_id) {
    $FlexformContentGenerator = new FlexformContentGenerator();
    $entity_view = $FlexformContentGenerator->entityView($entity_type, $entity_bundle, $entity_id);

    $build = array(
      '#type' => 'markup',
      '#markup' => render($entity_view),
      '#attached' => array(
        'library' => array(
          'flexform/entity_view',
        ),
      ),
    );

    return $build;
  }

  /**
   *
   */
  public function summaryEvaluationAddForm($meeting_nid) {
    $form = \Drupal::formBuilder()->getForm('Drupal\flexform\Form\FlexSummaryEvaluationForm', $meeting_nid);


    //
    // return new JsonResponse($form);

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
