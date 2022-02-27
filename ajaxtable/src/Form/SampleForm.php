<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;

/**
 * Class SampleForm.
 * https://www.webomelette.com/ajax-elements-drupal-form-tables
 */
class SampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_sample_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#id'] = $form['#id'] ?? Html::getId('test');

    $rows = [];
    $row = [
      $this->t('Row label'),
      []
    ];

    $rows[] = $row;

    $form['buttons'] = [
      [
        '#type' => 'button',
        '#value' => $this->t('Edit'),
        '#submit' => [
          [$this, 'editButtonSubmit'],
        ],
        '#executes_submit_callback' => TRUE,
        // Hardcoding for now as we have only one row.
        '#edit' => 0,
        '#ajax' => [
          'callback' => [$this, 'ajaxCallback'],
          'wrapper' => $form['#id'],
        ]
      ],
    ];

    $form['table'] = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => [$this->t('Title'), $this->t('Operations')],
    ];

    $form['#pre_render'] = [
      [$this, 'preRenderForm'],
    ];


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function editButtonSubmit(array &$form, FormStateInterface $form_state) {
    $element = $form_state->getTriggeringElement();
    $form_state->set('edit', $element['#edit']);
    $form_state->setRebuild();
  }

  /**
   * Prerender callback for the form.
   *
   * Moves the buttons into the table.
   *
   * @param array $form
   *   The form.
   *
   * @return array
   *   The form.
   */
  public function preRenderForm(array $form) {
    foreach (Element::children($form['buttons']) as $child) {
      // The 1 is the cell number where we insert the button.
      $form['table']['#rows'][$child][1] = [
        'data' => $form['buttons'][$child]
      ];
      unset($form['buttons'][$child]);
    }

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
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

}
