<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DemoTableSelectForm.
 * https://www.valuebound.com/resources/blog/how-to-create-form-table-drupal-8
 */
class DemoTableSelectForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'demo_table_select_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $header = [
      'productid' => $this->t('Product Id'),
      'productname' => $this->t('Product Name'),
      'price' => $this->t('Price'),
    ];

    $row[] = [
      'productid' => 'Product 1',
      'productname' =>'Product Name 1',
      'price' => 100,
    ];
    $row[] = [
      'productid' => 'Product 2',
      'productname' =>'Product Name 2',
      'price' => 200,
    ];

    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => $row,
      '#empty' => t('No Product found'),
    ];

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
