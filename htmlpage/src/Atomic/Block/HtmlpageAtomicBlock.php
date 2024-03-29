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
  public function blockTileSectionAdmindashboardSample() {
    $output = '';
    $output .= '<div class="htmlpage-tile-section-wrapper">';
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
   *
   */
  public function blockTileSectionProgramHeader($meeting_nodes) {
    $output = '';

    $signature_total = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_signature')
    );
    $evaluation_nums = array_sum(
      \Drupal::service('flexinfo.field.service')
      ->getFieldFirstValueCollection($meeting_nodes, 'field_meeting_evaluationnum')
    );

    $tile_array[] = array(
      'name'  => 'Total Events',
      'value' => count($meeting_nodes),
    );
    $tile_array[] = array(
      'name'  => 'HCP Reached',
      'value' => $signature_total,
    );
    $tile_array[] = array(
      'name'  => 'Evaluations Received',
      'value' => $evaluation_nums,
    );
    $tile_array[] = array(
      'name'  => 'HCP Response',
      'value' => \Drupal::service('flexinfo.calc.service')
        ->getPercentageDecimal($evaluation_nums, $signature_total, 0) . '%',
    );

    $output = '';
    foreach ($tile_array as $key => $tile) {
      $css_class = ' bg-' . \Drupal::service('baseinfo.setting.service')
        ->colorPlateForTile($key + 1, FALSE);
      $output .= \Drupal::service('htmlpage.atomic.organism')
        ->tileSection($tile['value'], $tile['name'], $css_class . " color-fff");
    }

    return $output;
  }

  /**
   * @return string
   */
  public function blockTileSectionProgramNameHeader($program_term = NULL, $share_link_content = NULL) {
    $output = '';

    $output .= '<div class="htmlpage-program-name-wrapper">';
      $output .= '<div class="row margin-left-12 margin-bottom-12">';

        $output .= \Drupal::service('htmlpage.atomic.atom')
          ->getProgramImage($program_term);

        $output .= '<div class="col-md-9">';
          $output .= '<span class="color-00a9e0 font-size-20 font-weight-300">';
            $output .= $program_term->getName();
          $output .= '</span>';
        $output .= '</div>';

        $output .= $share_link_content;

      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

  /**
   * @return string
   */
  public function blockTileSectionProgramNameHeaderForMeeting($program_term = NULL, $meeting_share_link = FALSE, $meeting_nid = NULL) {
    $output = '';
    $share_link_content = '';

    if ($meeting_share_link && $meeting_nid && \Drupal::currentUser()->isAuthenticated()) {
      $share_link_content .= '<div class="col-md-2 float-right margin-right-16">';
        $share_link_content .= \Drupal::service('htmlpage.atomic.atom')
          ->getBlockTileMeetingShareLink($meeting_nid);
      $share_link_content .= '</div>';
    }

    $output = $this->blockTileSectionProgramNameHeader($program_term, $share_link_content);

    return $output;
  }

  /**
   * @return string
   */
  public function blockTileSectionProgramNameHeaderForProgram($program_term = NULL, $program_share_link = FALSE) {
    $output = '';
    $share_link_content = '';

    if ($program_share_link && $program_term && \Drupal::currentUser()->isAuthenticated()) {
      $share_link_content .= '<div class="col-md-2 float-right margin-right-16">';
        $share_link_content .= \Drupal::service('htmlpage.atomic.atom')
          ->getBlockTileProgramShareLink($program_term->id());
      $share_link_content .= '</div>';
    }

    $output = $this->blockTileSectionProgramNameHeader($program_term, $share_link_content);

    return $output;
  }

  /**
   * @return string
   */
  public function blockTileSectionMeetingHeader($meeting_node = NULL, $meeting_share_link = TRUE) {
    $output = '';

    $fixed_section_param = \Drupal::service('htmlasset.meeting.service')
      ->blockTileMeetingHeaderValue($meeting_node);

    $output .= '<div class="htmlpage-meeting-tile-header-wrapper">';
      $output .= '<div class="margin-left-12 clear-both">';
        foreach ($fixed_section_param as $row) {
          if ($row['value'] == 'Speaker') {
            $html_span_tag_col = '<span class="col-md-12 col-sm-12 padding-top-12 htmlpage-meeting-tile-section-wrapper">';
          }
          else {
            $html_span_tag_col = '<span class="col-md-3 col-sm-6 padding-top-12 htmlpage-meeting-tile-section-wrapper">';
          }
          $output .= $html_span_tag_col;
            $output .= '<span class="dashpage-square-text">';
              $output .= $row['value'] . ':';
            $output .= '</span>';
            $output .= '<span class="dashpage-square-text padding-left-6">';
              $output .= $row['value_one'];
            $output .= '</span>';
          $output .= '</span>';
        }
      $output .= '</div>';
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
   * Charts.
   */
  public function blockChartSectionTemplate(array $block_data, $html_template = NULL) {
    $output = '';

    if (isset($block_data['block_id'])) {
      $block_id = $block_data['block_id'];
    }
    else {
      $block_id = \Drupal::service('htmlpage.atomic.atom')->generateUniqueId();
    }

    $block_column = 'col-xs-12';
    if (isset($block_data['block_column'])) {
      $block_column = $block_data['block_column'];
    }

    $output .= '<div class="' . $block_column . ' margin-top-16 margin-bottom-24 flex-section-wrapper">';

      $output .= $this->getOrganismSavePng($block_data);

      $output .= '<div id="' . $block_id . '" class="chartjs-block-question-wrapper">';
        $output .= '<div class="panel panel-primary">';
          $output .= '<div class="panel-heading print-panel-heading">';
            $output .= '<span class="font-size-16 line-height-1-2 print-font-size-12">';
              $output .= $block_data['title'];
            $output .= '</span>';
          $output .= '</div>';
          $output .= '<div class="panel-body padding-0">';
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
   * Chart
   */
  public function blockChartSectionTemplateTabs(array $block_data, array $html_templates) {
    $output = '';
    $output .= '<div class="col-xs-12 margin-top-32 flex-section-wrapper">';

      $output .= $this->getOrganismSavePng($block_data);

      $output .= '<div id="' . $block_data['block_id'] . '" class="chartjs-block-question-wrapper chartjs-block-question-tabs-wrapper">';
        $output .= '<div class="panel panel-primary">';
          $output .= '<div class="panel-heading print-panel-heading">';
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

  /**
   * Charts.
   */
  public function blockHtmlSectionTemplate(array $block_data, $html_template = NULL) {
    $output = '';

    if (isset($block_data['block_id'])) {
      $block_id = $block_data['block_id'];
    }
    else {
      $block_id = \Drupal::service('htmlpage.atomic.atom')->generateUniqueId();
    }

    $block_column = 'col-xs-12';
    if (isset($block_data['block_column'])) {
      $block_column = $block_data['block_column'];
    }

    $output .= '<div class="' . $block_column . ' margin-top-16 flex-section-wrapper">';

      $output .= $this->getOrganismSavePng($block_data);

      $output .= '<div id="' . $block_id . '" class="html-block-question-wrapper">';
        $output .= '<div class="panel panel-primary panel-box-shadow-none">';
          $output .= '<div class="panel-heading print-panel-heading">';
            $output .= '<span class="font-size-14 print-font-size-12">';
              $output .= $block_data['title'];
            $output .= '</span>';
          $output .= '</div>';
          $output .= '<div class="panel-body margin-top-2 margin-bottom-2 font-size-12">';
            $output .= '<div class="tab-content clearfix">';
              $output .= $html_template;
            $output .= '</div>';
          $output .= '</div>';
        $output .= '</div>';
      $output .= '</div>';
    $output .= '</div>';

    return $output;
  }

}
