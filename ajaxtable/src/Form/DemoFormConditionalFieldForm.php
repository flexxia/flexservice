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
class DemoFormConditionalFieldForm extends FormBase {

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
      '#markup' => 'https://www.drupal.org/docs/drupal-apis/form-api/conditional-form-fields',
    ];

    $form += $this->getmFormElementsRadioOne($form, $form_state);
    $form += $this->getmFormElementsRadioTwo($form, $form_state);
    $form += $this->getmFormElementsRadioThree($form, $form_state);

    $form['submit'] = [
     '#type' => 'submit',
     '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * Radio 选项隐藏或显示下一个Field
   * show this textfield only if the radio 'other' is selected above
   */
  public function getmFormElementsRadioOne(array &$form, FormStateInterface $form_state) {
    $form['radio_one'] = array(
      '#type' => 'fieldset',
      '#title' => t('Radio checked to show or hide textfield'),
    );
    $form['radio_one']['colour_zero'] = [
      '#type' => 'radios',
      '#title' => $this->t('Pick One colour'),
      '#options' => [
        'Floral' => $this->t('Floral'),
        'Lemon' => $this->t('Lemon'),
      ],
    ];
    // 'colour_one' 字段必须和下面的两个name一致，否则提交的时候得不到值
    $form['radio_one']['colour_one'] = [
      '#type' => 'radios',
      '#title' => $this->t('两个option，都可以显示textfield'),
      '#options' => [
        'Floral' => $this->t('Floral'),
        'Lemon' => $this->t('Lemon'),
        'other' => $this->t('Other'),
        'custom' => $this->t('Custom colour'),
      ],
      '#attributes' => [
        // define static name and id so we can easier select it
        'name' => 'colour_one',
      ],
    ];
    $form['radio_one']['colour_text_one'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#placeholder' => 'Enter favourite colour',
      // '#attributes' 好像可以省略
      '#attributes' => [
        'id' => 'custom-colour-one',
      ],
      '#states' => [
        'visible' => [
          // don't mistake :input for the type of field. You'll always use
          //:input here, no matter whether your source is a select, radio or checkbox element.
          ':input[name="colour_one"]' => [
            ['value' => 'other'],
              'or',
            // User selected 'Custom colour'.
            ['value' => 'custom'],
          ],
        ],
      ],
    ];

    return $form;
  }

  /**
   * User can't choose any other colour, after he selected 'Other' and entered a custom
   * favourite colour. To achieve this we also add a #states property to the radios list
   * and tell it, to enable the field only if the value of the custom colour textbox is empty.
   */
  public function getmFormElementsRadioTwo(array &$form, FormStateInterface $form_state) {
    $form['radio_two'] = array(
      '#type' => 'fieldset',
      '#title' => t('Disable color options when other input something, with Radio #state 状态'),
    );
    $form['radio_two']['colour_two'] = [
      '#type' => 'radios',
      '#title' => $this->t('Pick a colour'),
      '#options' => [
        'Floral' => $this->t('Floral'),
        'Lemon' => $this->t('Lemon'),
        'other' => $this->t('Other'),
      ],
      '#attributes' => [
        // define static name and id so we can easier select it
        'name' => 'colour_two',
      ],
      // add the #states property to the radios
      '#states' => [
        'enabled' => [
          // enable the radios only if the custom color textbox is empty
          ':input[name="field_custom_colour"]' => ['value' => ''],
        ],
      ],
    ];

    // this textfield will only be shown when the option 'Other'
    // is selected from the radios above.
    $form['radio_two']['colour_text_two'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#placeholder' => 'Enter favourite colour',
      '#attributes' => [
        // also add static name and id to the textbox
        'id' => 'custom-colour-two',
        'name' => 'field_custom_colour',
      ],
      '#states' => [
        // show this textfield only if the radio 'other' is selected above
        'visible' => [
          ':input[name="colour_two"]' => ['value' => 'other'],
        ],
      ],
    ];

    return $form;
  }

  /**
   *
   *
   */
  public function getmFormElementsRadioThree(array &$form, FormStateInterface $form_state) {
    $form['radio_three'] = array(
      '#type' => 'fieldset',
      '#title' => t('Disable color options when other input something, with Radio #state 状态'),
    );
    $form['radio_three']['colour_select'] = [
      '#type' => 'radios',
      '#title' => $this->t('Pick a colour'),
      '#options' => [
        'blue' => $this->t('Blue'),
        'white' => $this->t('White'),
        'black' => $this->t('Black'),
        'other' => $this->t('Other'),
      ],
      '#attributes' => [
        'name' => 'field_select_colour',
      ],
    ];

    // Create a list of radio boxes that will only allow to select
    // yes or no.
    $form['radio_three']['choice_select'] = [
      '#type' => 'radios',
      '#title' => $this->t('Do you want to enter a custom colour?'),
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
      '#attributes' => [
        'name' => 'field_choice_select',
      ],
    ];

    // This textfield will be shown when either the option 'Other'
    // or 'Custom colour' is selected from the radios above.
    $form['radio_three']['custom_colour'] = [
      '#type' => 'textfield',
      '#size' => '60',
      '#placeholder' => 'Enter favourite colour',
      '#attributes' => [
        // Also add static name and id to the textbox.
        'id' => 'custom-colour',
        'name' => 'field_custom_colour',
      ],
      '#states' => [
        // Show this textfield if the radios 'other' and 'yes' are
        // selected in the fields above.
        'visible' => [
          ':input[name="field_select_colour"]' => ['value' => 'other'],
          'and',
          ':input[name="field_choice_select"]' => ['value' => 'yes'],
        ],
      ],
    ];

    return $form;
  }

  /**
   *
   *
   */
  public function getmFormElementsRadio333(array &$form, FormStateInterface $form_state) {
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
      // dpm(gettype($value));
      // if (isset($form[$key]['#type'])) {
      //   dpm($form[$key]['#type']);
      //   dpm('- - - - -');
      // }
      // if (isset($value['value'])) {
        // \Drupal::messenger()->addMessage('cccc');
      //   \Drupal::messenger()->addMessage($key . ': ' . $value['value']);
      // }
    }
  }

}

