/**
 * @file puppeteer debug
 */
(function ($, Drupal) {
  Drupal.behaviors.htmlpagePrintPdf = {
    attach: function (context, settings) {
      // 判断是否等于“document”，等于让代码只运行一次。
      if (context !== document) {
        return;
      }

      // console.log("from Drupal.behaviors.htmlpagePrintPdf");
      var ctxId = document.getElementById("htmlpage-draw-canvas");
      // check if Element is null

      // $(".print-pdf-debug-link").click(function() {
      //   console.log(100);
      //   alert("The paragraph was clicked.");
      // });

      /**
       * 分页算法
       */
      // load 事件监听
      window.onload = (event) => {
        // console.log('page is fully loaded');
      };

      // img 标签加载状态轮询（在 load 事件触发后执行）
      const flexSections = document.querySelectorAll('div.flex-section-wrapper');
      // console.log({flexSections});
    }
  };
})(jQuery, Drupal);
