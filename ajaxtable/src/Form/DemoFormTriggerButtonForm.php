<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;

/**
 * Class DemoForm.
 * https://www.webomelette.com/ajax-elements-drupal-form-tables
 */
class DemoFormTriggerButtonForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_trigger_button_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#id'] = $form['#id'] ?? Html::getId('test');

    // Email pattern.
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#pattern' => '*@example.com',
    ];

    // Textfield pattern.
    $form['postalcode'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Postal Code'),
      '#size' => 60,
      '#maxlength' => 128,
      '#pattern' => 'some-prefix-[a-z]+',
    );

    //
    $form['country_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('People coming to country'),
      '#prefix' => '<div id="country-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['country_fieldset']['add_one_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => ['::addOneMoreCountry'],
      '#ajax' => [
        'callback' => '::addMoreAjaxCallback',
        'wrapper' => 'country-fieldset-wrapper',
      ],
      // 当有多个按钮, 使用一个callback必须定义'#name'，否则多个 getTriggeringElement() 只返回最后一个element，
      '#name' => 'country_button',
    ];

    // City.
    $form['city_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('People coming to city'),
      '#prefix' => '<div id="city-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['city_fieldset']['add_one_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => ['::addOneMoreCountry'],
      '#ajax' => [
        'callback' => '::addMoreAjaxCallback',
        'wrapper' => 'city-fieldset-wrapper',
      ],
      // 当有多个按钮, 使用一个callback必须定义'#name'，否则多个 getTriggeringElement() 只返回最后一个element，
      '#name' => 'city_button',
    ];

    // Submit.
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this
        ->t('Save'),
    );

    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addMoreAjaxCallback(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getTriggeringElement()['#name'];         // Return element's #name
    $id = $form_state->getTriggeringElement()['#value'];        // Return element's #value.
    $id = $form_state->getTriggeringElement()['#id'];           // Form Id.
    $id = $form_state->getTriggeringElement()['#parents'][0];        // Return ?.
    $fieldset_name = $form_state->getTriggeringElement()['#array_parents'][0];  // Return fieldset name.

    $form['country_fieldset']['#title'] .= " - " .  $fieldset_name . " - " .  $id;
    $form['city_fieldset']['#title'] .= " - " .  $fieldset_name. " - " .  $id;

    return $form[$fieldset_name];
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
