<?php

namespace Drupal\htmlpage\Atomic\Block;

use Drupal\htmlpage\Atomic\HtmlpageAtomic;

/**
 * Class HtmlpageAtomicBlock.
 \Drupal::service('htmlpage.atomic.block')->demo()
 */
class HtmlpageAtomicBlock extends HtmlpageAtomic {

  /**
   * Constructs a new HtmlpageAtomicBlock object.
   */
  public function __construct() {
  }

  /**
   *
   */
  public function blockTileSectionAdmindashboard() {
    $output = '';
    $output .= '<div class="htmlpage-wrapper">';
      $output .= \Drupal::service('htmlpage.atomic.organism')
        ->tileSection(161, "Total Users", "bg-344a5f color-fff");
      $output .= \Drupal::service('htmlpage.atomic.organism')
        ->tileSection(161, "Active Users", "bg-2fa9e0 color-fff");
      $output .= \Drupal::service('htmlpage.atomic.organism')
        ->tileSection(161, "HCP Attended", "bg-f24b99 color-fff");
      $output .= \Drupal::service('htmlpage.atomic.organism')
        ->tileSection(161, "Total Learners", "bg-99dc3b color-fff");
    $output .= '</div>';

    return $output;
  }

  /**
   * Save PNG.
   * Must include block_id, otherwise the save feature not work.
   */
  public function getOrganismSavePng(array $block_data) {
    $output = '';

    // Save PNG.
    $output .= '<div class="html-block-save-png-icon-wrapper">';
      $output .= \Drupal::service('ngdata.atomic.molecule')
        ->savePngIcon("float-right margin-top-12 margin-right-16", $block_data['block_id']);
    $output .= '</div>';

    return $output;
  }

  /**
   * ECharts.
   */
  public function blockChartTemplate(array $block_data, $html_template = NULL) {
    $output = '';

    $block_id = NULL;
    if (isset($block_data['block_id'])) {
      $block_id = ' id=' . $block_data['block_id'];
    }
    $output .= '<div class="col-xs-12 margin-top-32">';

      $output .= $this->getOrganismSavePng($block_data);

      $output .= '<div' . $block_id . ' class="">';
        $output .= '<div class="panel panel-primary">';
          $output .= '<div class="panel-heading">';
            $output .= '<span class="font-size-16">';
              $output .= $block_data['title'];
            $output .= '</span>';
          $output .= '</div>';
          $output .= '<div class="panel-body margin-top-12 margin-bottom-24">';
            $output .= '<div class="tab-content clearfix">';
              $output .= $html_template;
            $output .= '</div>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * Chartjs
   */
  public function blockChartTemplateTabs(array $block_data, array $html_templates) {
    $output = '';
    $output .= '<div class="col-xs-12 margin-top-32">';

      $output .= $this->getOrganismSavePng($block_data);

      $output .= '<div id="' . $block_data['block_id'] . '" class="">';
        $output .= '<div class="panel panel-primary">';
          $output .= '<div class="panel-heading">';
            $output .= '<span class="font-size-16">';
              $output .= $block_data['title'];
            $output .= '</span>';
          $output .= '</div>';
          $output .= '<div class="panel-body margin-top-12 margin-bottom-24">';

            // Nav Tabs.
            $output .= '<ul class="nav nav-tabs" role="tablist">';
            foreach ($block_data['tabs'] as $key => $row) {
              $li_class = 'active';
              if ($key > 0) {
                $li_class = '';
              }
              $output .= '<li role="presentation" class="' . $li_class . '">';
                $output .= '<a href="#tab-id-' . $row['chart_canvas_id'] . '" role="tab" data-toggle="tab">';
                  $output .= $row['name'];
                $output .= '</a>';
              $output .= '</li>';
            }
            $output .= '</ul>';

            $output .= '<div class="container-fluid">';
              $output .= '<div class="tab-content">';

              foreach ($block_data['tabs'] as $key => $row) {
                $div_class = ' active';
                if ($key > 0) {
                  $div_class = '';
                }
                $output .= '<div id="tab-id-' . $row['chart_canvas_id'] . '" role="tabpanel" class="tab-pane fade in' . $div_class . '">';
                  $output .= $html_templates[$key];
                $output .= '</div>';
              }

              $output .= '</div>';
            $output .= '</div>';

          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}
