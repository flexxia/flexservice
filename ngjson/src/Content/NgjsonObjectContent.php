<?php

/**
 * @file
 */

namespace Drupal\ngjson\Content;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Url;

use Drupal\dashpage\Content\DashTabSpeaker;
use Drupal\dashpage\Content\DashpageObjectContent;

use Drupal\flexpage\Content\FlexpageEventLayout;

/**
 * An example controller.
 */
class NgjsonCaseContent {

  /**
   *
   */
  public function blockTabChartjs21($meeting_nodes = array()) {
    $output = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsMeetingsByMonthByEventType($meeting_nodes);

    $output['blockContent'][0]['tabTitle'] = "MONTH";
    $output['tabShow'] = "show";

    $output['blockContent'][1] = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsMeetingsByQuarterByEventType($meeting_nodes)['blockContent'][0];
    $output['blockContent'][1]['tabTitle'] = "QUARTER";

    return $output;
  }

  /**
   *
   */
  public function blockTabChartjs22($meeting_nodes = array()) {
    $output = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsMeetingsByProvince($meeting_nodes);

    $output['tabShow'] = "show";
    $output['blockContent'][0]['tabTitle'] = "PROVINCE";

    // init tab 2
    $output['blockContent'][1] = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsMeetingsByProvinceByEventType($meeting_nodes)['blockContent'][0];
    $output['blockContent'][1]['tabTitle'] = "EVENT TYPE";

    return $output;
  }

  /**
   *
   */
  public function blockTabChartjs31($meeting_nodes = array()) {
    $output = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsHcpReachByMonthByEventType($meeting_nodes);

    $output['tabShow'] = "show";
    $output['blockContent'][0]['tabTitle'] = "MONTH";

    // init tab 2
    $output['blockContent'][1] = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsHcpReachByQuarterByEventType($meeting_nodes)['blockContent'][0];
    $output['blockContent'][1]['tabTitle'] = "QUARTER";

    return $output;
  }

  /**
   *
   */
  public function blockTabChartjs32($meeting_nodes = array()) {
    $output = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsHcpReachByProvince($meeting_nodes);

    $output['tabShow'] = "show";
    $output['blockContent'][0]['tabTitle'] = "PROVINCE";

    // init tab 2
    $output['blockContent'][1] = \Drupal::service('ngdata.atomic.block')
      ->blockChartjsHcpReachByProvinceByEventType($meeting_nodes)['blockContent'][0];
    $output['blockContent'][1]['tabTitle'] = "EVENT TYPE";

    return $output;
  }

}

/**
 * An example controller.
 */
class NgjsonObjectContent extends NgjsonCaseContent {

  public $atomic;

  /**
   * Constructs a new NgdataAtomicPage object.
   */
  public function __construct() {
    $this->atomic = \Drupal::getContainer()->get('ngdata.atomic');
  }

  /**
   * @return php object, not JSON
   */
  public function getFilterProgramTids() {
    $filter_program_tids = array();

    $user_default_theraparea_tid = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_theraparea');
    if ($user_default_theraparea_tid) {
      $filter_program_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')
        ->programTidsByTheraparea(array($user_default_theraparea_tid));
    }
    else{
      $user_default_businessunit_tid = \Drupal::service('user.data')
        ->get('navinfo', \Drupal::currentUser()->id(), 'default_businessunit');
      if ($user_default_businessunit_tid) {
        $theraparea_tids_by_businessunit_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')
          ->wrapperTermTidsByField('therapeuticarea', 'field_theraparea_businessunit', array($user_default_businessunit_tid));

        $filter_program_tids = \Drupal::getContainer()->get('flexinfo.queryterm.service')
          ->programTidsByTheraparea($theraparea_tids_by_businessunit_tids);
      }
      else {
        // no filter, it means all

        $filter_program_tids = \Drupal::getContainer()
          ->get('flexinfo.term.service')
          ->getTidsFromVidName('program');
      }
    }

    return $filter_program_tids;
  }

  /**
   *
   */
  public function querySnapshotMeetingsNodes($section = NULL, $entity_id = NULL, $start = NULL, $end = NULL) {
    // query container
    $query_container = \Drupal::getContainer()->get('flexinfo.querynode.service');
    $query = $query_container->queryNidsByBundle('meeting');

    // condition date ranger
    if ($start && $end) {
      $group = $query_container->groupByMeetingTimestamp($query, $start, $end);
      $query->condition($group);
    }

    // condition event type, only work on basic map
    $user_default_eventtype = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_eventtype');
    if ($user_default_eventtype) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_eventtype', $user_default_eventtype);
      $query->condition($group);
    }

    // condition province
    $user_default_provinces = \Drupal::service('user.data')
      ->get('navinfo', \Drupal::currentUser()->id(), 'default_province');
    if ($user_default_provinces) {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_province', $user_default_provinces, 'IN');
      $query->condition($group);
    }

    // top filter condition
    $filter_program_tids = $this->getFilterProgramTids();
    $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_program', $filter_program_tids, 'IN');
    $query->condition($group);

    // condition group
    if ($section == 'program') {
      $group = $query_container->groupStandardByFieldValue($query, 'field_meeting_program', $entity_id);
      $query->condition($group);
    }

    $meeting_nids = $query_container->runQueryWithGroup($query);

    // shuffle and slice
    // shuffle($meeting_nids);
    // $meeting_nids = array_slice($meeting_nids, 0, 10);

    $meeting_nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($meeting_nids);

    return $meeting_nodes;
  }

  /**
   *
   */
  public function businessunitPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = $this->atomic->getOrganism->tileSectionGroup($meeting_nodes);

    // $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    // $output[] = $this->atomic->getBlock->blockChartTotalEventsByTherapeuticArea($meeting_nodes, $entity_id);
    // $output[] = $this->atomic->getBlock->blockChartjsWithoutLegendMetricQuestion($meeting_nodes, 120, $chart_type = 'bar', FALSE, 'bg-69d0ff');

    // $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    // $output[] = $this->atomic->getBlock->blockChartTotalEventsByEventType($meeting_nodes);
    // $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSelectkeySwitch($meeting_nodes, 188, 'pie', FALSE, 'bg-f7d417');

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->blockTabChartjs21($meeting_nodes);
    $output[] = $this->blockTabChartjs31($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 130, 'pie', FALSE, 'bg-336699');
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 131, 'pie', FALSE, 'bg-336699');

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 132, 'pie', FALSE, 'bg-336699');
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 133, 'pie', FALSE, 'bg-336699');

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 135, 'pie', FALSE, 'bg-339966');
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 136, 'pie', FALSE, 'bg-339966');

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 137, 'pie', FALSE, 'bg-339966');

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableTopProgram($meeting_nodes);
    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableTopSpeaker($meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function dashboardPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = $this->atomic->getOrganism->tileSectionGroup($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsTotalEventsByBusinessunit($meeting_nodes, $entity_id);
    $output[] = $this->atomic->getBlock->blockChartTotalEventsByEventType($meeting_nodes);

    // $output[] = $this->atomic->getBlock->blockChartjsWithoutLegendMetricQuestion($meeting_nodes, 120, $chart_type = 'bar', FALSE, 'bg-69d0ff');
    // $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSelectkeySwitch($meeting_nodes, 188, 'pie', FALSE, 'bg-f7d417');

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->blockTabChartjs31($meeting_nodes);
    $output[] = $this->blockTabChartjs32($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->blockTabChartjs21($meeting_nodes);
    $output[] = $this->blockTabChartjs22($meeting_nodes);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableTopProgram($meeting_nodes);
    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableTopSpeaker($meeting_nodes);

    return $output;
  }

  /**
   *
   */
  public function dashboardPageContent_old($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = [];
    // $output[] = $this->atomic->getBlock->blockChartjsWithoutLegendMetricQuestion($meeting_nodes, 120, $chart_type = 'bar', FALSE, 'bg-69d0ff');
    // $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSelectkeySwitch($meeting_nodes, 188, 'pie', FALSE, 'bg-f7d417');

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 130, 'pie', FALSE, 'bg-336699');
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 131, 'pie', FALSE, 'bg-336699');

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    return $output;
  }

  /**
   * this is only Json output, no ngpage view
   */
  public function exportdataPageContent($section, $entity_id, $start, $end) {
    $output = [];

    // $ExportDataContent = new ExportDataContent();
    // $tbody_data = $ExportDataContent->standardDataTemplate($section, $entity_id, $start, $end);
    // return json
    // return $tbody_data;

    // Html table
    // $thead = \Drupal::service('ngdata.atomic.molecule')
    //   ->getTableTheadHtml(array_keys(current($tbody_data)));
    // $tbody = \Drupal::service('ngdata.atomic.molecule')
    //   ->getTableTbodyHtml($tbody_data);

    // $table = \Drupal::service('ngdata.atomic.organism')
    //   ->htmlSectionBasicTableTemplate(NULL, $thead, $tbody, $color_box_palette = FALSE, $bg_color_class = 'bg-ffffff margin-top-n-66');

    // $download_url = Url::fromUserInput(
    //   '/ngpage/export_table_data/excel',
    //   ['attributes' => ['class' => ['color-fff']]]
    // );

    // $html_snippet = '';
    // $html_snippet .= '<div class="btn btn-primary margin-left-12 margin-top-n-36 clear-both color-fff">';
    //   $html_snippet .= \Drupal::l(t('Export Excel'), $download_url);
    // $html_snippet .= '</div>';
    // $html_snippet .= $table;

    // $output[] = \Drupal::service('ngdata.atomic.block')
    //   ->getBlockHtmlSnippet($html_snippet, $type = "htmlSnippt", $save_png_icon_style = "display-none");

    return $output;
  }

  /**
   *
    $output[] = $this->atomic->getBlock->blockChartjsWithoutLegendMetricQuestion($meeting_nodes, 120, $chart_type = 'bar');
    $output[] = $this->atomic->getBlock->getBlockHtmlBasicTableByQuestion($meeting_nodes, 188, 'Hcp Reach By Specialty');
    $output[] = $this->blockBootstrapSliderByQuestion($meeting_nodes, 120);
   */
  public function metricPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    $output = [];


    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 128);
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 122);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 123);
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 124);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 125);
    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 126);

    $output[] = $this->atomic->getTemplate->blockHtmlClearBoth();

    $output[] = $this->atomic->getBlock->blockChartjsMetricQuestionSwitch($meeting_nodes, 127);
    return $output;
  }

  /**
   *
   */
  public function meetingPageContent($section, $entity_id, $start, $end) {
    return \Drupal::service('ngdata.atomic.page')
      ->meetingPageContent($section, $entity_id, $start, $end);
  }

  /**
   *
   */
  public function programPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    return \Drupal::service('ngdata.atomic.page')
      ->programPageContent($meeting_nodes, $entity_id, $start, $end);
  }

  /**
   * View Programs Table List
   */
  public function eventlistPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    return \Drupal::service('ngdata.atomic.page')
      ->eventlistPageContent($meeting_nodes, $entity_id, $start, $end);
  }

  /**
   * View Programs Table List
   */
  public function programlistPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    return \Drupal::service('ngdata.atomic.page')
      ->programlistPageContent($meeting_nodes, $entity_id, $start, $end);
  }

  /**
   *
   */
  public function speakerlistPageContent($section, $entity_id, $start, $end) {
    $meeting_nodes = $this->querySnapshotMeetingsNodes($section, $entity_id, $start, $end);

    return \Drupal::service('ngdata.atomic.page')
      ->speakerlistPageContent($meeting_nodes, $entity_id, $start, $end);
  }

  /**
   *
   */
  public function standardnodePageContent($section, $entity_id, $start, $end) {
    if ($entity_id == 'meeting') {
      return \Drupal::service('ngdata.atomic.page')
        ->customNodeMeetingPageContent($entity_id, $start, $end);
    }

    return \Drupal::service('ngdata.atomic.page')
      ->standardnodePageContent($entity_id, $start, $end);
  }

  /**
   *
   */
  public function standardtermPageContent($section, $entity_id, $start, $end) {
    return \Drupal::service('ngdata.atomic.page')
      ->standardtermPageContent($entity_id, $start, $end);
  }

  /**
   *
   */
  public function standarduserPageContent($section, $entity_id, $start, $end) {
    return \Drupal::service('ngdata.atomic.page')
      ->standarduserPageContent($entity_id, $start, $end);
  }

}
