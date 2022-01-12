<?php

/**
 * @file
 * Contains \Drupal\flexform\Form\FlexformSummaryEvaluationForm.php.
 */

namespace Drupal\flexform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Html;

/**
 * Provides some Elements.
 */
trait FlexSummaryEvaluationFormElement {

  /**
   * {@inheritdoc}
   */
  public function addReferValueToElement($element, $speaker_users, $meeting_relatedtypes) {
    if ($speaker_users) {
      foreach ($speaker_users as $speaker_user) {
        $refer = [];
        foreach ($element as $key => $element_row) {
          if (isset($element_row['#type']) && $element_row['#type'] == 'label') {
            $element_row['#title'] .= " - - - " . $speaker_user->getAccountName();
          }
          if (isset($element_row['#refer_value'])) {
            $element_row['#refer_value']['refer_uid'] = $speaker_user->id();
          }
          $refer[$key] = $element_row;
        }

        $output[] = $refer;
      }
    }
    elseif ($meeting_relatedtypes) {
      foreach ($meeting_relatedtypes as $relatedtype) {
        $refer = [];
        foreach ($element as $key => $element_row) {
          if (isset($element_row['#type']) && $element_row['#type'] == 'label') {
            $element_row['#title'] .= " - - - " . $relatedtype;
          }
          $element_row['#refer_value']['refer_other'] = $relatedtype;
          $refer[$key] = $element_row;
        }

        $output[] = $refer;
      }
    }
    else {
      $output[] = $element;
    }

    return $output;
  }

  /**
   * Summary Evaluation Question Element.
   */
  public function getSummaryEvaluationQuestionElement(array $form, FormStateInterface $form_state, $question_term, $meeting_node = NULL) {
    $output = [];

    $field_type = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

    $question_relatedfield = \Drupal::getContainer()
      ->get('flexinfo.field.service')
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
    elseif ($field_type == 'radios') {
      $output = $this->getSummaryElementNumberDropdown($form, $form_state, $question_term, $speaker_users, $meeting_relatedtypes);
    }
    elseif ($field_type == 'selectkey') {
      $output = $this->getSummaryElementSelectkey($form, $form_state, $question_term, $speaker_users, $meeting_relatedtypes);
    }
    elseif ($field_type == 'textfield') {
      $output = $this->getSummaryElementTextfield($form, $form_state, $question_term, $speaker_users, $meeting_relatedtypes);
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummaryElementNumberDropdown(array $form, FormStateInterface $form_state, $question_term, $speaker_users, $meeting_relatedtypes) {
    $element = $this->getElementNumberDropdown($question_term);

    $output = $this->addReferValueToElement($element, $speaker_users, $meeting_relatedtypes);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummaryElementSelectkey(array $form, FormStateInterface $form_state, $question_term, $speaker_users, $meeting_relatedtypes) {
    $element = $this->getElementSelectkey($question_term);

    $output = $this->addReferValueToElement($element, $speaker_users, $meeting_relatedtypes);

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummaryElementTextfield(array $form, FormStateInterface $form_state, $question_term, $speaker_users, $meeting_relatedtypes) {

    $question_tid = $question_term->id();
    $fieldset_key = $question_tid . '_fieldset';

    if ($speaker_users) {
      foreach ($speaker_users as $speaker_user) {
        $speaker_uid = $speaker_user->id();
        $refer_element = $this->getmElementBasicTextfieldWithAjaxAddMore($form, $form_state, $question_term, $speaker_uid);

        $refer_element[$fieldset_key]['#title'] .= " - - - " . $speaker_user->getAccountName();
        $refer_element[$fieldset_key]['#refer_value']['refer_uid'] = $speaker_uid;

        // Html::getUniqueId()
        // 当有多个按钮, 使用一个callback必须定义 Unique'#name'.
        // 否则多个 getTriggeringElement() 只返回最后一个element.
        $refer_element[$fieldset_key]['actions']['add_one_more']['#name'] .= '_' . $relatedtype;

        $output[] = $refer_element;
      }
    }
    elseif ($meeting_relatedtypes) {
      foreach ($meeting_relatedtypes as $relatedtype) {
        $clean_relatedtype = str_replace(' ', '', trim($relatedtype));
        $refer_element = $this->getmElementBasicTextfieldWithAjaxAddMore($form, $form_state, $question_term, $clean_relatedtype);

        $refer_element[$fieldset_key]['#title'] .= " - - - " . $relatedtype;
        $refer_element[$fieldset_key]['#refer_value']['refer_other'] = $relatedtype;

        // Html::getUniqueId()
        // 当有多个按钮, 使用一个callback必须定义 Unique'#name'.
        // 否则多个 getTriggeringElement() 只返回最后一个element.
        $refer_element[$fieldset_key]['actions']['add_one_more']['#name'] .= '_' . $relatedtype;

        $output[] = $refer_element;
      }
    }
    else {
      $output[] = $this->getmElementBasicTextfieldWithAjaxAddMore($form, $form_state, $question_term);
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getElementNumberDropdown($question_term) {
    $output[] = [
      '#type' => 'label',
      '#title' => $this->getElementTitleDebugSuffix($question_term),
      '#prefix' => '<div class="clear-both reactset-element-number-dropdown-wrapper">',
      '#suffix' => '</div>',
      '#attributes' => [
        'class' => ['h5 font-size-16'],
      ],
    ];

    $question_scale = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValue($question_term, 'field_queslibr_scale');
    for ($i = 1; $i < ($question_scale + 1); $i++) {
      $output[] = $this->getElementBasicCountNumber($i);
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getElementSelectkey($question_term) {
    $output = [];
    $output[] = [
      '#type' => 'label',
      '#title' => $this->getElementTitleDebugSuffix($question_term),
      '#prefix' => '<div class="clear-both reactset-element-number-dropdown-wrapper">',
      '#suffix' => '</div>',
      '#attributes' => [
        'class' => ['h5 font-size-16'],
      ],
    ];

    $all_answer_terms = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($question_term, 'field_queslibr_selectkeyanswer');

    foreach ($all_answer_terms as $key => $answer_term) {
      $output[$key] = $this->getElementBasicSelectkeyAnswer($answer_term);
    }

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getElementTitleDebugSuffix($question_term) {
    $output = '';
    $output .= $question_term->getName();
    // $output .= " - - DebugTitle - - " . $question_term->id();

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getElementBasicCountNumber($i = 1) {
    $output = [
      '#type' => 'number',
      '#title' => $i,
      '#refer_value' => [
        'refer_uid' => NULL,
        'refer_tid' => NULL,
        'refer_other' => NULL,
      ],
    ];

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getElementBasicSelectkeyAnswer($answer_term = NULL) {
    $output = [
      '#type' => 'number',
      '#title' => $answer_term->getName() . ' sssss ' . $answer_term->id(),
      '#refer_value' => [
        'refer_uid' => NULL,
        'refer_tid' => NULL,
        'refer_other' => NULL,
      ],
    ];

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function getmElementBasicTextfieldWithAjaxAddMore(array $form, FormStateInterface $form_state, $question_term = NULL, $multiple_keyword = NULL) {
    $question_tid = $question_term->id();
    $wrapper_id = Html::getUniqueId('field-' . $question_tid . '-add-more-wrapper');
    $fieldset_key = $question_tid . '_fieldset';
    $textfield_name_key = $question_tid . '_name';

    // 当有多个按钮, 并且在一个数组里的时候. 必须使用Unique key去计数.
    $num_rows_key = $question_tid . '_num_rows_' . $multiple_keyword;

    // Gather the number of names in the form already.
    // Num key is set addOneMoreTextfieldFn() & removeLastOneTextfieldFn().
    $num_rows = $form_state->get($num_rows_key);
    // We have to ensure that there is at least one name field.
    if ($num_rows === NULL) {
      $form_state->set($num_rows_key, 1);
      $num_rows = 1;
    }

    // Add '#refer_value' here to match same level with number/selectkey field.
    $element[$fieldset_key] = [
      // '#type' => 'container',
      '#type' => 'fieldset',
      '#title' => $this->getElementTitleDebugSuffix($question_term),
      '#prefix' => '<div id="' . $wrapper_id . '">',
      '#suffix' => '</div>',
      '#refer_value' => [
        'refer_uid' => NULL,
        'refer_tid' => NULL,
        'refer_other' => NULL,
      ],
    ];

    for ($i = 0; $i < $num_rows; $i++) {
      $element[$fieldset_key][$textfield_name_key][$i] = [
        '#type' => 'textfield',
        '#title' => ucfirst($question_tid) . " (Value " . ($i + 1) . ")",
        '#title_display' => "invisible",
      ];
    }

    $element[$fieldset_key]['actions'] = [
      '#type' => 'actions',
    ];
    $element[$fieldset_key]['actions']['add_one_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => [[get_class($this), 'addOneMoreTextfieldFn']],
      '#ajax' => [
        'callback' => [get_class($this), 'addMoreTextfieldAjaxCallback'],
        'wrapper' => $wrapper_id,
      ],
      '#attributes' => [
        'class' => ['reactset-textfield-fieldset-wrapper'],
      ],
      '#name' => $textfield_name_key,
      '#num_rows_key' => $num_rows_key,
    ];

    return $element;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addMoreTextfieldAjaxCallback(array $form, FormStateInterface $form_state) {
    // Return element's #name.
    $id = $form_state->getTriggeringElement()['#name'];
    // Return element's #value.
    $id = $form_state->getTriggeringElement()['#value'];
    // Form Id.
    $id = $form_state->getTriggeringElement()['#id'];
    // Return ?.
    $id = $form_state->getTriggeringElement()['#parents'][1];

    // Add more Button's #array_parents is an array like.
    // Here $array_parents[1] is question tid.
    // @code
    // "#array_parents": [
    //   "reactset",
    //   453,
    //   0,
    //   "453_fieldset",
    //   "actions",
    //   "add_one_more"
    // ];
    // @endcode
    // Return fieldset name.
    $array_parents = $form_state->getTriggeringElement()['#array_parents'];

    $form['reactset'][452][0]['452_fieldset']['#title'] .= " - " . implode(" - ", $array_parents) . " - " . $id;

    // @code
    // return $form['reactset'][454][0];
    // @endcode
    return $form['reactset'][$array_parents[1]][$array_parents[2]];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOneMoreTextfieldFn(array $form, FormStateInterface $form_state) {
    $num_rows_key = $form_state->getTriggeringElement()['#num_rows_key'];
    $name_field = $form_state->get($num_rows_key);
    $add_button = $name_field + 1;
    $form_state->set($num_rows_key, $add_button);

    // Since our buildForm() method relies on the value of "$num_rows_key" to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }

}

/**
 * Generate Summary Evaluation Form.
 */
class FlexSummaryEvaluationForm extends FormBase {

  use FlexSummaryEvaluationFormElement;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'flexform_summary_evaluation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $meeting_nid = NULL) {
    $meeting_node = \Drupal::entityTypeManager()
      ->getStorage('node')->load($meeting_nid);

    if ($meeting_node && $meeting_node->getType() == 'meeting') {
      $form = $this->generateSummaryEvaluationForm($form, $form_state, $meeting_node);
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
   * {@inheritdoc}
   */
  public function generateSummaryEvaluationForm(array $form, FormStateInterface $form_state, $meeting_node = NULL) {
    $form['htmltext'] = [
      '#type' => 'label',
      '#title' => '<div class="clear-both"><h5>Summary Question Form</h5></div>',
    ];

    $form['form_item_meeting_info'] = \Drupal::service('flexform.service.basic.info')
      ->getElementMeetingInfo($meeting_node);

    $form['form_item_evaluation_form_info'] = \Drupal::service('flexform.service.basic.info')
      ->getElementEvaluationFormInfo($meeting_node);

    $form['title'] = \Drupal::service('flexform.service.basic.info')
      ->getElementTitleForSummary($meeting_node);

    $form['meeting_nid'] = \Drupal::service('flexform.service.basic.info')
      ->getElementMeetingNid($meeting_node);

    // When #tree is FALSE, the submit() gets no results through getValues().
    $form['#tree'] = TRUE;

    $form['reactset'] = [
      '#type' => 'fieldset',
      // '#type' => 'container',
      '#title' => $this->t('Summary Answer Set'),
      '#attributes' => [
        'class' => ['reactset-answerset-summary-wrapper'],
      ],
      '#open' => TRUE,
    ];

    $all_question_terms = $this->generateQuestionTermsByMeetingNid($meeting_node);
    if ($all_question_terms) {

      // Like $form['reactset'][$question_tid][$delta].
      foreach ($all_question_terms as $question_tid => $question_term) {
        $form['reactset'][$question_tid] = $this->getSummaryEvaluationQuestionElement($form, $form_state, $question_term, $meeting_node);
      }
    }

    $form['show'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['#prefix'] = '<div class="container summary-evaluation-form-container">';
    $form['#suffix'] = '</div>';

    // Add your asset library here.
    $form['#attached']['library'][] = 'flexform/node_add_summary_evaluation_form';

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
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->createSummaryEvaluationNode($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function createSummaryEvaluationNode($form, $form_state = NULL) {
    $summary_reactset = $this->convertSummaryQuestionValueToReactsetFormat($form, $form_state);

    foreach ($summary_reactset as $reactset) {
      $field_array = $this->generateSummaryEvaluationFieldsValue($form_state, $reactset);
      \Drupal::service('flexinfo.node.service')->entityCreateNode($field_array);
    }

    return;
  }

  /**
   * Answer_row format.
   * @code
   * $answer_row = [
   *   'question_tid' => $question_tid,
   *   'question_answer' => $subrow,
   *   'refer_uid' => NULL,
   *   'refer_tid' => NULL,
   *   'refer_other' => NULL,
   * ];
   * @endcode
   */
  public function convertSummaryQuestionValueToReactsetFormat($form, $form_state = NULL) {
    $reactset_values = $form_state->getValue('reactset');

    $output = [];
    $answer_set = [];

    // Get maxmium number.
    $max_num = 0;

    if ($reactset_values) {
      // Element is like ['reactset'][$question_tid][$delta][$answer_key].
      // Number answer value like this.
      // If there are more than one speaker or realted field type.
      // It will has array[1], array[2], etc.
      // Example:
      // @code
      // [125] => array(
      //   [0] => array(
      //       [1] => 2,
      //       [2] => 3
      //       [3] => 10,
      //       [4] => 28,
      //       [5] => 35,
      //   ),
      //   [1] => array(
      //       [1] => 1,
      //       [2] => 2
      //       [3] => 8,
      //       [4] => 30,
      //       [5] => 38,
      //   ),
      // )
      // @endcode
      foreach ($reactset_values as $question_tid => $question_rows) {
        foreach ($question_rows as $delta => $question_answers) {
          $ref_answers = [];
          foreach ($question_answers as $answer_key => $answer_num) {
            // Check if it is textfield or not.
            if (strpos($answer_key, '_fieldset') !== FALSE) {
              $textfield_name_key = $question_tid . '_name';
              $ref_answers = NestedArray::getValue($answer_num, [$textfield_name_key]);

              // Remove the empty values.
              // When all values are empty, it returns "array()".
              $ref_answers = array_filter($ref_answers);
            }
            else {
              // Number field and selectkey field.
              // Fill an array with values.
              $answer_array = array_fill(0, intval($answer_num), $answer_key);
              $ref_answers = array_merge($ref_answers, $answer_array);
            }
          }

          if ($ref_answers) {
            $answer_set[$question_tid][] = [
              'answer_values' => $ref_answers,
              'refer_value' => $this->generateReferValuesFromElements($form["reactset"][$question_tid][$delta]),
            ];

            $cuurent_max_num = count($ref_answers);
            if ($cuurent_max_num && is_numeric($cuurent_max_num)) {
              $max_num = max([$cuurent_max_num, $max_num]);
            }
          }

        }
      }
    }

    // Generate answer set format.
    for ($i = 0; $i < $max_num; $i++) {
      $one_evaluation_answers = [];
      foreach ($answer_set as $question_tid => $question_rows) {
        foreach ($question_rows as $delta => $question_values) {

          if (isset($answer_set[$question_tid][$delta]['answer_values'][0])) {
            // Shifts/Removes the first element from the array of answer_values.
            // Get the first element from the array of answer_values.
            $first_value = array_shift($answer_set[$question_tid][$delta]['answer_values']);

            // Some answer key is exist, but value is NULL.
            if ($first_value) {
              $answer_row = [
                'question_tid' => $question_tid,
                'question_answer' => $first_value,
                'refer_uid' => $answer_set[$question_tid][$delta]['refer_value']['refer_uid'],
                'refer_tid' => $answer_set[$question_tid][$delta]['refer_value']['refer_tid'],
                'refer_other' => $answer_set[$question_tid][$delta]['refer_value']['refer_other'],
              ];

              $one_evaluation_answers[] = $answer_row;
            }
          }
        }
      }

      $output[] = $one_evaluation_answers;
    }

    return $output;
  }

  /**
   * Returns Refer Values.
   */
  public function generateReferValuesFromElements($elements = NULL) {
    $output = [];

    if ($elements) {
      foreach ($elements as $element_row) {
        // Some of $element_row[0] are lable type and don't have '#refer_value'.
        if (isset($element_row['#refer_value'])) {
          $output = $element_row['#refer_value'];

          break;
        }
      }
    }

    return $output;
  }

  /**
   * Returns Fields Value.
   */
  public function generateSummaryEvaluationFieldsValue($form_state = NULL, $reactset = NULL) {
    $entity_bundle = 'evaluation';

    $output = [
      'type' => $entity_bundle,
      'title' => 'Summary Evaluation for meeting ' . $form_state->getValue('meeting_nid'),
      'langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
      'uid' => \Drupal::currentUser()->id(),
      'status' => 1,
    ];

    $output['field_evaluation_meetingnid'] = [$form_state->getValue('meeting_nid')];
    $output['field_evaluation_reactset'] = $reactset;

    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function generateQuestionTermsByMeetingNid($meeting_node = NULL) {
    $output = [];

    if ($meeting_node) {
      $evaluationform_term = \Drupal::service('flexinfo.node.service')
        ->getMeetingEvaluationformTerm($meeting_node);

      $output = \Drupal::service('flexinfo.field.service')
        ->getFieldAllTargetIdsEntitys($evaluationform_term, 'field_evaluationform_questionset');
    }

    return $output;
  }

}
