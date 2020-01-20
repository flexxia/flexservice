<?php

namespace Drupal\ngpage\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @todo use form
 $form = \Drupal::formBuilder()->getForm('Drupal\ngpage\Form\DrupalSampleForm');
 */

/**
 * Implements an example form.
 */
class DrupalSampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ngpage_drupal_sample_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['default_sample'] = array(
      '#type' => 'checkbox',
      '#title' => 'Drupal Sample Form',
      '#attributes' => array('class' => array('display-inline-block', 'float-left', 'margin-left-24')),
    );
    $form['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Tel Phone'),
    ];
    $form['copy'] = array(
      '#type' => 'checkbox',
      '#title' => $this
        ->t('Send me a copy'),
    );
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

}
