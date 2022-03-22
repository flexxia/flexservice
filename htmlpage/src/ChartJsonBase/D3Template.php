<?php

namespace Drupal\htmlpage\ChartJsonBase;

/**
 * Class HtmlpageAtomicAtom.
 \Drupal::service('htmlpage.chartjsonbase.d3template')->demo();
 */
class D3Template {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   * @return array
   *   Json Array.
   */
  public function d3BaseJson($type = 'bar', $canvas_id = 'd3-bar-sample-1') {
    $output = [
      'chart_canvas_id' => $canvas_id,
      'chart_library' => 'd3',
      'content' => [
        'type' => $type,
        "data" => [
          "labels" => [
            "Jan",
            "Feb",
            "Mar",
          ],
          "datasets" => [
            [
              "label" => '',          // 图例文字
              "data" => [
              ],
              "backgroundColor" => [
              ],
              "borderColor" => [
              ],
              "borderWidth" => 1,
            ],
          ],
        ],
        "options" => [
        ],
      ],
    ];

    return $output;
  }

}
