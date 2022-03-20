<?php

namespace Drupal\flexform\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Flex Evaluation Form.
 */
class FlexEvaluationLayoutForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'flex_evaluation_layout_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $evaluation_form_tid = NULL) {
    $evaluation_form_entity = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')->load($evaluation_form_tid);

    if ($evaluation_form_entity && $evaluation_form_entity->getVocabularyId() == 'evaluationform') {
      $evaluation_layout_tids = \Drupal::service('flexinfo.queryterm.service')
        ->wrapperTermTidsByField('evaluationlayout', 'field_evallayout_form', $evaluation_form_tid);
      if ($evaluation_layout_tids) {
        $form = $this->generateExistMessage($form, $form_state, $evaluation_form_entity);
      }
      else {
        $form = $this->generateEvaluationForm($form, $form_state, $evaluation_form_entity);
      }
    }
    else {
      $form = $this->generateEmptyValueForm($form, $form_state);
    }

    return $form;
  }

  /**
   * Exist form.
   */
  public function generateExistMessage(array $form, FormStateInterface $form_state) {
    $form['form_info'] = [
      '#type' => 'item',
      '#title' => 'The Evaluation Layout Exist, Please Modify Existing One.',
    ];

    return $form;
  }

  /**
   * Empty form.
   */
  public function generateEmptyValueForm(array $form, FormStateInterface $form_state) {
    $form['form_info'] = [
      '#type' => 'item',
      '#title' => 'The Evaluation ID is not correct, Please try again.',
    ];

    return $form;
  }

  /**
   * Evaluation Form.
   */
  public function generateEvaluationForm(array $form, FormStateInterface $form_state, $evaluation_form_entity = NULL) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Evaluation Layout Title'),
      '#maxlength' => 255,
      '#size' => 64,
      '#default_value' => 'Evaluation Form Layout - ' . $evaluation_form_entity->getName(),
      '#required' => TRUE,
    ];

    // Storage of internal information.
    $form['evallayout_form_tid'] = [
      '#type' => 'value',
      '#value' => $evaluation_form_entity->id(),
    ];

    $form['evallayout_enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => TRUE,
    ];

    $form += $this->_getEvaluationQuestionSet($evaluation_form_entity);

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
    // $form['#attached']['library'][] = 'flexform/node_add_evaluation_form';

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
    foreach ($form_state->getValues() as $key => $value) {
      // \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }

    $this->_createEvaluationLayoutTerm($form, $form_state);

    // Page redirect.
    // $url_path = '/ngpage/standardterm/page/evaluationform';
    // $url = Url::fromUserInput($url_path);
    // $form_state->setRedirectUrl($url);
  }

  /**
   * {@inheritdoc}
   */
  public function _createEvaluationLayoutTerm($form, $form_state = NULL) {
    $fields_value = $this->_generateTermFieldsValue($form_state);

    \Drupal::service('flexinfo.term.service')->entityCreateTermWithFieldsValue(
      $form_state->getValue('title'),
      'evaluationlayout',
      $fields_value
    );
  }

  /**
   *
   */
  public function _generateTermFieldsValue($form_state = NULL, $reactset = []) {
    $evaluation_form_tid = $form_state->getValue('evallayout_form_tid');
    $enable = $form_state->getValue('evallayout_enable');
    $questionset = $form_state->getValue('evallayout_questionset');

    $output['field_evallayout_form'] = [
      'field_name' => 'field_evallayout_form',
      'value' => [$evaluation_form_tid],
      'vid' => 'evaluationform',
    ];
    $output['field_evallayout_enable'] = [
      'field_name' => 'field_evallayout_enable',
      'value' => [$enable],
    ];
    $output['field_evallayout_questionset'] = [
      'field_name' => 'field_evallayout_questionset',
      'value' => array_keys($questionset),
      'vid' => 'QuestionLibrary',
    ];

    return $output;
  }

  /**
   *
   */
  public function _getEvaluationQuestionSet($evaluation_form_entity = NULL) {
    $output = [];

    $form['evallayout_questionset'] = [
      '#type' => 'table',
      '#header' => [
        '',
        $this->t('Weight'),
        $this->t('Question'),
      ],
      '#attributes' => [
        'id' => 'my-module-table'
      ],
      '#tabledrag' => [[
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'draggable-weight',
      ]],
    ];

    $question_terms = \Drupal::service('flexinfo.field.service')
      ->getFieldAllTargetIdsEntitys($evaluation_form_entity, 'field_evaluationform_questionset');
    if ($question_terms) {
      $weight = -10;
      foreach ($question_terms as $question_tid => $question_term) {
        $form['evallayout_questionset'][$question_tid] = [
          'data' => [],
        ];
        $form['evallayout_questionset'][$question_tid]['#attributes']['class'] = ['draggable'];

        // Type is weight.
        $form['evallayout_questionset'][$question_tid]['weight'] = [
          '#type' => 'weight',
          '#title' => t('Weight'),
          '#title_display' => 'invisible',
          '#default_value' => $weight,
          '#attributes' => [
            'class' => [
              'draggable-weight'
            ]
          ],
        ];
        $form['evallayout_questionset'][$question_tid]['label'] = [
          '#markup' => $question_term->getName() . ' - ' . $question_tid,
        ];

        $weight++;
      }
    }

    return $form;
  }

}
