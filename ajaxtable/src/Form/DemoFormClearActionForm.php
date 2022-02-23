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
class DemoFormClearActionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_form_clear_action_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['student_name'] = array(
      '#type' => 'fieldset',
      '#title' => t('Student_name'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    );

    // Removes the #required property and
    // uses the validation function instead.
    $form['student_name']['first'] = array(
      '#type' => 'textfield',
      '#title' => t('First name'),
      '#default_value' => "First name",
      '#description' => "Please enter your first name.",
      '#size' => 20,
      '#maxlength' => 20,
    );

    // Removes the #required property and
    // uses the validation function instead.
    $form['student_name']['last'] = array(
      '#type' => 'textfield',
      '#title' => t('Last name'),
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
    );
    // Adds a new button to clear the form. The #validate property
    // directs the form to use a new validation handler function in place
    // of the default.
    $form['clear'] = array(
      '#type' => 'submit',
      '#value' => 'Reset form',
      '#validate' => ['::my_module_my_form_clear'],
    );

    return $form;
  }

  // This is the new validation handler for our Reset button. Setting
  // the $form_state['rebuild'] value to TRUE, clears the form and also
  // skips the submit handler.
  public function my_module_my_form_clear(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Rebuild form33');

    // the following internal values are removed by cleanValues().
    // form_id
    // form_token
    // form_build_id
    // op
    // cleanValues() 会清除掉form_id 等form的基本属性值。要小心使用
    // $form_state->cleanValues();

    $form_state->setRebuild(FALSE);
    // $form_state->setValue('last', 1910);

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

    \Drupal::messenger()->addMessage('validateForm fn');

    // Provides the first and last name fields to be required by using the
    // validation function to make sure values have been entered. This
    // causes the name fields to show up in red if left blank after clearing
    // the form with the "Reset form" button.
    $first_name = $form_state->getValues()['first'];
    $last_name = $form_state->getValues()['last'];
    if (!$first_name) {
      $form_state->setErrorByName('first', t('Please enter your first name.'));
    }
    if (!$last_name) {
      $form_state->setErrorByName('last', t('Please enter your last name.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('submitForm fn');

    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

}

