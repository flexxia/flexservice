<?php

namespace Drupal\htmlpage\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DefaultPageController.
 */
class DefaultPageController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Bootstrap Nav Tabs Example.
   */
  public function standardPageBootstrapTabs($section, $entity_id, $start_timestamp, $end_timestamp) {
    $output = '';
    $output .= '<ul class="nav nav-tabs" role="tablist">';
      $output .= '<li role="presentation" class="active">';
        $output .= '<a href="#tab-home" role="tab" data-toggle="tab">';
          $output .= 'Home';
        $output .= '</a>';
      $output .= '</li>';
      $output .= '<li role="presentation">';
        $output .= '<a href="#tab-contact" role="tab" data-toggle="tab">';
          $output .= 'Contact';
        $output .= '</a>';
      $output .= '</li>';
    $output .= '</ul>';

    $output .= '<div class="container-fluid">';
      $output .= '<div class="tab-content">';
        $output .= '<div id="tab-home" role="tabpanel" class="tab-pane fade in active">';
          $output .= '<div class="alert alert-info">Home';
          $output .= '</div>';
        $output .= '</div>';
        $output .= '<div id="tab-contact" role="tabpanel" class="tab-pane fade">';
          $output .= '<div class="alert alert-info">Contact';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    $build = [
      '#type' => 'markup',
      '#markup' => $output,
      '#cache' => [
        'max-age' => 0,
      ],
      '#allowed_tags' => \Drupal::service('flexinfo.setting.service')->adminTag(),
    ];

    return $build;
  }

  /**
   *
   */
  public function standardHtmlPage($section, $entity_id, $start_timestamp, $end_timestamp) {
    $page_content = '';

    // Override timestamp from user settings.
    $start_timestamp = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end_timestamp = \Drupal::service('flexinfo.setting.service')->userEndTime();

    if ($section == 'meeting') {
      $page_content = \Drupal::service('htmlpage.content.object')
        ->meetingPageContent($entity_id, 'from-standardPage')['html_content'];
    }
    elseif ($section == 'program') {
      $page_content = \Drupal::service('htmlpage.content.object')
        ->programPageContent($entity_id, $start_timestamp, $end_timestamp, 'from-standardPage')['html_content'];
    }
    $build = [
      '#type' => 'markup',
      '#theme' => 'htmlpage_default',
      '#htmlpage_content' => $page_content,
      '#cache' => [
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => [
          'htmlpage/block-chart-basic',
          'htmlpage/block-chartjs',
          'htmlpage/block-d3',
          'htmlpage/block-echarts',
          'htmlpage/block-jqvmap',
          'htmlpage/bootstrap-table',
          'htmlpage/draw-canvas-tile',
          'ngpage/html2canvas_save_png',
        ],
        'drupalSettings' => [
          'htmlpage' => [
            'jsonUrl' => 'htmlpage/' . $section . '/json/' .$entity_id . '/' . $start_timestamp . '/' . $end_timestamp,
          ],
        ],
      ],
    ];

    // Sample page
    if ($section == 'sample' ||$section == 'samplepage' || $section == 'samplechart') {
      $build = $this->samplePage($section, $entity_id, $start_timestamp, $end_timestamp);
    }

    return $build;
  }

  /**
   *
   */
  public function htmlGuestPage($section, $entity_id, $start_timestamp, $end_timestamp) {
    $output = '';

    if ($section == 'meeting') {
      $output = $this->standardHtmlPage($section, $entity_id, $start_timestamp, $end_timestamp);
    }

    return $output;
  }

  /**
   *
   */
  public function samplePage($section, $entity_id, $start_timestamp, $end_timestamp) {
    $page_content = '';
    $page_content = \Drupal::service('htmlpage.content.samplepage')
        ->samplePageContent()['html_content'];

    $build = [
      '#type' => 'markup',
      '#theme' => 'htmlpage_sample',
      '#htmlpage_content' => $page_content,
      '#cache' => [
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => [
          'htmlpage/block-chart-basic',
          'htmlpage/block-chartjs',
          'htmlpage/block-d3',
          'htmlpage/block-echarts',
          'htmlpage/block-jqvmap',
          'htmlpage/bootstrap-table',
          'htmlpage/draw-canvas-tile',
          'ngpage/html2canvas_save_png',
        ],
        'drupalSettings' => [
          'htmlpage' => [
            'jsonUrl' => 'htmlpage/' . $section . '/json/' .$entity_id . '/' . $start_timestamp . '/' . $end_timestamp,
          ],
        ],
      ],
      // '#allowed_tags' => \Drupal::service('flexinfo.setting.service')->adminTag(),
    ];

    return $build;
  }

}
