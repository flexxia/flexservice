/**
 * @file
 * ECharts
 *
 * Behaviors Name is unique.
 */

(function ($, Drupal) {
  Drupal.behaviors.htmlpageEChartsDefaultChart = {
    attach: function (context, settings) {

      // Run Once.
      // Once('id') is unique.
      $('.htmlpage-wrapper', context).once('htmlpageBehaviorECharts').each(function () {
        initOnce();
      });

      /**
       *
       */
      function initOnce() {
        var siteBaseUrl = settings.path.baseUrl;
        var jsonUrl = siteBaseUrl + drupalSettings.htmlpage.jsonUrl;

        axios.get(jsonUrl)
          .then(function (response) {

            var result = response.data;
            response.data.forEach(function (item, index) {
              if (item['chart_library'] == "echarts") {

                if ($('#' + item['chart_canvas_id']).length) {
                  switch (item['content']['type']) {
                    case "bar":
                      drawBarChart(item['chart_canvas_id'], item['content']);
                      break;
                    case "doughnut":
                      drawDoughnutChart(item['chart_canvas_id'], item['content']);
                      break;

                    default:
                  }
                }
              }
            });
          })
          .catch(function (error) {
            console.log(error);
          });
      }

      /**
       *
       */
      function drawBarChart(chartCanvasId, chartContent) {
        var myChart = echarts.init(document.getElementById(chartCanvasId));

        var option = chartContent['option'];

        // 必须从js指定yAxis是一个空的对象。
        option['yAxis'] = {};

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
      }

    }
  };
})(jQuery, Drupal);
