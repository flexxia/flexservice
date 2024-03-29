<?php

/**
 * @file
 * Contains Drupal\flexform\Service\FlexformBasicInfo.php.
 */
namespace Drupal\flexform\Service;

use Drupal\Core\Controller\ControllerBase;

use Drupal\dashpage\Content\DashpageObjectContent;

/**
 * Service container.
 * \Drupal::service('flexform.service.basic.info')->demo();
 */
class FlexformBasicInfo extends ControllerBase {

  /**
   * @option CustomText.
   */
  public function getElementCustomText($question_term) {
    $output = [
      '#title' => $question_term->getName(),
      '#title_display' => 'before',
      '#type' => 'item',
    ];

    return $output;
  }

  /**
   *
   */
  public function getElementEvaluationFormInfo($meeting_entity = NULL) {
    $output = [];
    $evaluation_form_entity = \Drupal::service('flexinfo.node.service')
      ->getMeetingEvaluationformTerm($meeting_entity);

    if ($evaluation_form_entity) {
      $output = [
        '#type' => 'item',
        '#title' => $evaluation_form_entity->getName($meeting_entity),
      ];
    }

    return $output;
  }

  /**
   *
   */
  public function getElementMeetingInfo($meeting_node = NULL) {
    $DashpageObjectContent = new DashpageObjectContent();
    $meeting_tile_html = $DashpageObjectContent->blockTileMeetingHtml($meeting_node, $meeting_share_link = FALSE, $meeting_snapshot_link = TRUE);

    $output = [
      '#type' => 'item',
      '#title' => $meeting_tile_html,
    ];

    return $output;
  }

  /**
   *
   */
  public function getElementMeetingNid($meeting_node = NULL) {
    $output = [
      '#title' => $this->t('Meeting Nid'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#disabled' => TRUE,
      '#default_value' => $meeting_node,
      '#selection_handler' => 'default',
      '#selection_settings' => [
        'target_bundles' => ['meeting'],
      ],
      '#autocreate' => [
        'bundle' => 'meeting',
        'uid' => 1,
      ],
    ];

    return $output;
  }

  /**
   *
   */
  public function getElementTitle($meeting_node = NULL) {
    $output = [
      '#type' => 'textfield',
      '#title' => $this->t('Evaluation Title'),
      '#maxlength' => 255,
      '#size' => 64,
      '#default_value' => 'Evaluation for meeting ' . $meeting_node->id(),
      '#required' => TRUE,
    ];

    return $output;
  }

  /**
   *
   */
  public function getElementTitleForSummary($meeting_node = NULL) {
    $output = $this->getElementTitle($meeting_node);
    $output['#default_value'] = 'Summary Evaluation for meeting ' . $meeting_node->id();

    return $output;
  }

  /**
   * @deprecated
   * @see \Drupal::service('flexinfo.node.service')->getMeetingEvaluationformTerm($meeting_entity);
   */
  public function getEvaluationFormEntityFromMeetingNode($meeting_node = NULL) {
    $output = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetIdToEntity($meeting_node, 'taxonomy_term', 'field_meeting_evaluationform');

    return $output;
  }

  /**
   *
   */
  public function getQuestionTermsFromMeetingNode($meeting_entity = NULL) {
    $evaluation_form_entity = \Drupal::service('flexinfo.node.service')
      ->getMeetingEvaluationformTerm($meeting_entity);

    $output = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($evaluation_form_entity, 'field_evaluationform_questionset');

    return $output;
  }

}
