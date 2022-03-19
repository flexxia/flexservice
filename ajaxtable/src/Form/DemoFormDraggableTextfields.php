<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Html;

/**
 * Class DemoFormMultiUnlimitedTextfieldsForm.
 * https://drupal.stackexchange.com/a/290222
 */
class DemoFormDraggableTextfields extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ajaxtable_example_form_draggable_textfields';
  }

  /**
   * Form with weight.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('This example shows a draggable table and save weights.'),
    ];

    $form['radio_one'] = [
      '#type' => 'radios',
      '#title' => $this->t('Pick One colour'),
      '#options' => [
        'Floral' => $this->t('Floral'),
        'Lemon' => $this->t('Lemon'),
      ],
      '#default_value' => 'Floral',
    ];

    $form['table_set'] = [
      '#type' => 'table',
      '#header' => [
        '',
        $this->t('Weight'),
        $this->t('Country'),
        $this->t('Continents'),
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

    $countrys = [
      ['Canada', 'Asia'],
      ['China', 'North America'],
      ['Spain', 'Europe'],
    ];
    foreach ($countrys as $key=> $country_name) {
      $weight = $key;

      $form['table_set'][$key] = [
        'data' => [],
      ];
      $form['table_set'][$key]['#attributes']['class'] = ['draggable'];

      // type is weight
      $form['table_set'][$key]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight'),
        '#title_display' => 'invisible',
        '#default_value' => $weight + 20,
        '#attributes' => [
          'class' => [
            'draggable-weight'
          ]
        ],
      ];
      $form['table_set'][$key]['label'] = [
        '#markup' => $country_name[0],
      ];
      $form['table_set'][$key]['continents'] = [
        '#markup' => $country_name[1],
      ];
    }

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Final submit handler.
   *
   * Reports what values were finally set.
   *
   * Display result.
   foreach ($form_state->getValues() as $key => $value) {
     \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
   }
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue(['radio_one', 'table_set']);
    foreach ($form_state->getValues() as $key => $value) {
      dpm($value);
      // \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }

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

}
