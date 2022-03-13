<?php

/**
 * @file
 */

namespace Drupal\htmlpage\Content;

/**
 * \Drupal::service('htmlpage.object.content')->demo().
 */
class HtmlpageObjectContent {

  /**
   *
   */
  public function meetingPageContent($block_data = []) {
    $output = [];
    $output['html_content'] = NULL;
    $output['json_content'] = [];

    // Tile
    $output['html_content'] .= \Drupal::service('htmlpage.atomic.block')
      ->blockTileSectionAdmindashboard();
    $output['html_content'] .= \Drupal::service('htmlpage.atomic.template')
      ->blockHtmlClearBoth();

    // D3 Cloud.
    $output['html_content'] .= \Drupal::service('htmlpage.object.samplepage')->d3Cloud()['html_content'];
    $output['json_content'][] = \Drupal::service('htmlpage.object.samplepage')->d3Cloud()['json_content'];

    return $output;
  }
}
