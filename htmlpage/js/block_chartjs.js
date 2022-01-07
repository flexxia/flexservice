/**
 * @file
 * Chartjs
 *
 * Behaviors Name is unique.
 */

(function ($, Drupal) {
  Drupal.behaviors.htmlpageChartJsDefaultChart = {
    attach: function (context, settings) {

      // Run Once.
      // Once('id') is unique.
      $('.htmlpage-wrapper', context).once('htmlpageBehaviorChartjs').each(function () {
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
              if (item['chart_library'] == "chartjs") {
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
       * For destroying a ChartJS chart before creating a new one.
       * Or use https://stackoverflow.com/a/46584899.
       */
      function destroyChart(chartCanvasId) {
        let chartStatus = Chart.getChart(chartCanvasId);
        if (chartStatus != undefined) {
          chartStatus.destroy();
        }
      }

      /**
       * 一定要设置ctx.height.
       */
      function drawBarChart(chartCanvasId, chartContent) {
        var ctx = document.getElementById(chartCanvasId);

        // Add hover custom Percentage or Sum value.
        const customFooterSum = (tooltipItems) => {
          let sum = 0;

          tooltipItems.forEach(function(tooltipItem) {
            sum += tooltipItem.parsed.y;
          });
          return 'Sum: ' + sum;
        };

        const customFooterPercentage = (tooltipItems) => {
          let sum = tooltipItems[0].dataset.data.reduce(function(acc, val) { return acc + val; }, 0)
          let pct = 0  + '%';

          tooltipItems.forEach(function(tooltipItem) {
            if (tooltipItem.raw) {
              pct = (tooltipItem.raw / sum * 100).toFixed(0) + '%';
            }
          });
          return 'Pct: ' + pct;
        };

        // Plugins.
        const optionsObj = chartContent['options']
        optionsObj.plugins.tooltip = {
          callbacks: {
            footer: customFooterPercentage,
          }
        }

        optionsObj.plugins.datalabels = {
          anchor: 'end',
          align: 'top',
          formatter: Math.round,
          font: {
            weight: 'bold'
          }
        };

        // Chart destroy.
        destroyChart(chartCanvasId);

        // Draw Chart.
        var chartBlock = new Chart(ctx, {
          type: 'bar',
          data: chartContent['data'],
          options: optionsObj,
          plugins: [ChartDataLabels],
        });

      }

      /**
       *
       */
      function drawDoughnutChart(chartCanvasId, chartContent) {
        var ctx = document.getElementById(chartCanvasId);
        ctx.height = 100;
        ctx.width = 100;

        // Chart destroy.
        destroyChart(chartCanvasId);

        // Draw Chart.
        var chartBlock = new Chart(ctx, {
          type: 'doughnut',
          data: chartContent['data'],
          options: chartContent['options']
        });
      }

    }
  };
})(jQuery, Drupal);
