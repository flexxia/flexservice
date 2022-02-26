<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;

/**
 * Class DemoForm.
 * https://www.drupal.org/node/717742
 */
class DemoFormConditionalOptionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_form_conditional_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // $form['#tree'] = TRUE;
    // $form['#tree'] = FALSE;

    $form['description'] = [
      '#type' => 'item',
      '#markup' => 'Dropdown based on Radio option',
    ];

    $form += $this->getmFormElementsRadioOne($form, $form_state);
    $form += $this->getmFormElementsSelectOne($form, $form_state);

    $form['submit'] = [
     '#type' => 'submit',
     '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Radio 选项隐藏或显示下一个Field
   */
  public function getmFormElementsSelectOne(array &$form, FormStateInterface $form_state) {
    $form['select_one'] = array(
      '#type' => 'fieldset',
      '#title' => t('Ajax with Wrapper, Select to show or different province'),
    );

    $form['select_one']['select_country'] = [
      '#type' => 'select',
      '#title' => $this->t('两个option，不同的国家显示不同的省'),
      "#empty_option"=>t('- 请选择 -'),
      '#options' => [
        'Usa' => $this->t('Usa'),
        'Canada' => $this->t('Canada'),
      ],
      '#ajax' => [
        'callback' => [get_class($this), 'ajax_dependent_select_callback'],
        'wrapper' => 'load-american-wrapper'
      ],
    ];

    $selected = '';
    if ($form_state->getValues()) {
      $selected = $form_state->getValues()['select_country'];
    }

    $form['select_one']['province'] = array(
      '#type' => 'select',
      '#title' => $this->t('Load province'),
      '#options' =>  $this->_ajax_get_province_dropdown_options($selected),
      "#empty_option"=>t('- 请先选择国家 -'),
      '#prefix' => '<div id="load-american-wrapper">',
      '#suffix' => '</div>',
    );

    return $form;
  }

  function ajax_dependent_select_callback(array &$form, FormStateInterface $form_state) {
    return $form['select_one']['province'];
  }

  function _ajax_get_province_dropdown_options($key = '') {
    $options = [
      'Usa' => [
        'Fla' => 'Fla',
        'Mic' => 'Mic',
      ],
      'Canada' => [
        'ON' => 'ON',
        'BC' => 'BC',
      ],
    ];

    if (isset($options[$key])) {
      return $options[$key];
    }
    else {
      return [];
    }
  }

  /**
   * Radio 选项改变下面的Dropdown
   */
  public function getmFormElementsRadioOne(array &$form, FormStateInterface $form_state) {
    $form['radio_one'] = array(
      '#type' => 'fieldset',
      '#title' => t('Ajax with Wrapper, Radio checked to show or hide textfield'),
    );

    $form['radio_one']['radio_state'] = [
      '#type' => 'radios',
      '#title' => $this->t('两个option，显示不同的国家'),
      '#options' => [
        'Asia' => $this->t('Asia'),
        'Europe' => $this->t('Europe'),
      ],
      '#ajax' => [
        'callback' => [get_class($this), 'ajax_dependent_raido_callback'],
        'wrapper' => 'load-country-wrapper'
      ],
    ];

    $selected = '';
    if ($form_state->getValues()) {
      $selected = $form_state->getValues()['radio_state'];
    }

    $form['radio_one']['country'] = array(
      '#type' => 'select',
      '#title' => $this->t('Load country'),
      '#options' =>  $this->_ajax_get_second_dropdown_options($selected),
      '#empty_option' => '- Please Select -',
      '#prefix' => '<div id="load-country-wrapper">',
      '#suffix' => '</div>',
    );

    return $form;
  }

  function ajax_dependent_raido_callback(array &$form, FormStateInterface $form_state) {
    return $form['radio_one']['country'];
  }

  function _ajax_get_second_dropdown_options($key = '') {
    $options = [
      'Asia' => [
        'Japan' => 'Japan',
        'China' => 'China',
      ],
      'Europe' => [
        'Uk' => 'Uk',
        'France' => 'France',
      ],
    ];

    if (isset($options[$key])) {
      return $options[$key];
    }
    else {
      return [];
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

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('submitForm fn');

    foreach ($form_state->getValues() as $key => $value) {

      // \Drupal::messenger()->addMessage($key . ': ' . $value);
      dpm(($key . ': ' . $value));
    }
  }

}

