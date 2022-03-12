<?php
/**
 * @file
 * Contains \Drupal\ngpage\Form\NgpageSummaryEvaluationForm.php.
 */

namespace Drupal\ngpage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @deprecated.
 */
class NgpageSummaryEvaluationForm extends FormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'ngpage_summary_evaluation_form';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_id = NULL) {
    $form['htmltext'] = [
      '#type' => 'label',
      '#title' => '<div class="clear-both"><h5>Summary Question Form</h5></div>'
    ];

    $form['meeting_nid'] = [
      '#type' => 'number',
      '#title' => 'Meeting Nid',
      '#value' => $entity_id,
      '#disabled' => TRUE,
    ];

    $form['#tree'] = TRUE; // When this is set to false, the submit method gets no results through getValues().

    $form['reactset'] = array(
      '#type' => 'container',
      '#title' => $this->t('Answer'),
      '#attributes' => array(
        'class' => array('reactset-answerset-warpper'),
      ),
      '#open' => TRUE,
    );

    $answer_set = [];
    $all_question_terms = $this->_getQuestionTermsByMeetingNid($entity_id);
    if ($all_question_terms) {
      foreach ($all_question_terms as $tid => $question_term) {
        $question_scale = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($question_term, 'field_queslibr_scale');

        $question_fieldtype = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

        $related_field = \Drupal::service('flexinfo.field.service')
          ->getFieldFirstValue($question_term, 'field_queslibr_relatedfield');

        $form['reactset']['question_item_' . $tid] = array(
          '#type' => 'label',
          '#title' => $question_term->getName(),
          '#prefix' => '<div class="clear-both h5 font-size-16">',
          '#suffix' => '</div>',
        );

        if ($question_fieldtype == 'radios') {

          $boolean_duplicate_question = FALSE;
          if ($related_field == 'field_meeting_speaker') {
            $meeting_entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);

            $speaker_users = \Drupal::getContainer()
              ->get('flexinfo.field.service')
              ->getFieldAllTargetIdsEntitys($meeting_entity, 'field_meeting_speaker', 'user');

            if ($speaker_users && count($speaker_users) > 1) {
              $boolean_duplicate_question = TRUE;

              foreach ($speaker_users as $speaker_user) {
                $form['reactset']['question_item_' . $tid . $speaker_user->id()] = array(
                  '#type' => 'label',
                  '#title' => $question_term->getName() . ' - ' . $speaker_user->getAccountName(),
                  '#prefix' => '<div class="clear-both h5 font-size-16">',
                  '#suffix' => '</div>',
                );

                for ($i = 1; $i < ($question_scale + 1); $i++) {
                  $form['reactset']['question_answer_radios_'. $tid . '_' . $i . '_' . 'refer_uid' . '_' .$speaker_user->id()] = array(
                    '#type' => 'number',
                    '#title' => $i,
                  );

                  $answer_set['radios'][$tid][] = $i;
                }
              }
            }
          }

          if (!$boolean_duplicate_question) {
            for ($i = 1; $i < ($question_scale + 1); $i++) {
              $form['reactset']['question_answer_radios_'. $tid . '_' . $i] = array(
                '#type' => 'number',
                '#title' => $i,
              );

              $answer_set['radios'][$tid][] = $i;
            }
          }
        }
        elseif ($question_fieldtype == 'selectkey') {
          $answer_terms = \Drupal::getContainer()
            ->get('flexinfo.field.service')
            ->getFieldAllTargetIdsEntitys($question_term, 'field_queslibr_selectkeyanswer');

          if ($answer_terms) {
            foreach ($answer_terms as $key => $answer_term) {
              $form['reactset']['question_answer_selectkey_'. $tid . '_' . $key] = array(
                '#type' => 'number',
                '#title' => $answer_term->getName(),
              );

              $answer_set['selectkey'][$tid][] = $key;
            }
          }
        }
        elseif ($question_fieldtype == 'textfield') {
          $form['reactset']['question_answer_textfield_'. $tid . '_' . 1] = array(
            '#type' => 'textarea',
            '#title' => NULL,
            '#description' => 'use "&&&" to break line',
          );

          $answer_set['textfield'][$tid][] = 1;
        }
      }
    }

    $form['show'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['#prefix'] = '<div class="container">';
    $form['#suffix'] = '</div>';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('email')) {
      if (strpos($form_state->getValue('email'), '.com') === FALSE) {
        $form_state->setErrorByName('email', $this->t('This is not a .com email address.'));
      }
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->_convertQuestionReactset($form_state);

    return;
  }

  /**
   *
   */
  public function _convertQuestionReactset($form_state = NULL) {
    $reactset_values = $form_state->getValue('reactset');

    $output = [];
    $answer_set = [];

    if ($reactset_values) {

      // $key is like 'question_answer_radios_2096_1'
      // or
      // $key is like 'question_answer_radios_2096_1_refer_uid_267'
      foreach ($reactset_values as $key => $row) {
        if ($row) {
          $result_array = explode("_", $key);
          if ($result_array && isset($result_array[3])) {
            $answer_key = $result_array[3];
            if (isset($result_array[7])) {
              $answer_key = $result_array[3] . '_' . $result_array[6] . '_' . $result_array[7];
            }

            if ($result_array[2] == 'radios' || $result_array[2] == 'selectkey') {
              for ($i = 0; $i < $row; $i++) {
                $answer_set[$answer_key][] = $result_array[4];
              }
            }
            elseif ($result_array[2] == 'textfield') {
              $textfield_row = explode("&&&", $row);
              if ($textfield_row) {
                foreach ($textfield_row as $comment) {
                  $answer_set[$answer_key][] = $comment;
                }
              }
            }
          }
        }
      }

      // get maxmium number
      $max_num = 0;
      if ($answer_set) {
        foreach ($answer_set as $key => $value) {
          $array_sum = count($value);

          if ($array_sum > $max_num) {
            $max_num = array_sum($value);
          }
        }
      }
    }

    for ($i = 0; $i < $max_num; $i++) {
      $output = NULL;
      foreach ($answer_set as $key => $value) {
        if (isset($value[$i])) {
          $answer_tid = $key;

          if (strpos($key, '_') !== false) {
            $answer_key_array = explode("_", $key);
            $answer_tid = $answer_key_array[0];
          }

          $answer_row = [
            'question_tid' => $answer_tid,
            'question_answer' => $value[$i]
          ];

          if (isset($answer_key_array[2])) {
            if ($answer_key_array[1] == 'uid') {
              $answer_row['refer_uid'] = $answer_key_array[2];
            }
          }

          $output[] = $answer_row;
        }
      }

      if ($output) {
        $this->_entityCreateForm($form_state, $output);
      }
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function _entityCreateForm($form_state = NULL, $reactset = NULL) {
    $field_array = $this->_generateSummaryEvaluationFieldsValue($form_state, $reactset);
    \Drupal::getContainer()->get('flexinfo.node.service')->entityCreateNode($field_array);

    return;
  }

  /**
   *
   */
  public function _generateSummaryEvaluationFieldsValue($form_state = NULL, $reactset = NULL) {
    $entity_bundle = 'evaluation';

    $output = array(
      'type' => $entity_bundle,
      'title' => 'Summary Evaluation for meeting ' . $form_state->getValue('meeting_nid'),
      'langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      'uid' => \Drupal::currentUser()->id(),
      'status' => 1,
    );

    $output['field_evaluation_meetingnid'] = array($form_state->getValue('meeting_nid'));
    $output['field_evaluation_reactset'] = $reactset;

    return $output;
  }

  /**
   *
   */
  public function _getQuestionTermsByMeetingNid($entity_id = NULL) {
    $output = [];

    $meeting_entity = \Drupal::entityTypeManager()->getStorage('node')->load($entity_id);
    if ($meeting_entity) {
      $evaluationform_term = \Drupal::service('flexinfo.node.service')
        ->getMeetingEvaluationformTerm($meeting_entity);

      $output = \Drupal::service('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($evaluationform_term, 'field_evaluationform_questionset');
    }

    return $output;
  }

}
