
<?php

namespace Drupal\navinfo\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @todo use form
 $form = \Drupal::formBuilder()->getForm('Drupal\navinfo\Form\EventtypeFilterForm');
 */

/**
 * Implements an example form.
 */
class EventtypeFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'eventtype_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $terms = \Drupal::getContainer()
      ->get('flexinfo.term.service')
      ->getFullTermsFromVidName('eventtype');
    foreach ($terms as $term) {
      $image_file = strip_tags(strtolower($term->getDescription())) . '.png';
      $image_file = str_replace("\r\n", "", $image_file);

      $image_render_array = [
        '#theme' => 'image_style',
        '#style_name' => 'thumbnail',
        '#uri' => 'public://images/' . $image_file,
        '#width' => 30,
        '#height' => 26,
        '#attributes' => array(
          'class' => array('float-left', 'margin-top-2', 'height-16'),
        ),
        // optional parameters
      ];

      $row = '';
      $row .= '<li class="province-filter-option-wrapper display-inline-block">';
        $row .= '<span class="display-inline-block float-left margin-right-6">';
          $row .= $term->getDescription();
        $row .= '</span>';
        $row .= render($image_render_array);
      $row .= '</li>';
      $options[$term->id()] = $row;
    }

    $user_default_provinces = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_term_eventtype');
    if (!$user_default_provinces) {
      $user_default_provinces = [];
    }
    $form['eventtype_selection'] = array(
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => $user_default_provinces,
      '#title' => '',
      '#attributes' => array('class' => array('display-inline-block', 'float-left', 'margin-left-24')),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => -100,
      '#attributes' => array('class' => array('display-inline-block', 'float-left', 'margin-left-24')),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (array_sum($form_state->getValue('eventtype_selection')) < 1) {
      // $form_state->setErrorByName('phone_number', $this->t('Your eventtype selection is empty. Please selct a eventtype.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $filter_result = $this->filterEventtypeResult($form_state->getValue('eventtype_selection'));

    \Drupal::service('user.data')->set('navinfo', \Drupal::currentUser()->id(), 'default_term_eventtype', $filter_result);

    if (\Drupal::currentUser()->id() == 1) {
      drupal_set_message($this->t('Your eventtype is @number', ['@number' => implode(", ", $filter_result)]));
    }
  }

  /**
   * @param is $raw_array like array(58, 0, 0, 0, 0, 0, 0, 0, 0, 0)
   * remove all empty array elements
   * @return is $raw_array like array(58)
   */
  public function filterEventtypeResult($raw_array = array()) {
    $output = array_filter($raw_array);
    return $output;
  }

}