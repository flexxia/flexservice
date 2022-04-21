<?php

/**
 * @file
 */

namespace Drupal\htmlpage\Content;

/**
 * \Drupal::service('htmlpage.content.object')->demo().
 */
class HtmlpageObjectContent {

  /**
   *
   */
  public function meetingPageContent($entity_id = NULL, $from_type = NULL) {
    $output = [];
    $output['html_content'] = NULL;
    $output['json_content'] = [];

    // Check if empty
    if (!$entity_id) {
      \Drupal::messenger()
        ->addMessage("There are not meeting id, please contact administer. For meetingPageContent().");
    }
    $meeting_node = \Drupal::entityManager()->getStorage('node')->load($entity_id);

    // Tile Header
    $output['html_content'] .= \Drupal::service('htmlpage.atomic.block')
      ->blockTileSectionMeetingHeader($meeting_node);
    $output['html_content'] .= \Drupal::service('htmlpage.atomic.template')
      ->blockHtmlClearBoth();

    //
    $meetingPageBlocks = \Drupal::service('htmlpage.content.meeting')
      ->meetingPageBlocks($meeting_node);
    $output['html_content'] .= $meetingPageBlocks['html_content'];
    $output['json_content'] = $meetingPageBlocks['json_content'];

    return $output;
  }

}
