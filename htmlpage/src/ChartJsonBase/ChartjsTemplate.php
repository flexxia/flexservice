<?php

namespace Drupal\htmlpage\ChartJsonBase;

/**
 * Class HtmlpageAtomicAtom.
 \Drupal::service('htmlpage.chartjsonbase.chartjstemplate')->demo();
 */
class ChartjsTemplate {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   * @return array
   *   Json Array.
   */
  public function chartjsSampleColor() {
    $output = [
      'rgba(0, 157, 223, 0.8)',
      'rgba(242, 75, 153, 0.8)',
      'rgba(165, 210, 62, 0.8)',
    ];

    return $output;
  }


  /**
   * @return array
   *   Json Array.
   */
  public function chartjsBaseBar() {
    $output = [
      "chart_canvas_id" => "chartjs-bar-sample-1",
      "chart_library" => "chartjs",
      "content" => [
        "type" => "bar",
        "data" => [
          "labels" => [
            "Jan",
            "Feb",
            "Mar",
          ],
          "datasets" => [
            [
              "label" => '2018 Year',          // 图例文字
              "data" => [
                16,
                -21,
                28,
              ],
              "backgroundColor" => [
                $this->chartjsSampleColor()[0],
              ],
              "borderColor" => [
                $this->chartjsSampleColor()[2],
              ],
              "barPercentage" => 0.5,
              "borderWidth" => 1,
            ],
          ],
        ],
        "options" => [
          "responsive" => TRUE,             // 让图片width 100%
          "maintainAspectRatio" => FALSE,   // 让图片width 100%
          "layout" => [
            "padding" => [
              "left" => 10,
              "right" => 0,
              "top" => 30,
              "bottom" => 0,
            ],
          ],
          "plugins" => [
            "legend" => [
              "display" => FALSE,             // 图例， 颜色图例
            ],
            "title" => [
              "display" => FALSE,
              "text"  => 'Chart Top Title'
            ],
          ],
          "scales" => [
            "y" => [
              "title" => [
                "display" => FALSE,
                "text" => 'Y轴说明文字',
              ],
              "suggestedMin" => 50,
              "suggestedMax" => 100,
            ],
          ],
        ],
      ],
    ];

    return $output;
  }

  /**
   *
   */
  public function chartjsBaseMultipleBar() {
    $output = [
      "chart_canvas_id" => "chartjs-bar-sample-2",
      "chart_library" => "chartjs",
      "content" => [
        "type" => "bar",
        "data" => [
          "labels" => [
            "Jan",
            "Feb",
          ],
          "datasets" => [
            [
              "label" => '2019',
              "data" => [62, 20],
              "borderWidth" => 1,
              "backgroundColor" => [
                'rgba(0, 157, 223, 0.8)',
                'rgba(0, 157, 223, 0.8)',
              ],
              "borderColor" => [
                'rgba(0, 157, 223, 0.8)',
                'rgba(0, 157, 223, 0.8)',
              ],
            ],
            [
              "label" => '2020',
              "data" => [30, 12],
              "borderWidth" => 1,
              "backgroundColor" => [
                'rgba(242, 75, 153, 0.8)',
                'rgba(242, 75, 153, 0.8)',
                'rgba(242, 75, 153, 0.8)',
                'rgba(242, 75, 153, 0.8)',
              ],
              "borderColor" => [
                'rgba(242, 75, 153, 0.8)',
                'rgba(242, 75, 153, 0.8)',
              ],
            ],
            [
              "label" => '2021',
              "data" => [18, 13],
              "borderWidth" => 1,
              "backgroundColor" => [
                'rgba(153, 220, 59, 0.8)',
                'rgba(153, 220, 59, 0.8)',
              ],
              "borderColor" => [
                'rgba(153, 220, 59, 0.8)',
                'rgba(153, 220, 59, 0.8)',
              ],
            ],
          ],
        ],
        "options" => [
          "responsive" => TRUE,             // 让图片width 100%
          "maintainAspectRatio" => FALSE,   // 让图片width 100%
          "plugins" => [],
          "scales" => [
            "y" => [
              "suggestedMin" => 50,
              "suggestedMax" => 80
            ],
          ],
        ],
      ],
    ];

    return $output;
  }

  /**
   * @return array
   *   Json Array.
   */
  public function chartjsBaseDoughnut() {
    $output = [
      "chart_canvas_id" => "chartjs-doughnut-sample-1",
      "chart_library" => "chartjs",
      "content" => [
        "type" => "doughnut",
        "data" => [
          "labels" => [
            "Jan",
            "Feb",
            "Mar",
          ],
          "datasets" => [
            [
              "label" => '2018 Year',          // 图例文字
              "data" => [
                16,
                21,
                28,
              ],
              "backgroundColor" => [
                $this->chartjsSampleColor()[0],
                $this->chartjsSampleColor()[1],
                $this->chartjsSampleColor()[2],
              ],
              "hoverOffset" => 4,
            ],
          ],
        ],
        "options" => [
          "responsive" => TRUE,             // 让图片width 100%
          "plugins" => [
            "legend" => [
              "position" => 'top',
            ],
            "title" => [
              "display" => true,
              "text"  => 'Chart Top Title'
            ],
          ],
        ],
      ],
    ];

    return $output;
  }

  /**
   * @return array
   *   Json Array.
   */
  public function chartjsBasePie() {
    $output = [
      "chart_canvas_id" => "chartjs-doughnut-sample-1",
      "chart_library" => "chartjs",
      "content" => [
        "type" => "pie",
        "data" => [
          "labels" => [
            "Jan",
            "Feb",
            "Mar",
          ],
          "datasets" => [
            [
              "label" => 'Pie Chart',       // 图例文字
              "data" => [
                16,
                21,
                28,
              ],
              "backgroundColor" => [
                $this->chartjsSampleColor()[0],
                $this->chartjsSampleColor()[1],
                $this->chartjsSampleColor()[2],
              ],
              "hoverOffset" => 5,           // Hover时，偏移量
            ],
          ],
        ],
        "options" => [
          "responsive" => TRUE,             // 让图片width 100%
          "plugins" => [
            "legend" => [
              "position" => 'top',
            ],
            "title" => [
              "display" => TRUE,
              "text"  => 'Chart Top Title'
            ],
            // "datalabels" => [
            //   "display" => TRUE,
            //   "formatter" => 'Math.round',
            //   "color"  => '#36A2EB'
            // ],
          ],
        ],
      ],
    ];
    return $output;
  }

  /**
   * @return array
   *   Json Array.
   * For meeting page.
   */
  public function chartjsBasePiePure() {
    $output = $this->chartjsBasePie();
    $output["content"]["options"] = [
      "responsive" => TRUE,             // 让图片width 100%
      "plugins" => [
        "legend" => [
          "position" => NULL,
        ],
        "title" => [
          "display" => FALSE,
          "text"  => 'Chart Top Title'
        ],
        // "datalabels" => [
        //   "display" => TRUE,
        //   "formatter" => 'Math.round',
        //   "color"  => '#36A2EB'
        // ],
      ],
    ];

    return $output;
  }

}
