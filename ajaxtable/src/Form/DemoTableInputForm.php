<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DemoTableInputForm.
 * https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Element%21Table.php/class/Table/8.8.x
 */
class DemoTableInputForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_table_input_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['contacts'] = array(
      '#type' => 'table',
      '#caption' => $this->t('Sample Input Table'),
      '#header' => array(
        $this->t('Name'),
        $this->t('Phone'),
        $this->t('Checkboxes'),
      ),
    );
    for ($i = 1; $i <= 4; $i++) {
      $form['contacts'][$i]['#attributes'] = array(
        'class' => array(
          'foo',
          'baz',
        ),
      );
      $form['contacts'][$i]['name'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Name'),
        '#title_display' => 'invisible',
      );
      $form['contacts'][$i]['phone'] = array(
        '#type' => 'tel',
        '#title' => $this->t('Phone'),
        '#title_display' => 'invisible',
      );
      $form['contacts'][$i]['checkbox'] = array(
        '#type' => 'checkboxes',
        '#options' => [1 => 'One', 2 => 'Two'],
        '#title_display' => 'invisible',
      );
    }
    $form['contacts'][]['colspan_example'] = array(
      '#plain_text' => 'Colspan Example',
      '#wrapper_attributes' => array(
        'colspan' => 2,
        'class' => array(
          'foo',
          'bar',
        ),
      ),
    );

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

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
