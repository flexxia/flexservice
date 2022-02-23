<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;

/**
 * Class DemoTableAjaxForm.
 * https://kshitij206.medium.com/how-to-create-a-custom-ajax-form-in-drupal-8-deb07ea98bfd
 *
 * https://github.com/drupal-up/drupalup_simple_form/
 * https://www.youtube.com/watch?v=Mc1oucR4Vak&t=3s
 */
class DemoTableAjaxForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_table_ajax_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['user_email'] = [
      '#type' => 'textfield',
      '#title' => 'User or Email',
      '#description' => 'Please enter in a user or email',
      '#prefix' => '<div id="user-email-result"></div>',
    ];
    $form['actions'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit1'),
      '#ajax' => array(
         'callback' => '::checkUserEmailValidation',
          'progress' => array(
             'type' => 'throbber',
             'message' => NULL,
          ),
      )
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

  public function checkUserEmailValidation(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    // Check if User or email exists or not
    if ($form_state->getValue('user_email')) {
      $text = 'User or Email add to database';
    }
    else {
      $text = 'User or Email is empty';
    }

    $response->addCommand(
      new HtmlCommand('#user-email-result', $text)
    );

    $response->addCommand(
      new HtmlCommand(
        '.search-block-form',
        '<div class="my_top_message">' . t('The results is 9') . '</div>'
      )
    );

    return $response;
  }

}
