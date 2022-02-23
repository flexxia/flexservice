<?php

namespace Drupal\ajaxtable\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Html;

/**
 * Class DemoFormMultiUnlimitedTextfieldsForm.
 * https://drupal.stackexchange.com/a/290222
 */
class DemoFormMultiUnlimitedTextfieldsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_api_example_ajax_addmore_multiple_textfield';
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
   * formMultipleElements Textfields
   */
  public function getmFormMultipleElements(array &$form, FormStateInterface $form_state) {
    $elements = [];

    $variable = [
      'country',
      'city',
      'town',
    ];

    foreach ($variable as $key => $value) {
      $elements += $this->getmMultipleElement($form, $form_state, $value);
    }

    return $elements;
  }

  /**
   *
   */
  public function getmMultipleElement(array &$form, FormStateInterface $form_state, $field_name = NULL) {
    $wrapper_id = Html::getUniqueId('field-' . $field_name . '-add-more-wrapper');
    $fieldset_key = $field_name . '_fieldset';
    $field_name_key = $field_name . '_name';
    $num_rows_key = $field_name . '_num_rows';


    // Gather the number of names in the form already.
    // Num key is set in the addOneMoreFn() and removeOneFn().
    $num_rows = $form_state->get($num_rows_key);
    // We have to ensure that there is at least one name field.
    if ($num_rows === NULL) {
      $name_field = $form_state->set($num_rows_key, 1);
      $num_rows = 1;
    }

    $element[$fieldset_key] = [
      // '#type' => 'container',
      '#type' => 'fieldset',
      '#title' => $this->t('People coming to ') . ucfirst($field_name) . ' picnic',
      '#prefix' => '<div id="' . $wrapper_id . '">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_rows; $i++) {
      $element[$fieldset_key][$field_name_key][$i] = [
        '#type' => 'textfield',
        '#title' => ucfirst($field_name) . " (Value ". ($i + 1) . ")",
      ];
    }

    $element[$fieldset_key]['actions'] = [
      '#type' => 'actions',
    ];
    $element[$fieldset_key]['actions']['add_one_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => [[get_class($this), 'addOneMoreFn']],
      '#ajax' => [
        // 'callback' => '::addMoreAjaxCallback',
        'callback' => [get_class($this), 'addMoreAjaxCallback'],
        'wrapper' => $wrapper_id,
      ],
      // 当有多个按钮, 使用一个callback必须定义 Unique'#name'，否则多个 getTriggeringElement() 只返回最后一个element，
      '#name' => $field_name_key,
      '#num_rows_key' => $num_rows_key,
    ];

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
    $id = $form_state->getTriggeringElement()['#parents'][0];        // Return ?.
    $id = $form_state->getTriggeringElement()['#id'];           // Form Id.

    $button = $form_state->getTriggeringElement();

    $fieldset_name = $form_state->getTriggeringElement()['#array_parents'][0];  // Return fieldset name.
    $fieldset_name1 = $form_state->getTriggeringElement()['#array_parents'][1];  // Return fieldset name.

    $form['country_fieldset']['#title'] .= " - " .  $fieldset_name1 . " - " .  count($button['#array_parents']);
    $form['city_fieldset']['#title'] .= " - " .  $fieldset_name1. " - " .  $id;
    $form['town_fieldset']['#title'] .= " - " .  $fieldset_name1. " - " .  $id;

    return $form[$fieldset_name];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOneMoreFn(array &$form, FormStateInterface $form_state) {
    $num_rows_key = $form_state->getTriggeringElement()['#num_rows_key'];
    $name_field = $form_state->get($num_rows_key);
    $add_button = $name_field + 1;
    $form_state->set($num_rows_key, $add_button);

    // Since our buildForm() method relies on the value of "$num_rows_key" to
    // generate 'name' form elements, we have to tell the form to rebuild. If we
    // don't do this, the form builder will not call buildForm().

    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeOneFn(array &$form, FormStateInterface $form_state) {
    $num_rows_key = $form_state->getTriggeringElement()['#num_rows_key'];
    $name_field = $form_state->get($num_rows_key);
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set($num_rows_key, $remove_button);
    }

    // Since our buildForm() method relies on the value of "$num_rows_key" to
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
