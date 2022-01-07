/**
 *
 */
(function ($, Drupal) {
  Drupal.behaviors.htmlpageDrawCanvas = {
    attach: function (context, settings) {

      // 判断是否等于“document”，等于让代码只运行一次。
      if (context !== document) {
        return;
      }

      var ctxId = document.getElementById("htmlpage-draw-canvas");
      var ctx = ctxId.getContext("2d");

      ctx.fillStyle = "#f24b99";
      ctx.fillRect(10, 10, 280, 120);

      ctx.fillStyle = "#f2f2f2";
      ctx.font = "16px serif";
      ctx.fillText("Hello World!", 50, 50);

      ctx.font = "18px Verdana";
      ctx.fillText("Js Draw Canvas", 60, 90);
    }
  };
})(jQuery, Drupal);

/**
 * Save Canvas as PNG
 */
function exportCanvasAsPNG(id = "htmlpage-draw-canvas", fileName = 'demo33_image') {
  var canvasElement = document.getElementById(id);

  var MIME_TYPE = "image/png";
  var imgUrl = canvasElement.toDataURL(MIME_TYPE);
  var dlLink = document.createElement('a');

  dlLink.download = fileName;
  dlLink.href = imgUrl;
  dlLink.dataset.downloadurl = [MIME_TYPE, dlLink.download, dlLink.href].join(':');

  document.body.appendChild(dlLink);
  dlLink.click();
  document.body.removeChild(dlLink);
}
