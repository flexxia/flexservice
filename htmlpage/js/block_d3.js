/**
 * @file
 * D3
 *
 * Behaviors Name is unique.
 */

(function ($, Drupal) {
  Drupal.behaviors.htmlpageD3DefaultChart = {
    attach: function (context, settings) {

      // Run Once.
      // Once('id') is unique.
      $('.htmlpage-wrapper', context).once('htmlpageBehaviorD3').each(function () {
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
              if (item['chart_library'] == "d3") {
                if ($('#' + item['chart_canvas_id']).length) {
                  switch (item['content']['type']) {
                    case "7fairy":
                      draw7FairyChart(item['chart_canvas_id'], item['content']);
                      break;
                    case "axis":
                      drawAxisChart(item['chart_canvas_id'], item['content']);
                      break;
                    case "bar":
                      drawBarChart(item['chart_canvas_id'], item['content']);
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
                      // drawScatterplotWithAsync(item['chart_canvas_id'], item['content']);
                      // drawScatterplotWithAxios(item['chart_canvas_id'], item['content']);
                      break;
                    case "violin_plot":
                      // drawViolinplot(item['chart_canvas_id'], item['content']);
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
        function pathLineUpdate() {
          var points = [
            [0, 80],
            [100, 100],
            [200, 30],
            [300, 50],
            [400, 40],
            [500, 80],
            [600, 60]
          ];

          var pathData = d3.line().curve(d3.curveCardinal)(points);
          d3.select(".line-path-chart-wrapper")
            .select('path')
            .style('fill', "none")
            .style('stroke', "red")
            .attr('d', pathData);
        }

        pathLineUpdate()

        //
        d3.select("body").selectAll("#" + chartCanvasId).text("draw7FairyChart");

        let myData = [40, 10, 20, 60, 30];
        let myData2 = [40, 10, 20, 60, 30, 50, 25];

        function barUpdate(data) {
          d3.select('.rect-chart')
            .selectAll('rect')
            .data(data)
            .join('rect')
            .attr('width', 30)
            .attr('height', function(d, i) {
              return d;
            })
            .attr('x', function(d, i) {
              return i * 40 + 40;
            })
            .attr('y', function(d, i) {
              return 100 - d;
            })
            .style('stroke', '#70d5dd')
            .style('fill', 'orange');
        }

        barUpdate(myData);

        function circleUpdate(data) {
          d3.select('.circle-chart')
            .append('svg')
              .attr('width', 900)
            .selectAll('circle')
            .data(data)
            .join('circle')
            .attr('r', function(d, i) {
              return d;
            })
            .attr('cy', 60)
            .attr('cx', function(d, i) {
              return i * 120 + 50;
            })
            .style('fill', 'orange');

          d3.select('.circle-chart svg').selectAll('circle')
            .data(myData2)
            .style('fill', 'red')
            .enter()
              .append('circle')
                .attr('width', 700)
                .attr('r', function(d, i) {
                  return d;
                })
                .attr('cy', 60)
                .attr('cx', function(d, i) {
                  return i * 120 + 50;
                })
                .style('fill', 'green')
            .exit()
              .style('fill', 'blue');
        }

        circleUpdate(myData);

        // d3.selectAll('circle')
        //   .style('fill', 'orange')
        //   .attr('r', function() {
        //     return 10 + Math.random() * 40;
        //   });


        // d3.selectAll('circle')
        //   .remove()
        //   .append('rect')
        //   .style('fill', 'orange')
        //   .classed('background', true)
        //   .attr('x', function(d, i) {
        //     return i * 40 + 20;
        //   });


        // d3.selectAll('rect')
        //   // .html('<rect width="30" height="30" y="120" />')
        //   .style('fill', 'orange')
        //   .classed('background', true)
        //   .attr('x', function(d, i) {
        //     return i * 40 + 20;
        //   });
      }

      /**
       *
       */
      function drawAxisChart(chartCanvasId, chartContent) {
        // domain specifies the input extent.
        // range defines the output extent of the scale.
        let scale = d3.scaleLinear().domain([0, 30]).range([0, 500]);

        let axisLeft = d3.axisLeft(scale);
        let axisRight = d3.axisRight(scale);
        let axisTop = d3.axisTop(scale);
        let axisBottom = d3.axisBottom(scale);

        // Number of ticks.
        axisBottom.ticks(5);

        // Tick label formatting and Number of ticks.
        axisBottom.ticks(9)
          .tickFormat(function(d) {
            return d + "%";
          });

        d3.select("body").selectAll("#" + chartCanvasId).select("svg")
          .append('g')
            .attr("transform", "translate(30, 60)")
          .call(axisLeft)
          .append('g')
            .attr("transform", "translate(30, 20)")
          .call(axisBottom);

      }

      /**
       *
       */
      function drawText(chartCanvasId, chartContent) {
        d3.select("body").selectAll("#" + chartCanvasId).text("www.ourd3js.com").style("color", "red");
      }

      /**
       * https://doc.yonyoucloud.com/doc/wiki/project/d3wiki/makechart.html
       */
      function drawBasicMap(chartCanvasId, chartContent) {
        var siteBaseUrl = settings.path.baseUrl;
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
        // 添加 SVG 画布.
        var width = 300;    // 画布的宽度
        var height = 200;   // 画布的高度

        var svg = d3.select("#" + chartCanvasId)     // 选择文档中的body元素
            .append("svg")              // append添加一个svg元素
            .attr("width", width)       // 设定宽度
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
         * 开发者需要指定 domain 和 range 的范围，如此即可得到一个计算关系。
         * domain 和 range 分别被称为 定义域 和 值域，
         * 线性比例尺，能将一个连续的区间，映射到另一区间
         * 解决柱形图高度/宽度的问题，就需要线性比例尺
         * https://doc.yonyoucloud.com/doc/wiki/project/d3wiki/scale.html
         */
        // 给柱形图添加线性比例尺
        var xScale = d3.scaleBand()
          .domain(d3.range(0, dataset.length))
          .rangeRound([0, width - padding.left - padding.right])
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
       * async/await。被称为到目前最优雅的异步过程解决方案
       */
      async function drawScatterplotWithAsync(chartCanvasId, chartContent) {
        var siteBaseUrl = settings.path.baseUrl;
        const pathToJSON = siteBaseUrl + "modules/custom/flexservice/htmlpage/data/seattle_wa_weather_data.json";

        // Access data
        const dataset = await d3.json(pathToJSON);

        drawScatterplot(chartCanvasId, chartContent, dataset)
      }

      /**
       * Axios 是一个基于 promise 的 HTTP 库
       */
      function drawScatterplotWithAxios(chartCanvasId, chartContent) {
        var siteBaseUrl = settings.path.baseUrl;
        const pathToJSON = siteBaseUrl + "modules/custom/flexservice/htmlpage/data/seattle_wa_weather_data.json";
        axios.get(pathToJSON)
          .then(function (response) {
            dataset = response.data;
            drawScatterplot(chartCanvasId, chartContent, dataset);
          })
          .catch(function (error) {
            console.log(error);
          });
      }

      /**
       * https://github.com/TheRobBrennan/explore-data-visualization-with-D3/tree/master/examples/02-scatterplot
       */
      function drawScatterplot(chartCanvasId, chartContent, dataset) {
        const xAccessor = d => d.dewPoint
        const yAccessor = d => d.humidity
        // Let's show how the amount of cloud cover varies based on humidity and dew point
        const colorAccessor = d => d.cloudCover

        // Create chart dimensions
        // REMEMBER: For scatter plots, we typically want square charts so axes do not appear squished
        //           In this example, we want to use whatever is smaller - the width or height of our chart area.
        //
        // d3.min() offers a whole host of benefits/safeguards; which is why it is preferable when creating charts
        const width = d3.min([window.innerWidth * 0.9, window.innerHeight * 0.9])

        let dimensions = {
          width: width,
          height: width,
          margin: {
            top: 10,
            right: 10,
            bottom: 50,
            left: 50,
          },
        }
        dimensions.boundedWidth = dimensions.width - dimensions.margin.left - dimensions.margin.right
        dimensions.boundedHeight = dimensions.height - dimensions.margin.top - dimensions.margin.bottom

        // Draw canvas
        const wrapper = d3.select("#" + chartCanvasId)
          .append("svg")
            // Note that these width and height sizes are the size "outside" of our plot
            .attr("width", dimensions.width)
            .attr("height", dimensions.height)

        const bounds = wrapper.append("g")
          // Create a group element to move the inner part of our chart to the right and down
          .style("transform", `translate(${
            dimensions.margin.left
          }px, ${
            dimensions.margin.top
          }px)`)

        // Create scales
        const xScale = d3.scaleLinear()
          .domain(d3.extent(dataset, xAccessor))  // Find the min and max values
          .range([0, dimensions.boundedWidth])    // Display values appropriately
          // Current scale would be [8.19, 58.38] - let's use .nice() to make a friendlier scale
          .nice()
          // Now our scale is [5, 60] - offering better readability and avoiding smushing dots to the edge

        const yScale = d3.scaleLinear()
          .domain(d3.extent(dataset, yAccessor))  // Find the min and max values
          .range([dimensions.boundedHeight, 0])   // Invert the range so the axis runs bottom-to-top
          // Current scale would be [0.27, 0.93] - let's use .nice() to make a friendlier scale
          .nice()
          // Now our scale is [0.25, 0.95] - offering better readability and avoiding smushing dots to the edge

        const colorScale = d3.scaleLinear()
          .domain(d3.extent(dataset, colorAccessor))  // Find the min and max values
          .range(["skyblue", "darkslategrey"])


          // Draw data
        // REMEMBER: For scatter plots, we want one element per data point - not a line that covers all data points
        //  We will use the <circle> SVG element - setting x, y, and the radius (half of its width or height)

        // // Test circle
        // bounds.append("circle")
        //   .attr("cx", dimensions.boundedWidth / 2)
        //   .attr("cy", dimensions.boundedHeight / 2)
        //   .attr("r", 5)

        // // Idea 1 - Naively map over each element in the dataset and append a circle to our bounds
        // dataset.forEach(d => {
        //   bounds
        //     .append("circle")
        //     .attr("cx", xScale(xAccessor(d)))
        //     .attr("cy", yScale(yAccessor(d)))
        //     .attr("r", 5)
        // })
        // // ...so what's the problem? We are adding a level of nesting, making our code harder to follow. The bigger issue, though, is that if we run this function multiple times, we'll end up drawing multiple sets of dots. When we start updating charts, we'll want to draw and update our data with the same code to prevent repeating ourselves.

        // Let's handle the dots without using a loop.
        // const dots = bounds.selectAll("circle") // Returns an array of matching "circle" elements
        //   // ...but wait. We don't have any dots yet! Why are we doing this? We want this selection to be aware of dots that already exist
        //   .data(dataset)  // Pass our dataset to the selection
        //   // This joins our selected elements with our array of data points. The returned selection will have a list of existing elements, new elements that need to be added, and old elements that need to be removed

        /* WAIT! Let's understand what's going on here. When we join our current dots with our data, we will have
        the following keys to look at:
          Existing dots -> These will be contained in the _groups key
          New dots to render -> These will be contained in the _enter key
          Dots to remove because their data points are not in the dataset -> These will be contained in the _exit key
          // Prove this!
          let theDots = bounds.selectAll("circle")
          console.log(theDots)  // Notice how _groups is an array with an empty node list; no dots exist
          theDots = theDots.data(dataset) // Bind our dataset
          console.log(theDots)  // Notice how _enter has an array of 365 items; and _groups now has an array of 365 items
        */

        // // Idea 2 - Ta-daaaaa! Here are the dots!!
        // const dots = bounds.selectAll("circle") // Returns an array of matching "circle" elements
        //     .data(dataset)  // Pass our dataset to the selection
        //     .enter()  // Grab the selection of new dots to render (contained in _enter)
        //       .append("circle") // Create a circle element for each new dot(s)
        //         .attr("cx", d => xScale(xAccessor(d)))
        //         .attr("cy", d => yScale(yAccessor(d)))
        //         .attr("r", 5)
        //         // Let's make the dots a lighter color to help them stand out
        //         .attr("fill", "cornflowerblue")

        // EXERCISE 01: Split the dataset in two and draw both parts separately (comment out Idea 2 for the moment)
        function drawDots(dataset) {
          const dots = bounds.selectAll("circle").data(dataset)

          // Notice how only the new dots have the supplied color when we call drawDots multiple times?
          // dots
          //   .enter().append("circle")
          //     .attr("cx", d => xScale(xAccessor(d)))
          //     .attr("cy", d => yScale(yAccessor(d)))
          //     .attr("r", 5)
          //     .attr("fill", color)

          // Want to have all of the drawn dots to have the same color?
          // Notice how this example breaks the ability to chain.
          // dots
          //   .enter().append("circle")

          // bounds.selectAll("circle")
          //     .attr("cx", d => xScale(xAccessor(d)))
          //     .attr("cy", d => yScale(yAccessor(d)))
          //     .attr("r", 5)
          //     .attr("fill", color)

          // // Let's have all of the drawn dots have the same color AND use merge() so we can create a chain
          // dots
          //   .enter().append("circle")
          //   .merge(dots)  // Merge already drawn/existing dots with the new ones AND keep our chain going
          //     .attr("cx", d => xScale(xAccessor(d)))
          //     .attr("cy", d => yScale(yAccessor(d)))
          //     .attr("r", 5)
          //     .attr("fill", color)

          // Great news! Since d3-selection version 1.4.0, we can use a join() method - which is a shortcut for running the enter(), append(), merge(), and other methods
          dots.join("circle")
            .attr("cx", d => xScale(xAccessor(d)))
            .attr("cy", d => yScale(yAccessor(d)))
            .attr("r", 5)
            .attr("fill", d => colorScale(colorAccessor(d)))  // Fill based on our new color scale for cloud cover

        }
        // Now let's call this function with a subset of our data
        drawDots(dataset.slice(0, 200))
        // After one second, let's call this function with our whole dataset and a blue color to distinguish our two sets of dots
        setTimeout(() => {
          drawDots(dataset)
        }, 1000)
        // END EXERCISE 01

        // Draw peripherals

        // x axis
        const xAxisGenerator = d3.axisBottom().scale(xScale)
        // Remember to translate the x axis to move it to the bottom of the chart bounds
        const xAxis = bounds.append("g")
          .call(xAxisGenerator)
            .style("transform", `translateY(${dimensions.boundedHeight}px)`)

        // Label for the x axis
        const xAxisLabel = xAxis.append("text") // Append a text element to our SVG
          .attr("x", dimensions.boundedWidth / 2) // Position it horizontally centered
          .attr("y", dimensions.margin.bottom - 10) // Position it slightly above the bottom of the chart
          // Explicitly set fill to black because D3 sets a fill of none by default on the axis "g" element
          .attr("fill", "black")
          // Style our label
          .style("font-size", "1.4em")
          // Add text to display on label
          .html("Dew point (&deg;F)")

        // y axis
        const yAxisGenerator = d3.axisLeft()
          .scale(yScale)
          // Cut down on visual clutter and aim for a certain number (4) of ticks
          .ticks(4)
          // Note that the resulting axis won't necessarily have exactly 4 ticks. It will aim for four ticks, but also use friendly intervals to get close. You can also specify exact values of ticks with the .ticksValues() method

        const yAxis = bounds.append("g")
          .call(yAxisGenerator)

        // Label for the y axis
        const yAxisLabel = yAxis.append("text")
          // Draw this in the middle of the y axis and just inside the left side of the chart wrapper
          .attr("x", -dimensions.boundedHeight / 2)
          .attr("y", -dimensions.margin.left + 10)
          .attr("fill", "black")
          .style("font-size", "1.4em")
          .text("Relative humidity")
          // Rotate the label to find next to the y axis
          .style("transform", "rotate(-90deg)")
          // Rotate the label around its center
          .style("text-anchor", "middle")

        // Set up interactions
      }

      /**
       * https://www.d3-graph-gallery.com/graph/violin_basicHist.html
       */
      function drawViolinplot(chartCanvasId, chartContent) {
        var siteBaseUrl = settings.path.baseUrl;
        const pathToJSON = siteBaseUrl + "modules/custom/flexservice/htmlpage/data/iris.json";
        axios.get(pathToJSON)
          .then(function (response) {
            dataset = response.data;
            drawViolinplotFn(chartCanvasId, chartContent, dataset);
          })
          .catch(function (error) {
            console.log(error);
          });

        function drawViolinplotFn(chartCanvasId, chartContent) {
          // set the dimensions and margins of the graph
          var margin = {top: 10, right: 30, bottom: 30, left: 40};
          var width = 460 - margin.left - margin.right;
          var height = 400 - margin.top - margin.bottom;

          // append the svg object to the body of the page
          var svg = d3.select("#" + chartCanvasId)
            .append("svg")
              .attr("width", width + margin.left + margin.right)
              .attr("height", height + margin.top + margin.bottom)
            .append("g")
              .attr("transform", "translate(" + margin.left + ", " + margin.top + ")");

          // Build and Show the Y scale
          var yScale = d3.scaleLinear()
            .domain([ 3.5,8 ])          // Note that here the Y scale is set manually
            .range([height, 0])
          svg.append("g").call( d3.axisLeft(yScale) )

          // Build and Show the X scale. It is a band scale like for a boxplot: each group has an dedicated RANGE on the axis. This range has a length of x.bandwidth
          var xScale = d3.scaleBand()
            .range([ 0, width ])
            .domain(["setosa", "versicolor", "virginica"])
            .padding(0.05)     // This is important: it is the space between 2 groups. 0 means no padding. 1 is the maximum.
          svg.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(xScale))

          // Features of the histogram
          var histogram = d3.histogram()
              .domain(yScale.domain())
              .thresholds(yScale.ticks(20))    // Important: how many bins approx are going to be made? It is the 'resolution' of the violin plot
              .value(d => d)

          // Compute the binning for each group of the dataset
          const nameGroupCount = dataset.reduce((obj, {
            Species,
            Sepal_Length
          }) => {
            if (!(Species in obj)) {
              obj[Species] = {
                [Sepal_Length]: 1
              };
            }
            else {
              // I.e. if the year doesn't exist, default to zero
              obj[Species][Sepal_Length] = (obj[Species][Sepal_Length] || 0) + 1
            }
            return obj;
          }, {});

          // Now transform it into the desired format
          var sumstat = Object.entries(nameGroupCount)
            .map(([Species, yearsCount]) => {
              const value = Object.entries(yearsCount)
                .map(([Sepal_Length, count]) => ({
                  key: Sepal_Length,
                  length: count
                }));

              return {
                key: Species,
                value
              };
            });


          /**
           *
           */

          var ppp = function(ddd) {
            return(ddd);
          };

          var sumstat2 = d3.group(dataset, d => d.Species);

          var sumstat3 = d3.rollup(dataset,
            v => v.length,
            d => d.Species,
            // d => d.Sepal_Length,
            d => ppp(d.Sepal_Length)
          );

          // MapIterator
          // console.log(sumstat2.values());


          /**
           *
           */
          // var sumstat = d3.nest() // nest function allows to group the calculation per level of a factor
            // .key(function(d) { return d.Species;})
            // .rollup(function(d) {   // For each key..
            //   input = d.map(function(g) { return g.Sepal_Length;})    // Keep the variable called Sepal_Length
            //   bins = histogram(input)   // And compute the binning on it.
            //   return(bins)
            // })
            // .entries(dataset)

          // What is the biggest number of value in a bin? We need it cause this value will have a width of 100% of the bandwidth.
          var maxNum = 0

          for ( i in sumstat ) {
            allBins = sumstat[i].value;
            lengths = allBins.map(function(a) {
              return a.length;
            })

            longuest = d3.max(lengths)
            if (longuest > maxNum) {
              maxNum = longuest
            }
          }

          // The maximum width of a violin must be x.bandwidth = the width dedicated to a group
          var xNum = d3.scaleLinear()
            .range([0, xScale.bandwidth()])
            .domain([-maxNum, maxNum])


          // Add the shape to this svg!
          svg
            .selectAll("myViolin")
            .data(sumstat)
            .enter()        // So now we are working group per group
            .append("g")
              .attr("transform", function(d){ return("translate(" + xScale(d.key) + ", 0)") } ) // Translation on the right to be at the group position
            .append("path")
              .datum(function(d){ return(d.value)})     // So now we are working bin per bin
              .style("stroke", "none")
              .style("fill", "#69b3a2")
              .attr("d", d3.area()
                .x0(function(d) {
                  x0Num = xNum(-d.length);
                  if(!isNaN(x0Num)) {
                    return(x0Num);
                  }
                  else {
                    // console.log(x0Num);
                  }
                })
                .x1(function(d) {
                  x1Num = xNum(d.length);
                  if(!isNaN(x1Num)) {
                    return(x1Num);
                  }
                  else {
                    // console.log(x1Num);
                  }
                })
                .y(function(d) {
                  yNum = yScale(d.key);
                  if(!isNaN(yNum)) {
                    return(yNum);
                  }
                  else {
                    // console.log(yNum);
                  }
                })
                .curve(d3.curveCatmullRom)    // This makes the line smoother to give the violin appearance. Try d3.curveStep to see the difference
              )
        }
      }

    }
  };
})(jQuery, Drupal);
