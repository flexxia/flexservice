<?php

namespace Drupal\ngdata\Entity\Node;

/**
 * Class NgdataNodeMeeting.
 \Drupal::service('ngdata.node.meeting')->demo();
 */
class NgdataNodeMeeting extends NgdataNode {

  /**
   * Constructs a new NgdataNodeMeeting object.
   */
  public function __construct() {

  }

  /**
   * @param $meeting_entity
   *

   */
  public function getMeetingEvaluationformTid($meeting_entity = NULL) {
    $evaluationform_tid = NULL;

    if ($meeting_entity && method_exists($meeting_entity, 'getType')) {
      if ($meeting_entity->getType() == 'meeting') {
        $meeting_evaluationform_tid = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstTargetId($meeting_entity, 'field_meeting_evaluationform');

        if ($meeting_evaluationform_tid) {
          $evaluationform_tid = $meeting_evaluationform_tid;
        }
        else {
          $program_tid = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldFirstTargetId($meeting_entity, 'field_meeting_program');

          if ($program_tid) {
            $program_entity = \Drupal::entityTypeManager()
              ->getStorage('taxonomy_term')
              ->load($program_tid);

            $evaluationform_tid = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldFirstTargetId($program_entity, 'field_program_evaluationform');
          }
        }
      }
    }

    return $evaluationform_tid;
  }

  /**
   *
   */
  public function countMeetingNodesArray($meeting_nodes_array = array()) {
    $output = array();

    foreach ($meeting_nodes_array as $key => $value) {
      $output[$key] = count($value);
    }

    return $output;
  }

  /**
   *
   */
  public function meetingNodesByBU($meeting_nodes = array()) {
    $output = [];

    $bu_tids = \Drupal::service('ngdata.term')->getTermListByVocabulary('businessunit')['tid'];

    foreach ($bu_tids as $key => $bu_tid) {
      $theraparea_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')
        ->wrapperTermTidsByField('therapeuticarea', 'field_theraparea_businessunit', array($bu_tid), 'IN');

      $output[] = \Drupal::service('flexinfo.querynode.service')
        ->meetingNodesByTheraparea($meeting_nodes, $theraparea_tids);
    }

    return $output;
  }

  /**
   * @deprecated by 2020 Feb
   */
  public function meetingNodesByEventType($meeting_nodes = array()) {
    $output = $this->meetingNodesByStandardTermWithNodeField($meeting_nodes, 'eventtype', 'field_meeting_eventtype');

    return $output;
  }

  /**
   *
   */
  public function meetingNodesByStandardTermWithNodeField($meeting_nodes = array(), $vid = 'eventtype', $node_field = 'field_meeting_eventtype') {
    $output = array();

    $TermList = \Drupal::service('ngdata.term')->getTermListByVocabulary($vid);

    foreach ($TermList['tid'] as $key => $row) {
      $output[$row] = \Drupal::service('flexinfo.querynode.service')
        ->wrapperMeetingNodesByFieldValue($meeting_nodes, $node_field, array($row), 'IN');
    }

    return $output;
  }

  /**
   * @deprecated
   * @see meetingNodesByTherapeuticAreaByBuTids()
   */
  public function meetingNodesByTherapeuticArea($meeting_nodes = array(), $entity_id = NULL) {
    $output = $this->meetingNodesByTherapeuticAreaByBuTids($meeting_nodes, [$entity_id]);

    return $output;
  }

  /**
   *
   */
  public function meetingNodesByTherapeuticAreaByBuTids($meeting_nodes = array(), $bu_tids = []) {
    $output = [];

    $therap_tids = \Drupal::service('ngdata.term')
      ->getTermTherapeuticAreaListByBuTids($bu_tids)['tid'];

    foreach ($therap_tids as $key => $therap_tid) {
      $output[] = \Drupal::service('flexinfo.querynode.service')
        ->meetingNodesByTheraparea($meeting_nodes, array($therap_tid));
    }

    return $output;
  }

  /**
   *
   */
  public function meetingNodesByDiseaseState($meeting_nodes = array(), $entity_id = NULL) {
    $output = [];

    $diseasestate_tids = \Drupal::service('ngdata.term')->getTermListByVocabulary('diseasestate')['tid'];

    foreach ($diseasestate_tids as $key => $diseasestate_tid) {
      $output[] = \Drupal::service('flexinfo.querynode.service')
        ->meetingNodesByDiseasestate($meeting_nodes, array($diseasestate_tid));
    }

    return $output;
  }

}
