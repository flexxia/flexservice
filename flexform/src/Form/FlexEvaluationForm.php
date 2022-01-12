<?php

namespace Drupal\flexform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Flex Evaluation Form.
 */
class FlexEvaluationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'flex_evaluation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $meeting_nid = NULL) {
    $meeting_node = \Drupal::entityTypeManager()
      ->getStorage('node')->load($meeting_nid);

    if ($meeting_node && $meeting_node->getType() == 'meeting') {
      $form = $this->generateEvaluationForm($form, $form_state, $meeting_node);
    }
    else {
      $form = $this->generateEmptyValueForm($form, $form_state);
    }

    return $form;
  }

  /**
   * Empty form.
   */
  public function generateEmptyValueForm(array $form, FormStateInterface $form_state) {
    $form['form_info'] = [
      '#type' => 'item',
      '#title' => 'The meeting ID is not correct, Please try again',
    ];

    return $form;
  }

  /**
   * Evaluation Form.
   */
  public function generateEvaluationForm(array $form, FormStateInterface $form_state, $meeting_node = NULL) {
    $question_terms = \Drupal::service('flexform.service.basic.info')
      ->getQuestionTermsFromMeetingNode($meeting_node);

    $form['form_item_meeting_info'] = \Drupal::service('flexform.service.basic.info')
      ->getElementMeetingInfo($meeting_node);

    $form['form_item_evaluation_form_info'] = \Drupal::service('flexform.service.basic.info')
      ->getElementEvaluationFormInfo($meeting_node);

    $form['title'] = \Drupal::service('flexform.service.basic.info')
      ->getElementTitle($meeting_node);

    $form['meeting_nid'] = \Drupal::service('flexform.service.basic.info')
      ->getElementMeetingNid($meeting_node);

    // When this is set to false, the submit method gets no results through getValues().
    $form['#tree'] = TRUE;

    $form['reactset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Questions Group'),
    ];

    // Like $form['reactset'][$question_tid][$delta].
    foreach ($question_terms as $question_tid => $question_term) {
      $form['reactset'][$question_tid] = $this->_getEvaluationQuestionElement($question_term, $meeting_node);
    }

    // $form['save'] = [
    //   '#type' => 'submit',
    //   '#title' => $this->t('Save'),
    //   '#weight' => '0',
    // ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['#attributes'] = [
      'class' => [
        'margin-top-24',
        'margin-right-36',
        'margin-left-36',
      ],
    ];

    // Add your asset library here.
    $form['#attached']['library'][] = 'flexform/node_add_evaluation_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // \Drupal::messenger()->addMessage("form_state value is below line 133");
    // foreach ($form_state->getValues() as $key => $value) {
    //   \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    // }

    $this->_createEvaluationNode($form, $form_state);

    // Page redirect.
    // $url_path = '/ngpage/standardterm/page/evaluationform';
    // $url = Url::fromUserInput($url_path);
    // $form_state->setRedirectUrl($url);
  }

  /**
   * {@inheritdoc}
   */
  public function _createEvaluationNode($form, $form_state = NULL) {
    $reactset = $this->_convertQuestionValueToReactsetFormat($form, $form_state);
    $field_array = $this->_generateEvaluationFieldsValue($form_state, $reactset);
    \Drupal::service('flexinfo.node.service')->entityCreateNode($field_array);
  }

  /**
   * $answer_row format.
   $answer_row = [
     'question_tid' => $question_tid,
     'question_answer' => $subrow,
     'refer_uid' => NULL,
     'refer_tid' => NULL,
     'refer_other' => NULL,
   ];
   */
  public function _convertQuestionValueToReactsetFormat($form, $form_state = NULL) {
    $output = [];

    $reactset_values = $form_state->getValue('reactset');
    if ($reactset_values) {
      foreach ($reactset_values as $question_tid => $reactset_value_row) {
        foreach ($reactset_value_row as $delta => $row) {
          if ($row) {
            if (is_array($row)) {
              foreach ($row as $subrow) {
                if ($subrow) {
                  $answer_row = [
                    'question_tid' => $question_tid,
                    'question_answer' => $subrow,
                    'refer_uid' => $form['reactset'][$question_tid][$delta]['#refer_value']['refer_uid'],
                    'refer_tid' => $form['reactset'][$question_tid][$delta]['#refer_value']['refer_tid'],
                    'refer_other' => $form['reactset'][$question_tid][$delta]['#refer_value']['refer_other'],
                  ];
                  $output[] = $answer_row;
                }
              }
            }
            else {
              if ($row && !empty($row)) {
                $answer_row = [
                  'question_tid' => $question_tid,
                  'question_answer' => $row,
                  'refer_uid' => $form['reactset'][$question_tid][$delta]['#refer_value']['refer_uid'],
                  'refer_tid' => $form['reactset'][$question_tid][$delta]['#refer_value']['refer_tid'],
                  'refer_other' => $form['reactset'][$question_tid][$delta]['#refer_value']['refer_other'],
                ];

                if ($answer_row) {
                  $output[] = $answer_row;
                }
              }
            }
          }
        }
      }
    }

    return $output;
  }

  /**
   *
   */
  public function _generateEvaluationFieldsValue($form_state = NULL, $reactset = []) {
    $entity_bundle = 'evaluation';

    $output = array(
      'type' => $entity_bundle,
      'title' => $form_state->getValue('title'),
      'langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      'uid' => \Drupal::currentUser()->id(),
      'status' => 1,
    );

    $output['field_evaluation_meetingnid'] = $form_state->getValue('meeting_nid');
    $output['field_evaluation_reactset'] = $reactset;

    return $output;
  }

  /**
   * EvaluationQuestionElement.
   */
  public function _getEvaluationQuestionElement($question_term, $meeting_node = NULL) {
    $field_type = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

    $question_relatedfield = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_relatedfield');

    $speaker_users = [];
    $meeting_relatedtypes = [];
    if ($question_relatedfield == 'field_meeting_speaker') {
      $speaker_users = \Drupal::service('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($meeting_node, 'field_meeting_speaker', 'user');
    }
    elseif ($question_relatedfield == 'field_queslibr_relatedtype') {
      $meeting_relatedtypes = \Drupal::service('flexinfo.field.service')
        ->getFieldAllValues($question_term, 'field_queslibr_relatedtype');
    }

    if ($field_type == 'customtext') {
      $output = \Drupal::service('flexform.service.basic.info')
      ->getElementCustomText($question_term);
    }
    if ($field_type == 'radios') {
      $output = $this->_getElementNumberDropdown($question_term, $speaker_users, $meeting_relatedtypes);
    }
    elseif ($field_type == 'selectkey') {
      $output = $this->_getElementSelectkey($question_term, $speaker_users, $meeting_relatedtypes);
    }
    elseif ($field_type == 'textfield') {
      $output = $this->_getElementTextfield($question_term, $speaker_users, $meeting_relatedtypes);
    }

    return $output;
  }

  /**
   * @option Number Integer.
   */
  public function _getElementNumberInteger($question_term) {
    $output = [
      '#title' => $question_term->getName(),
      '#title_display' => 'before',
      '#type' => 'number',
    ];

    return $output;
  }

  /**
   * @option Number Integer.
   */
  public function _getElementNumberDropdown($question_term, $speaker_users = [], $meeting_relatedtypes = []) {
    $output = [];
    $basic_element = $this->_getElementNumberDropdownBasic($question_term);

    if ($speaker_users) {
      foreach ($speaker_users as $speaker_user) {
        $row = $basic_element;
        $row['#title'] .= " - - - " . $speaker_user->getAccountName();
        $row['#refer_value']['refer_uid'] = $speaker_user->id();

        $output[] = $row;
      }
    }
    elseif ($meeting_relatedtypes) {
      foreach ($meeting_relatedtypes as $relatedtype) {
        $row = $basic_element;
        $row['#title'] .= " - - - " . $relatedtype;
        $row['#refer_value']['refer_other'] = $relatedtype;

        $output[] = $row;
      }
    }
    else {
      $output[] = $basic_element;
    }

    return $output;
  }

  /**
   * @option Number Integer.
   */
  public function _getElementNumberDropdownBasic($question_term) {
    $question_scale = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_scale');

    if (empty($question_scale)) {
      $question_scale = 5;
    }
    $range = range(1, $question_scale);

    $output = [
      '#type' => 'select',
      '#title' => $question_term->getName(),
      '#options' => array_combine($range, $range),
      "#empty_option" => $this->t('- Select -'),
      '#default_value' => NULL,
      '#refer_value' => [
        'refer_uid' => NULL,
        'refer_tid' => NULL,
        'refer_other' => NULL,
      ],
    ];

    return $output;
  }

  /**
   * Selectkey question.
   */
  public function _getElementSelectkey($question_term, $speaker_users = [], $meeting_relatedtypes = []) {
    $output = [];
    $basic_element = $this->_getElementSelectkeyBasic($question_term);

    if ($speaker_users) {
      foreach ($speaker_users as $speaker_user) {
        $row = $basic_element;
        $row['#title'] .= " - - - " . $speaker_user->getAccountName();
        $row['#refer_value']['refer_uid'] = $speaker_user->id();

        $output[] = $row;
      }
    }
    elseif ($meeting_relatedtypes) {
      foreach ($meeting_relatedtypes as $relatedtype) {
        $row = $basic_element;
        $row['#title'] .= " - - - " . $relatedtype;
        $row['#refer_value']['refer_other'] = $relatedtype;

        $output[] = $row;
      }
    }
    else {
      $output[] = $basic_element;
    }

    return $output;
  }

  /**
   * Selectkey question.
   */
  public function _getElementSelectkeyBasic($question_term, $speaker_users = [], $meeting_relatedtypes = []) {
    $all_answer_terms = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($question_term, 'field_queslibr_selectkeyanswer');

    $options = [];
    if ($all_answer_terms) {
      foreach ($all_answer_terms as $answer_term) {
        $options[$answer_term->id()] = $answer_term->getName();
      }
    }

    $output = [
      '#title' => $question_term->getName(),
      '#title_display' => 'before',
      '#type' => 'checkboxes',
      "#empty_option" => $this->t('- Select -'),
      '#multiple' => TRUE,
      '#options' => $options,
      '#question_fieldtype' => "selectkey",
      '#refer_value' => [
        'refer_uid' => NULL,
        'refer_tid' => NULL,
        'refer_other' => NULL,
      ],
    ];

    return $output;
  }

  /**
   * Text question.
   */
  public function _getElementTextfield($question_term, $speaker_users = [], $meeting_relatedtypes = []) {
    $output = [];
    $basic_element = $this->_getElementTextfieldBasic($question_term);

    if ($speaker_users) {
      foreach ($speaker_users as $speaker_user) {
        $row = $basic_element;
        $row['#title'] .= " - - - " . $speaker_user->getAccountName();
        $row['#refer_value']['refer_uid'] = $speaker_user->id();

        $output[] = $row;
      }
    }
    elseif ($meeting_relatedtypes) {
      foreach ($meeting_relatedtypes as $relatedtype) {
        $row = $basic_element;
        $row['#title'] .= " - - - " . $relatedtype;
        $row['#refer_value']['refer_other'] = $relatedtype;

        $output[] = $row;
      }
    }
    else {
      $output[] = $basic_element;
    }

    return $output;
  }

  /**
   * Text question.
   */
  public function _getElementTextfieldBasic($question_term) {
    $output = [
      '#title' => $question_term->getName(),
      '#title_display' => 'before',
      '#type' => 'textfield',
      '#size' => 60,
      '#maxlength' => 255,
      '#refer_value' => [
        'refer_uid' => NULL,
        'refer_tid' => NULL,
        'refer_other' => NULL,
      ],
      // pattern: A string for the native HTML5 pattern attribute.
      // '#pattern' => 'some-prefix-[a-z]+',
      // '#attributes' => [
      //   'class' => ['new-demo-class'],
        // 'data-toggle' => ['tooltip'],
        // 'data-original-title' => ['tooltip text'],
      // ],
    ];

    return $output;
  }

}
