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
    $meeting_page_blocks = \Drupal::service('htmlpage.content.meeting')
      ->meetingPageBlocks($meeting_node);

    if (isset($meeting_page_blocks['html_content'])) {
      // Add flex layout, flex-wrap to allow the items to wrap as needed with this property.
      $output['html_content'] .= '<div class="htmlpage-default-wrapper display-flex" style="flex-wrap: wrap;">';
        $output['html_content'] .= $meeting_page_blocks['html_content'];
      $output['html_content'] .= '</div>';
    }
    if (isset($meeting_page_blocks['json_content'])) {
      $output['json_content'] = $meeting_page_blocks['json_content'];
    }

    return $output;
  }

}
