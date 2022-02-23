<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;

/**
 * Implementing a ajax form.
 * https://www.youtube.com/watch?v=YbyoY8mUv2A
 * https://github.com/drupal-up/ajax_form_submit
 * https://github.com/drupal-up/ajax_form_submit_js_callback
 */
class DemoTableAjaxInvokeJsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_table_ajax_invoke_js_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['cat_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cat name'),
    ];

    $form['actions'] = [
      '#type' => 'button',
      '#value' => $this->t('Log cat!'),
      '#ajax' => [
        'callback' => '::logSomething',
      ],
    ];

    // implement JS callback
    $form['#attached']['library'][] = 'ajaxtable/loggy';

    return $form;
  }

  /**
   * Setting the message in our form.
   */
  public function logSomething(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    // https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Ajax%21InvokeCommand.php/class/InvokeCommand/8.7.x
    /**
      * Constructs an InvokeCommand object.
      *
      * @param string $selector
      *   A jQuery selector.
      * @param string $method
      *   The name of a jQuery method to invoke.
      * @param array $arguments
      *   An optional array of arguments to pass to the method.
      */
    $response->addCommand(
      new InvokeCommand(NULL, 'loggy', [$form_state->getValue('cat_name')])
    );
    return $response;
  }

  /**
   * Submitting the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
