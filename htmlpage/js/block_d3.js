/**
 * @file
 * D3
 *
 * Behaviors Name is unique.
 */

(function ($, Drupal) {
  Drupal.behaviors.htmlpageD3DefaultChart = {
    attach: function (context, settings) {

      // Global Variable
      const siteBaseUrl = settings.path.baseUrl;

      /**
       * Run Once.
       * Once('id') is unique.
       */
      $('.htmlpage-wrapper', context).once('htmlpageBehaviorD3').each(function () {
        initOnce();
      });

      /**
       *
       */
      function initOnce() {
        const jsonUrl = siteBaseUrl + drupalSettings.htmlpage.jsonUrl;

        axios.get(jsonUrl)
          .then(function (response) {
            var result = response.data;
            response.data.forEach(function (item, index) {
              if (item['chart_library'] == "d3") {
                if ($('#' + item['chart_canvas_id']).length) {
                  switch (item['content']['type']) {
                    case "7fairy":
                      draw7FairyChart(item['chart_canvas_id'], item['content']);
                      break;
                    case "bar":
                      drawBarChart(item['chart_canvas_id'], item['content']);
                      break;
                    case "word_cloud":
                      drawWordCloud(item['chart_canvas_id'], item['content']);
                      break;
                    case "map":
                      drawBasicMap(item['chart_canvas_id'], item['content']);
                      break;
                    case "text":
                      drawText(item['chart_canvas_id'], item['content']);
                      break;
                    case "doughnut":
                      drawDoughnutChart(item['chart_canvas_id'], item['content']);
                      break;
                    case "scatter_plot":
                      drawScatterplot(item['chart_canvas_id'], item['content']);
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
      function draw7FairyChart(chartCanvasId, chartContent) {
        pathLineUpdate(chartCanvasId);
        function pathLineUpdate(chartCanvasId) {
          d3.select("#" + chartCanvasId)
            .append('svg')
            .attr('width', 300)
            .attr('height', 180)
            .append('line')
            .attr('x1', 130)
            .attr('y1', 80)
            .attr('x2', 500)
            .attr('y2', 180)
            .attr('stroke', '#36a2eb')
            .attr('stroke-width', 2);
        }
      }

      /**
       * https://doc.yonyoucloud.com/doc/wiki/project/d3wiki/makechart.html
       */
      function drawBasicMap(chartCanvasId, chartContent) {
        const pathToJSON = siteBaseUrl + "modules/custom/flexservice/htmlpage/data/map_world.geojson";
        // const pathToJSON = siteBaseUrl + "modules/custom/flexservice/htmlpage/data/map_beijing.json";
        axios.get(pathToJSON)
          .then(function (response) {
            dataset = response.data;
            drawBasicMapFn(chartCanvasId, chartContent, dataset);
          })
          .catch(function (error) {
            console.log(error);
          });
      }
      function drawBasicMapFn2(chartCanvasId, chartContent, dataset) {
        let projection = d3.geoMercator()
          .scale(400)
          .translate([200, 280])
          .center([0, 5]);

        let geoGenerator = d3.geoPath()
          .projection(projection);

        function handleMouseover(e, d) {
          let pixelArea = geoGenerator.area(d);
          let bounds = geoGenerator.bounds(d);
          let centroid = geoGenerator.centroid(d);
          let measure = geoGenerator.measure(d);

          d3.select('#content .info')
            .text(d.properties.name + ' (path.area = ' + pixelArea.toFixed(1) + ' path.measure = ' + measure.toFixed(1) + ')');

          d3.select('#content .bounding-box rect')
            .attr('x', bounds[0][0])
            .attr('y', bounds[0][1])
            .attr('width', bounds[1][0] - bounds[0][0])
            .attr('height', bounds[1][1] - bounds[0][1]);

          d3.select('#content .centroid')
            .style('display', 'inline')
            .attr('transform', 'translate(' + centroid + ')');
        }

        function update(geojson) {
          let u = d3.select('#content g.map')
            .selectAll('path')
            .data(geojson.features);

          u.enter()
            .append('path')
            .attr('d', geoGenerator)
            .on('mouseover', handleMouseover);
        }

        // REQUEST DATA
        d3.json('https://assets.codepen.io/2814973/africa.json')
          .then(function(json) {
            update(json)
          });
      }
      function drawBasicMapFn(chartCanvasId, chartContent, dataset) {
        var mapGeoJson = dataset.features;

        /**
         * 基本配置
         */
        const svgWidth = 1000;
        const svgHeight = 600;
        const padding = 30;

        // chart id
        const svg = d3.select("#" + chartCanvasId)
          .append("svg")
          .attr("width", svgWidth)
          .attr("height", svgHeight);

        /*
         * 创建一个地理投影
         * .center 设置投影中心位置
         * .scale 设置缩放系数
         * 坐标轴的位置，可以通过 transform 属性来设定。
         */
        var projection = d3.geoMercator()
          .center([10, 10])
          .scale(150)
          .translate([svgWidth / 2, svgHeight / 2]);

        //  创建路径生成器path
        var path = d3.geoPath(projection);

        var color = d3.schemeCategory10;

        // 设置颜色值
        var ss2 = d3.schemeSet2;
        var sp2 = d3.schemePastel2;

        /*
         * 渲染地图
         * mouseover 鼠标移入变色
         */
         svg.selectAll("g")
           .data(mapGeoJson)
           .enter()
           .append("g")
           .append("path")
             .attr('d',path)//使用地理路径生成器
             .attr("stroke-width", 1)
             .attr("stroke", "#ccc")
             .attr("fill", function(d,i){
                 return color[i%10];
             })
           .on("mouseover",function(d,i){
              d3.select(this).attr('opacity', 0.5);
           })
           .on("mouseout",function(d,i){
              d3.select(this).attr('opacity', 1);
           });

        return;
      }

      /**
       * https://doc.yonyoucloud.com/doc/wiki/project/d3wiki/makechart.html
       */
      function drawBarChart(chartCanvasId, chartContent) {
        /**
         * 添加画布和绘制矩形
         */
        var canvasWidth = 300;    // 画布的宽度
        var height = 200;   // 画布的高度

        var svg = d3.select("#" + chartCanvasId)     // 选择文档中的body元素
          .append("svg")              // append添加一个svg元素
          .attr("width", canvasWidth)       // 设定宽度
          .attr("height", height);    // 设定高度

        // 画布周边的空白.
        var padding = {
          left: 30,
          right: 30,
          top: 20,
          bottom: 20
        };

        // 定义一个数组.
        var dataset = [30, 21, 17, 13, 9];

        // 每个矩形所占的像素高度(包括空白)
        var rectHeight = 25;

        /**
         * 定义数据和比例尺.
         */
        var xScale = d3.scaleBand()
          .domain(d3.range(0, dataset.length))
          .rangeRound([0, canvasWidth - padding.left - padding.right])
          .padding(0.2);

        // y轴的比例尺
        var yScale = d3.scaleLinear()
          .domain([0, d3.max(dataset)])
          .range([height - padding.top - padding.bottom, 0]);

        // 矩形之间的空白
        var rectPadding = 4;

        // 画矩形
        // .selectAll("rect") 选择svg内所有的矩形
        // .data(dataset)     绑定数组
        // .enter()           指定选择集的enter部分
        // .append("rect")    添加足够数量的矩形元素
        // function(d, i)，    d 代表与当前元素绑定的数据，i 代表索引号。
        // return xScale(d)   在这里用比例尺
        // .attr("x", 20)     设置x坐标 为20
        // .attr("fill", "color") 是给矩形元素设置颜色
        svg.selectAll("rect")
          .data(dataset)
          .enter()
          .append("rect")
          .attr("class", "rect-elements")
          .attr("transform", "translate(" + padding.left + ", " + padding.top + ")")
          .attr("x", function(d, i) {
            return xScale(i) + rectPadding / 2;
          })
          .attr("y", function(d) {
            return yScale(d);
          })
          .attr("width", (xScale.bandwidth() - rectPadding ))
          .attr("height", function(d) {
            return height - padding.top - padding.bottom - yScale(d);
          })
          .attr("fill", "steelblue");

        /**
         * 坐标轴
         */
        // 定义坐标轴
        var xAxis = d3.axisBottom()   // d3.axisBottom()：D3 中坐标轴的组件，能够在 SVG 中生成组成坐标轴的元素
          .scale(xScale)              // 指定比例尺
          .ticks(7);                  // 指定刻度的数量
        var yAxis = d3.axisLeft()
          .scale(yScale)
          .ticks(7);

        // 坐标轴的class，attr来设定。
        // 坐标轴的位置，可以通过 transform 属性来设定。
        svg.append("g")
          .attr("class", "axis")
          .attr("transform", "translate(30, 180)")
          .call(xAxis);

        svg.append("g")
          .attr("class", "axis")
          .attr("transform", "translate(" + padding.left + ", " + padding.top + ")")
          .call(yAxis);

        /**
         * 添加文字元素
         */
        var texts = svg.selectAll(".barChartLabel")
          .data(dataset)
          .enter()
          .append("text")
          .attr("class", "barChartLabel")
          .attr("transform", "translate(" + padding.left + ", " + padding.top + ")")
          .attr("x", function(d, i) {
            return xScale(i) + rectPadding / 2 - 9;
          })
          .attr("y", function(d) {
            return yScale(d);
          })
          .attr("dx", function() {
            return (xScale.bandwidth() - rectPadding) / 2 + 10;
          })
          .attr("dy", function(d) {
            return 20;
          })
          .text(function(d) {
            return d;
          })
          .attr("text-anchor", "middle")
          .attr("fill", "white");

        /**
         * 添加均线
         */
        let meanValue = d3.mean(dataset);
        let meanNum = padding.top + yScale(meanValue);
        svg.append('line')
          .attr('x1', 0)
          .attr('y1', meanNum)
          .attr('x2', 500)
          .attr('y2', meanNum)
          .attr('stroke', 'red')
      }

      /**
       *
       */
      function drawScatterplot(chartCanvasId, chartContent, dataset) {
      }

      /**
       *
       */
      function drawText(chartCanvasId, chartContent) {
        d3.select("body")
          .selectAll("#" + chartCanvasId)
          .text("www.ourd3js.com").style("color", "red");
      }

      /**
       *
       */
      function drawWordCloud(chartCanvasId, chartContent) {
        const canvasWidth = 800;
        const canvasHeight = 400;

        const svg = d3.select("#" + chartCanvasId)
          .append("svg")
          .attr("width", canvasWidth)
          .attr("height", canvasHeight);

        const padding = {
          left: 30,
          right: 30,
          top: 20,
          bottom: 20
        };

        if (chartCanvasId == 'd3-cloud-meeting-1') {
          var dataset = [
            {
              name: 'ppp',
              count_field: 20,
            },
            {
              name: 'ooo',
              count_field: 10,
            },
          ];
          dataset = chartContent['data']['datasets'];
          drawCloudLayout(dataset);
        }
        else if (chartCanvasId == 'd3-cloud-sample-1') {
          // Draw Sample
          const pathToJSON = siteBaseUrl + "modules/custom/flexservice/htmlpage/data/all_time_olympic_medal.json";

          d3.json(pathToJSON).then(function(rawData) {
            // 转换数据键值 为标准的'name'和'count_field'
            const dataset = rawData.map(function(element) {
              var elementCopy = element;
              elementCopy['name'] = element['team'];
              elementCopy['count_field'] = element['summer_gold'];
              return element;
            });
            // 作用域，dataset 需要放在里面
            drawCloudLayout(dataset);
          });
        }

        function drawCloudLayout(dataset) {
          const maxValue = d3.max(dataset, function(d, i) {
            return parseInt(d.count_field);
          });

          const wordScale = d3.scaleLinear()
            .domain([0, maxValue])
            .range([10, 100]);

          d3.layout.cloud()
            .size([800, 400])
            .rotate(0)
            .words(dataset)
            .fontSize( function(d) {
              return wordScale(parseInt(d.count_field));
            })
            .on("end", drawWordTag)
            .start();
        }

        function drawWordTag(words) {
          svg.append("g")
            .attr("transform", "translate(400, 200)")
            .selectAll("text")
            .data(words)
            .enter()
            .append("text")
              .style("font-size", d => d.size + "px")
              .style("fill", "#7f7f7f")
              .attr("text-anchor", "middle")
              .attr("transform", d =>
                `translate(${[d.x, d.y]}) rotate(${d.rotate})`)
              .text(d => d.name);
        }
      }

    }
  };
})(jQuery, Drupal);
