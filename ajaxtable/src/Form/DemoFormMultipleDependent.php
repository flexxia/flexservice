<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;

/**
 * Class DemoForm.
 */
class DemoFormMultipleDependent extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_form_multiple_dependent';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // $form['#tree'] = TRUE;
    // $form['#tree'] = FALSE;

    $form['description'] = [
      '#type' => 'item',
      '#markup' => 'Checkboxes based on Dropdown option and other Checkboxes, 多个field控制一个field',
    ];

    $form += $this->getmFormElementsSelectOne($form, $form_state);

    $form['submit'] = [
     '#type' => 'submit',
     '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * 多个选项 控制一个Field
   */
  public function getmFormElementsSelectOne(array &$form, FormStateInterface $form_state) {
    $selected_region = NULL;
    $selected_language = NULL;
    if ($form_state->getValues()) {
      $selected_region = $form_state->getValues()['region'];
      $selected_language = $form_state->getValues()['language'];
    }

    // 用ID定义一个统一的wrapper
    $form['term_group'] = [
      '#type' => 'fieldset',
      '#attributes' => ['id' => 'load-wrapper-group'],
    ];

    // 定义一个统一的 callback - ajax_dependent_callback
    $form['term_group']['region'] = [
      '#type' => 'select',
      '#title' => $this->t('Region'),
      "#empty_option"=>t('- Select -'),
      '#options' => [
        'as' => "Asian",
        'eu' => "Europe",
      ],
      '#ajax' => [
        'callback' => [get_class($this), 'ajax_dependent_callback'],
        'wrapper' => 'load-wrapper-group',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => t('Fetching content...'),
        ],
      ],
    ];

    $form['term_group']['language'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Language') . ' 请只选择一个',
      '#options' => $this->_getLanguageOptions($selected_region),
      "#empty_option"=>t('- Select -'),
      '#ajax' => [
        'callback' => [get_class($this), 'ajax_dependent_callback'],
        'wrapper' => 'load-wrapper-group'
      ],
    );

    $form['term_group']['country'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Country'),
      '#options' => $this->_getCountryOptions($selected_region, $selected_language),
      "#empty_option"=>t('- Select -'),
    );

    return $form;
  }

  /**
   * Ajax dependent select callback.
   * 用一个统一的callback 返回所有的field
   */
  public function ajax_dependent_callback(array &$form, FormStateInterface $form_state) {
    return $form['term_group'];
  }

  /**
   *
   */
  public function _getLanguageOptions($selected_region) {
    $output = [
      'en' => 'English',
      'fr' => 'French',
      'ch' => 'Chinese',
      'jp' => 'Janpanese',
    ];

    if ($selected_region == 'eu') {
      $output = [
        'en' => 'English',
        'fr' => 'French',
      ];
    }
    else if ($selected_region == 'as') {
      $output = [
        'ch' => 'Chinese',
        'jp' => 'Janpanese',
      ];
    }

    return $output;
  }

  /**
   *
   */
  public function _getCountryOptions($selected_region, $selected_language) {
    \Drupal::messenger()->addMessage('$selected_region : ' . json_encode($selected_region));
    \Drupal::messenger()->addMessage('$selected_language : ' . json_encode($selected_language));

    $output = [
      'England',
      'France',
      'Ireland',
      'Japan',
      'China',
      'Singapore',
    ];

    if ($selected_region == 'eu') {
      $output = [
        'England',
        'France',
        'Ireland',
      ];
    }
    else if ($selected_region == 'as') {
      $output = [
        'Japan',
        'China',
        'Singapore',
      ];
    }

    if ($selected_language) {
      if ($selected_language['en']) {
        $output = [
          'England',
          'Ireland',
        ];
      }
      else if ($selected_language['fr']) {
        $output = [
          'France',
        ];
      }
      else if ($selected_language['ch']) {
        $output = [
          'China',
          'Singapore',
        ];
      }
      else if ($selected_language['jp']) {
        $output = [
          'Japan',
        ];
      }
    }


    return $output;
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
