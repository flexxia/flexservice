<?php

namespace Drupal\ngpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

use Symfony\Component\HttpFoundation\RedirectResponse;


use Drupal\ngjson\Content\NgjsonExportDataContent;
use Drupal\ngjson\Content\NgjsonObjectContent;

/**
 * Class NgpageController.
 */
class NgpageController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {
    $markup = $this->t('Implement method: hello with parameter(s): ') . $name;
    $markup .= '<div>';
      $markup .= '<span type="button" class="close bootstrap-modal-button" data-dismiss="modal" aria-hidden="true">Hello Html Pape</span>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   *
   * @return string
   */
  public function questionEvaluationForms($entity_id) {
    $NgjsonObjectContent = new NgjsonObjectContent();
    $getEvaluationforms = $NgjsonObjectContent->questionEvaluationformsPageContent($entity_id);

    $markup = '<div class="margin-left-12">';
        $markup .= $getEvaluationforms;
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

    return $build;
  }

  /**
   * @return string
   */
  public function exportTableDataToExcel() {
    $markup = '<div>';
      $markup .= '<span type="button" class="bootstrap-modal-button">';
        $markup .= 'Excel Download Successful';
      $markup .= '</span>';
    $markup .= '</div>';

    $build = array(
      '#type' => 'markup',
      '#markup' => $markup,
    );

    $NgjsonExportDataContent = new NgjsonExportDataContent();
    $tbody_data = $NgjsonExportDataContent->standardDataTemplate();

    $header = array_keys(current($tbody_data));

    $flip_tbody_data = \Drupal::service('ngdata.office.excel')->flipArrayRowToColumn($tbody_data);
    \Drupal::service('ngdata.office.excel')->createPHPExcelObject($header, $flip_tbody_data);

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function ngPrimengForm($section, $entity_id, $start, $end) {
    return $this->ngPrimengPage($section, $entity_id, $start, $end);
  }

  /**
   * {@inheritdoc}
   */
  public function ngPrimengPage($section, $entity_id, $start, $end) {
    $admin_tags = \Drupal::service('flexinfo.setting.service')->adminTag();
    array_push($admin_tags, 'app-root');

    $build = array(
      '#type' => 'html_tag',
      '#tag' => 'app-root', // Selector of the your app root component from the Angular app
      // '#type' => 'markup',
      // '#type' => 'inline_template',
      // '#template' => '{{ somecontent }}',
      // '#context' => [
      //   'somecontent' => $somecontent
      // ]

      '#allowed_tags' => $admin_tags,
      '#attached' => [
        'library' => [
          // 'ngpage/primeng_app', // To load the library only with this block
          'ngpage/primeng7_app', // To load the library only with this block
          'ngpage/bootstrap_slider',
          'ngpage/html2canvas_save_png',
          // 'ngpage/bootstrap_modal_template',
        ],
      ],
    );

    return $build;
  }

  /**
   *
   */
  public function ngGuestpage($section, $entity_id, $start, $end) {
    return $this->ngPrimengPage($section, $entity_id, $start, $end);
  }

  /**
   * {@inheritdoc}
   * use Symfony\Component\HttpFoundation\RedirectResponse;
   */
  public function standardMenuItem($section, $page_type, $entity_id) {
    $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end   = \Drupal::service('flexinfo.setting.service')->userEndTime();

    $uri = '/ngpage/' . $section . '/' . $page_type . '/' . $entity_id . '/' . $start . '/' . $end;
    $url = Url::fromUserInput($uri)->toString();

    return new RedirectResponse($url);
  }

  /**
   * @deprecated.
   */
  public function summaryEvaluationForm($entity_id) {
    $form = \Drupal::formBuilder()->getForm('Drupal\ngpage\Form\NgpageSummaryEvaluationForm', $entity_id);

    // or render
    $build = array(
      '#type' => 'markup',
      '#markup' => render($form),
    );

    return $build;
  }

  /**
   * @todo page to debug drupal form
   */
  public function ngDrupalFormStandard($bundle) {
    $form = \Drupal::formBuilder()->getForm('Drupal\ngpage\Form\DrupalSampleForm');

    if ($bundle == 'eventtype') {
      $form = \Drupal::formBuilder()->getForm('Drupal\ngpage\Form\EventtypeFilterForm');
    }

    $build = array(
      '#type' => 'markup',
      '#title' => $this->t('Drupal Form Standard Page H2'),
      '#markup' => render($form),
    );

    return $build;
  }

}
