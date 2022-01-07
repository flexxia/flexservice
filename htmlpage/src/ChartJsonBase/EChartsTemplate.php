<?php

namespace Drupal\htmlpage\ChartJsonBase;

/**
 * Class HtmlpageAtomicAtom.
 \Drupal::service('htmlpage.chartjsonbase.echartstemplate')->demo();
 */
class EChartsTemplate {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   * @return array
   *   Json Array.
   *
   * 数据option['legend']['data'] 需要和 option['series']['data'] 一致.
   */
  public function echartsBaseBar() {
    $output = [
      'chart_canvas_id' => 'echarts-bar-sample-1',
      'chart_library' => 'echarts',
      'content' => [
        'type' => 'bar',
        'option' => [
          'title' => [
            'text' => 'ECharts Apple',
          ],
          'tooltip' => [],
          'legend' => [
            'data' => [
              '2019',
            ],
          ],
          'xAxis' => [
            'data' => [
              'Jan',
              'Feb',
              'Mar',
              'Apr',
              'May',
              'Jun',
            ],
          ],
          'yAxis' => [],
          'series' => [
            [
              'name' => '2019',
              'type' => 'bar',
              'data' => [
                -15,
                20,
                36,
                10,
                10,
                20,
              ],
            ],
          ],
        ],
      ],
    ];

    return $output;
  }

}
