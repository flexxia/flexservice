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
    $output['html_content'] .= $this->meetingCommentsD3WordCloud()['html_content'];
    $output['json_content'][] = $this->meetingCommentsD3WordCloud()['json_content'];

    return $output;
  }

  /**
   * D3 Cloud.
   */
  public function meetingCommentsD3WordCloud($block_data = []) {
    $output = [];

    $d3_canvas_id = 'd3-cloud-meeting-1';
    $block_definition = [
      'title' => 'Program -- "I just want to be my old self again" - Overcoming the Status Quo in MDD',
      'block_id' => 'd3-cloud-meeting-block-1',
      'chart_canvas_id' => $d3_canvas_id,
    ];

    $output['html_content'] = \Drupal::service('htmlpage.charthtmltemplate.section.d3')
      ->blockD3Template($block_definition);

    $json_content = \Drupal::service('htmlpage.chartjsonbase.d3template')
      ->d3BaseJson('word_cloud', $d3_canvas_id);
    $json_content['content']['data']['datasets'] = [
      ["name" => "thank you", "count_field" => 6,],
      ["name" => "management of", "count_field" => 4,],
      ["name" => "of the ", "count_field" => 3,],
      ["name" => "anxiety and", "count_field" => 3,],
      ["name" => "more time", "count_field" => 3,],
      ["name" => "for bipolar", "count_field" => 3,],
      ["name" => "of this", "count_field" => 2,],
      ["name" => "the topic", "count_field" => 2,],
      ["name" => "topic of ", "count_field" => 2,],
      ["name" => "will use ", "count_field" => 2,],
      ["name" => "use switch ", "count_field" => 2,],
      ["name" => "switch rx", "count_field" => 2,],
      ["name" => "rx more", "count_field" => 2,],
      ["name" => "of anxiety ", "count_field" => 2,],
      ["name" => "and depression ", "count_field" => 2,],
      ["name" => "on the ", "count_field" => 2,],
      ["name" => "list of", "count_field" => 2,],
      ["name" => "case studies ", "count_field" => 2,],
      ["name" => "studies very ", "count_field" => 2,],
      ["name" => "look at", "count_field" => 2,],
      ["name" => "importance of", "count_field" => 2,],
      ["name" => "great presentation ", "count_field" => 2,],
      ["name" => "of medication", "count_field" => 2,],
      ["name" => "n a", "count_field" => 2,],
      ["name" => "role of", "count_field" => 2,],
      ["name" => "adhd treatments", "count_field" => 2,],
      ["name" => "what i’m ", "count_field" => 2,],
      ["name" => "for mdd", "count_field" => 2,],
      ["name" => "more about ", "count_field" => 2,],
      ["name" => "for certain", "count_field" => 2,],
    ];
    $json_content['content']['data']['datasets'] = [
      ["name" => "anxiety", "count_field" => 10,],
      ["name" => "very ", "count_field" => 8,],
      ["name" => "will ", "count_field" => 6,],
      ["name" => "thank", "count_field" => 6,],
      ["name" => "depression ", "count_field" => 6,],
      ["name" => "adhd ", "count_field" => 6,],
      ["name" => "program", "count_field" => 5,],
      ["name" => "time ", "count_field" => 5,],
      ["name" => "excellent", "count_field" => 5,],
      ["name" => "treatment", "count_field" => 5,],
      ["name" => "mdd", "count_field" => 5,],
      ["name" => "list ", "count_field" => 4,],
      ["name" => "change ", "count_field" => 4,],
      ["name" => "none ", "count_field" => 4,],
      ["name" => "symptoms ", "count_field" => 4,],
      ["name" => "meds ", "count_field" => 4,],
      ["name" => "management ", "count_field" => 4,],
      ["name" => "would", "count_field" => 4,],
      ["name" => "discussion ", "count_field" => 4,],
      ["name" => "practice ", "count_field" => 3,],
      ["name" => "well ", "count_field" => 3,],
      ["name" => "better ", "count_field" => 3,],
      ["name" => "great", "count_field" => 3,],
      ["name" => "presentation ", "count_field" => 3,],
      ["name" => "nil", "count_field" => 3,],
      ["name" => "effects", "count_field" => 3,],
      ["name" => "doing", "count_field" => 3,],
      ["name" => "trintellix ", "count_field" => 3,],
      ["name" => "between", "count_field" => 3,],
      ["name" => "bipolar", "count_field" => 3,],
    ];
    $output['json_content'] = $json_content;

    return $output;
  }
}
