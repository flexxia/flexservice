<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/debug_field.php');
  _get_all_fields_in_a_bundle();
 */

use Drupal\field\Entity\FieldConfig;

/**
 *
 */
function _get_all_fields_in_a_bundle() {
  $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'meeting');
  // $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'article');

  // cardinality
 // ksm($bundle_fields['field_meeting_module']->getItemDefinition());
 // ksm($bundle_fields['field_meeting_module']->getItemDefinition()->getFieldDefinition()->getFieldStorageDefinition()->getCardinality());
}

/**
 *
 */
function _get_field($entity_id = NULL) {
  $entity_storage = \Drupal::entityTypeManager()->getStorage('node');
  $entity = $entity_storage->load($entity_id);

  $field_name = 'field_page_city';
  $field_name = 'body';
  $field = $entity->get($field_name);
}

/**
 * Obtaining information about the field
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/debug_field.php');
  _get_field_information(3);
 */
function _get_field_information($entity_id = NULL) {
  // $entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
  $entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($entity_id);
  $field = $entity->get('field_page_city');

  $definition = $field->getFieldDefinition();
  $field_name = $definition->get('field_name');
  $field_type = $definition->get('field_type');

}

/**
 *
  _set_field_value(3);
 */
function _set_field_value($entity_id = NULL) {
  $entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);

  $field_name = 'field_page_city';
  $field = $entity->get($field_name);
  // dpm($entity->get($field_name)->value);

  $field_values = $field->getValue();
  $field_values[0]['value'] = 'London';
  $field->setValue($field_values);
  $entity->save();

  $entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
  // dpm($entity->get($field_name)->value);
}

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexservice/phpdebug/debug_field.php');
  _update_field_first_values_by_vid();
 */
function _update_field_first_values_by_vid() {
  $entitys = \Drupal::service('flexinfo.term.service')
    ->getFullTermsFromVidName($vid = 'questionlibrary');

  foreach ($entitys as $key => $entity) {
    _update_field_first_value($entity, $value = 'BEST ANSWER');
  }

}

/**
 *
  $field_name = 'field_queslibr_footeranswer';
 */
function _update_field_first_value($entity = NULL, $value = NULL) {
  $field_name = 'field_queslibr_chartfooter';
  $field = $entity->get($field_name);

  $field_values = $field->getValue();

  if (isset($field_values[0]['value']) && $field_values[0]['value']) {
  }
  else {
    $field_values[0]['value'] = $value;
    $field->setValue($field_values);
    $entity->save();
  }

  return;
}

/**
 *
 */
function _update_field_first_target_id($entity = NULL, $target_id = NULL) {
  $field_name = 'field_queslibr_chartlegend';
  $field = $entity->get($field_name);

  $field_values = $field->getValue();
  $field_values[0]['target_id'] = $target_id;
  $field->setValue($field_values);
  $entity->save();
}

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/debug_field.php');
  _save_question_field_for_selectkeyanswer_value();
 */
function _save_question_field_for_selectkeyanswer_value() {
  $selectkeyanswer_terms = \Drupal::getContainer()->get('flexinfo.term.service')->getFullTermsFromVidName($vid = 'selectkeyanswer');

  $skip_question_tids = array(
    5448,
    5449,
    5446,
    5422,
    5425,
    5444,
    5445,
    5447,
    5424,
    5423,
  );

  foreach ($selectkeyanswer_terms as $selectkeyanswer_term) {
    if (in_array($selectkeyanswer_term->id(), $skip_question_tids)) {
      continue;
    }

    $question_entity = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstTargetIdTermEntity($selectkeyanswer_term, 'field_keyanswer_question');

    if ($question_entity) {
      \Drupal::service('flexinfo.field.service')
        ->updateAdditionalFieldValue('taxonomy_term', $question_entity, 'field_queslibr_selectkeyanswer', array($selectkeyanswer_term->id()));
    }
  }

}

function _print_field() {
  // dpm(\Drupal::currentUser());

  $entity_type = 'taxonomy_term';
  $field_name  = 'field_theraparea_eventregion';
  $bundle      = 'therapeuticarea';

  $entity_type = 'user';
  $field_name  = 'field_user_region';
  $bundle      = 'user';

  $entity_type = 'node';
  $field_name  = 'field_meeting_eventregion';
  $bundle      = 'meeting';

  $entityManager = Drupal::service('entity.manager');
  $FieldDefinition = $entityManager->getFieldDefinitions($entity_type, $field_name);

  $FieldConfig = FieldConfig::loadByName($entity_type, $bundle, $field_name);
  $target_bundles = $FieldConfig->getSettings()['handler_settings']['target_bundles'];

  $field = \Drupal\field\Entity\FieldStorageConfig::loadByName($entity_type, $field_name);
}
