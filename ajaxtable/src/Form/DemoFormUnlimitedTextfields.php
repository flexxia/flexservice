<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\Element;

/**
 * Class DemoFormUnlimitedTextfieldsForm.
 * https://drupal.stackexchange.com/a/290222
 */
class DemoFormUnlimitedTextfields extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_api_example_ajax_addmore_textfield';
  }

  /**
   * Form with 'add more' and 'remove' buttons.
   *
   * This example shows a button to "add more" - add another textfield, and
   * the corresponding "remove" button.
   *
   * @see function formMultipleElements()
   * core/lib/Drupal/Core/Field/WidgetBase.php.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('This example shows an add-more and a remove-last button.'),
    ];

    // When this is set to false, the submit method gets no results through getValues().
    $form['#tree'] = TRUE;

    $form += $this->getmFormMultipleElements($form, $form_state);

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * FormMultipleElements Textfields.
   */
  public function getmFormMultipleElements(array &$form, FormStateInterface $form_state) {
    $elements = [];

    $variable = [
      'country',
    ];

    foreach ($variable as $key => $value) {
      $elements += $this->getmMultipleElementCity($form, $form_state);
      $elements += $this->getmMultipleElementCountry($form, $form_state);
    }

    return $elements;
  }

  /**
   *
   */
  public function getmMultipleElementCity(array &$form, FormStateInterface $form_state, $field_name = NULL) {
    // Gather the number of names in the form already.
    // Num key is set in the addOneMoreCity() and removeCallbackCity().
    $num_city = $form_state->get('num_city');
    // We have to ensure that there is at least one name field.
    if ($num_city === NULL) {
      $name_field = $form_state->set('num_city', 1);
      $num_city = 1;
    }

    $element['city_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('People coming to picnic'),
      '#prefix' => '<div id="city-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_city; $i++) {
      $element['city_fieldset']['country_name'][$i] = [
        '#type' => 'textfield',
        '#title' => $this->t('City Name') . " (Value ". ($i + 1) . ")",
      ];
    }

    $element['city_fieldset']['actions'] = [
      '#type' => 'actions',
    ];
    $element['city_fieldset']['actions']['add_one_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => ['::addOneMoreCity'],
      '#ajax' => [
        'callback' => '::addMoreAjaxCallback',
        'wrapper' => 'city-fieldset-wrapper',
      ],
      // 当有多个按钮, 使用一个callback必须定义 Unique '#name'，否则多个 getTriggeringElement() 只返回最后一个element，
      '#name' => 'city_button',
    ];

    return $element;
  }

  /**
   *
   */
  public function getmMultipleElementCountry(array &$form, FormStateInterface $form_state, $field_name = NULL) {
    // Gather the number of names in the form already.
    // Num key is set in the addOneMoreCountry() and removeCallbackCountry().
    $num_country = $form_state->get('num_country');
    // We have to ensure that there is at least one name field.
    if ($num_country === NULL) {
      $name_field = $form_state->set('num_country', 1);
      $num_country = 1;
    }

    $element['country_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('People coming to picnic'),
      '#prefix' => '<div id="country-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_country; $i++) {
      $element['country_fieldset']['country_name'][$i] = [
        '#type' => 'textfield',
        '#title' => $this->t('Country Name') . " (Value ". ($i + 1) . ")",
      ];
    }

    $element['country_fieldset']['actions'] = [
      '#type' => 'actions',
    ];
    $element['country_fieldset']['actions']['add_one_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more Country'),
      '#submit' => ['::addOneMoreCountry'],
      '#ajax' => [
        'callback' => '::addMoreAjaxCallback',
        'wrapper' => 'country-fieldset-wrapper',
      ],
      // 当有多个按钮, 使用一个callback必须定义'#name'，否则多个 getTriggeringElement() 只返回最后一个element，
      '#name' => 'country_button',
    ];
    // If there is more than one name, add the remove button.
    if ($num_country > 1) {
      $element['country_fieldset']['actions']['remove_last_one'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove one'),
        '#submit' => ['::removeCallbackCountry'],
        '#ajax' => [
          'callback' => '::addMoreAjaxCallback',
          'wrapper' => 'country-fieldset-wrapper',
        ],
        '#arguments' => 'country_one',
      ];
    }

    return $element;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addMoreAjaxCallback(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getTriggeringElement()['#name'];         // Return element's #name
    $id = $form_state->getTriggeringElement()['#value'];        // Return element's #value.
    $id = $form_state->getTriggeringElement()['#id'];           // Form Id.
    $id = $form_state->getTriggeringElement()['#parents'][0];        // Return ?.
    $fieldset_name = $form_state->getTriggeringElement()['#array_parents'][0];  // Return fieldset name.

    $form['country_fieldset']['#title'] .= " - " .  $fieldset_name . " - " .  $id;
    $form['city_fieldset']['#title'] .= " - " .  $fieldset_name. " - " .  $id;

    return $form[$fieldset_name];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOneMoreCountry(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_country');
    $add_button = $name_field + 1;
    $form_state->set('num_country', $add_button);

    // Since our buildForm() method relies on the value of 'num_country' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().

    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallbackCountry(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_country');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_country', $remove_button);
    }
    // Since our buildForm() method relies on the value of 'num_country' to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().
    $form_state->setRebuild();
  }

  /**
   * Final submit handler.
   *
   * Reports what values were finally set.
   *
   * Display result.
   foreach ($form_state->getValues() as $key => $value) {
     \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
   }
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue(['country_fieldset', 'country_name']);

    $output = $this->t('These people are coming to the picnic: @names', [
        '@names' => implode(', ', $values),
      ]
    );
    $this->messenger()->addMessage($output);
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

}
