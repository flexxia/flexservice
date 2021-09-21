<?php

namespace Drupal\flexform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

use Drupal\dashpage\Content\DashpageObjectContent;

/**
 * Class FlexEvaluationForm.
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
   * {@inheritdoc}
   */
  public function generateEmptyValueForm(array $form, FormStateInterface $form_state) {
    $form['form_info'] = [
      '#type' => 'item',
      '#title' => 'The meeting ID is not correct, Please try again',
    ];

    return $form;
  }

  /**
   *
   */
  public function generateEvaluationForm(array $form, FormStateInterface $form_state, $meeting_node = NULL) {
    $evaluation_form_entity = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetIdTermEntity($meeting_node, 'field_meeting_evaluationform');

    $question_terms = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($evaluation_form_entity, 'field_evaluationform_questionset');

    //
    $DashpageObjectContent = new DashpageObjectContent();
    $meeting_tile_html = $DashpageObjectContent->blockTileMeetingHtml($meeting_node, $meeting_share_link = FALSE, $meeting_snapshot_link = FALSE);

    $form['form_info'] = [
      '#type' => 'item',
      '#title' => $meeting_tile_html,
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Evaluation Title'),
      '#maxlength' => 255,
      '#size' => 64,
      '#default_value' => 'Evaluation for meeting ' . $meeting_node->id(),
      '#required' => TRUE,
    ];

    $form['meeting_nid'] = [
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

    // When this is set to false, the submit method gets no results through getValues().
    $form['#tree'] = TRUE;

    //
    $form['reactset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Questions Group'),
    ];

    foreach ($question_terms as $question_tid => $question_term) {
      $form['reactset'][$question_tid] = $this->_getEvaluationQuestionElement($question_term);
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
    $reactset = $this->_convertQuestionReactsetValue($form, $form_state);
    $field_array = $this->_generateEvaluationFieldsValue($form_state, $reactset);
    \Drupal::service('flexinfo.node.service')->entityCreateNode($field_array);
  }

  /**
   *
   */
  public function _convertQuestionReactsetValue($form, $form_state = NULL) {
    $output = [];

    $reactset_values = $form_state->getValue('reactset');
    // dpm($reactset_values);
    // dpm($form_state['meeting_nid']);

    if ($reactset_values) {
      foreach ($reactset_values as $key => $row) {
        if ($row) {
          if (is_array($row)) {
            foreach ($row as $subrow) {
              if ($subrow) {
                $answer_row = [
                  'question_tid' => $key,
                  'question_answer' => $subrow,
                  'refer_uid' => $form['reactset'][$key]['#refer_value']['refer_uid'],
                  'refer_tid' => $form['reactset'][$key]['#refer_value']['refer_tid'],
                  'refer_other' => $form['reactset'][$key]['#refer_value']['refer_other'],
                ];
                $output[] = $answer_row;
              }
            }
          }
          else {
            $answer_row = [
              'question_tid' => $key,
              'question_answer' => $row,
              'refer_uid' => $form['reactset'][$key]['#refer_value']['refer_uid'],
              'refer_tid' => $form['reactset'][$key]['#refer_value']['refer_tid'],
              'refer_other' => $form['reactset'][$key]['#refer_value']['refer_other'],
            ];

            if ($answer_row) {
              $output[] = $answer_row;
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
   * Number Integer question.
   */
  public function _getEvaluationQuestionElement($question_term) {
    $field_type = \Drupal::service('flexinfo.field.service')
      ->getFieldFirstTargetIdTermName($question_term, 'field_queslibr_fieldtype');

    if ($field_type == 'radios') {
      $output = $this->_getElementNumberDropdown($question_term);
    }
    elseif ($field_type == 'selectkey') {
      $output = $this->_getElementSelectkey($question_term);
    }
    elseif ($field_type == 'textfield') {
      $output = $this->_getElementTextfield($question_term);
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
  public function _getElementNumberDropdown($question_term) {
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
      "#empty_option"=> $this->t('- Select -'),
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
  public function _getElementSelectkey($question_term) {
    $all_answer_terms =\Drupal::service('flexinfo.field.service')
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
      "#empty_option"=> $this->t('- Select -'),
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
   * Selectkey question.
   */
  public function _getElementTextfield($question_term) {
    $output = [
      '#title' => $question_term->getName(),
      '#title_display' => 'before',
      '#type' => 'textfield',
      '#refer_value' => [
        'refer_uid' => NULL,
        'refer_tid' => NULL,
        'refer_other' => NULL,
      ],
      '#attributes' => [
        'class' => ['new-demo-class'],
        // 'data-toggle' => ['tooltip'],
        // 'data-original-title' => ['tooltip text'],
      ],
    ];

    return $output;
  }

}
