<?php

/**
 * @file
 * Contains \Drupal\modalpage\Controller\ModalpageController.
 */

namespace Drupal\modalpage\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;

use Drupal\modalpage\Content\ModalContentGenerator;


/**
 * An example controller.
 */
class ModalpageController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function standardModalPage($section, $entity_id) {
    $DashpageModalContentGenerator = new DashpageModalContentGenerator();
    $markup = $DashpageModalContentGenerator->standardModalPage($section, $entity_id);

    dpm($markup['all']['header']);
    // $markup = render($markup['speaker']);
    // $markup = render($markup['all']['header']);
    $markup = render($markup['ytd']['header']);

    $build = array(
      '#type' => 'markup',
      '#header' => 'header',
      '#markup' => $markup,
      '#allowed_tags' => \Drupal::getContainer()->get('flexinfo.setting.service')->adminTag(),
    );

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function standardModalJson($section, $entity_id) {
    $DashpageModalContentGenerator = new DashpageModalContentGenerator();
    $object_content_data['content'] = $DashpageModalContentGenerator->standardModalPage($section, $entity_id);

    return new JsonResponse($object_content_data);

    // debug output as JSON format
    $build = array(
      '#type' => 'markup',
      '#markup' => json_encode($object_content_data),
    );

    return $build;
  }

}
