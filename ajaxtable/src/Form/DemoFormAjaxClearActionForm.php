<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;

/**
 * Class DemoForm.
 * https://www.drupal.org/node/717742
 * submitForm() or validateForm(). Both of those functions are run before ajax callback function
*
 * https://drupal.stackexchange.com/questions/220185/clear-form-fields-after-ajax-submit
 * Ajax清除form的原理
   You are looking for https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Form%21FormStateInterface.php/function/FormStateInterface%3A%3AsetUserInput/8.2.x.
  The values are the validated input, for setting the default values, the form system respects the original user input, which you can change with the method above.
  However, for entity forms, it is a bit more complicated. $this->entity is a reference to the entity, you will still have that and it will fallback to that. What you probably need to do is create a new entity in save, assign it to $this->entity, then empty user input and $form_state->setRebuild(). That should then rebuild the form with a new and empty entity.
 */
class DemoFormAjaxClearActionForm extends FormBase {

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
    $form['wrapper'] = array(
      '#type' => 'container',
      '#attributes' => array('id' => 'data-wrapper'),
    );
    $form['wrapper']['columnNum'] = [
      '#title'   => t('Number of Columns，其它的ajax调用'),
      '#type'    => 'select',
      '#options' => [1 => '1', 2 => '2'],
      // '#default_value' => $this->configuration['columnNum'],
      '#default_value' => 1,
      '#ajax'          => [
        'callback'   => '::columnCallback',
        'wrapper'    => 'data-wrapper',
      ],
    ];

    $form['country'] = array(
      '#type' => 'textfield',
      '#title' => "Country",
      '#description' => 'Ajax Reset button do not reset heree',
    );

    // Adds a new button to clear the form. The #validate property
    // directs the form to use a new validation handler function in place
    // of the default.
    $form['reset_wrapper'] = array(
      '#type' => 'container',
      '#attributes' => array('id' => 'data-reset-wrapper'),
    );
    $form['reset_wrapper']['city'] = array(
      '#type' => 'textfield',
      '#title' => "City",
      '#default_value' => "ping",
      '#description' => 'City Name, Ajax Reset button do not reset here',
    );

    $form['reset_wrapper']['town'] = array(
      '#type' => 'textfield',
      '#title' => "Town",
      '#description' => 'Town Name',
    );
    $form['reset_wrapper']['clear'] = array(
      '#type' => 'submit',
      '#value' => 'Reset Button',
      '#ajax'  => [
        'callback' => '::resetCallback',
        'wrapper'  => 'data-reset-wrapper',
      ],
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function columnCallback(array &$form, FormStateInterface $form_state) {
    $form_state->setValue('city', 'Bei');

    \Drupal::messenger()->addMessage('Ajax callback from option  - ' .  $form_state->getValues()['columnNum']);

    return $form['wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function resetCallback(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Ajax callback from resetCallback()');


    return $form['reset_wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);

    $this->clearFormInput($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    \Drupal::messenger()->addMessage('Submit callback from submitForm()');


    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

  /**
   * Clears form input.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * https://drupal.stackexchange.com/questions/220185/clear-form-fields-after-ajax-submit
   * 在validateForm() 或 submitForm() 调用这个clearFormInput()来清除form
   */
  protected function clearFormInput(array $form, FormStateInterface $form_state) {
    // Replace the form entity with an empty instance.

    // Clear user input.
    $input = $form_state->getUserInput();
    // We should not clear the system items from the user input.
    $clean_keys = $form_state->getCleanValueKeys();
    $clean_keys[] = 'ajax_page_state';
    foreach ($input as $key => $item) {
      if (!in_array($key, $clean_keys) && substr($key, 0, 1) !== '_') {
        unset($input[$key]);
      }
    }

    // Sets the form values as though they were submitted by a user.
    $form_state->setUserInput($input);

    // Rebuild the form state values.
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   *
   * Resets default values for a new entity.
   * https://drupal.stackexchange.com/questions/220185/clear-form-fields-after-ajax-submit
   * Update 08.03.2021
      If you have a created date field in the entity form, it will be cached in $form_state after submission. You will have to reset it's value for the entities. You can do it by overriding the \Drupal\Core\Entity\EntityForm::processForm:
   */
  public function processForm($element, FormStateInterface $form_state, $form) {
    $element = parent::processForm($element, $form_state, $form);

    if ($this->entity->isNew()) {
      $this->entity->set('created', $this->time->getRequestTime());
    }

    return $element;
  }

}

