/**
 * @file
 * Using puppeteer to print pdf
 */

/**
 * process.argv属性返回一个数组，包含启动Node.js进程时传递的命令行参数
 * 第一个元素将是process.execPath。如果需要访问argv [0]的原始值，请参见process.argv0
 * 第二个元素将是要执行的JavaScript文件的路径。
 * 其余元素将是任何其他命令行参数
 */
console.log("from print pdf");
// console.log(JSON.stringify(process.argv));

var guestPageUrl = process.argv[2].replace('--page_url=', '');
var exportFileName = process.argv[3].replace('--file_name=', '');

/**
 * @file
 cd /var/ubuntushare/www/wai/nodejs/puppeteer/demo
 node sample/sample013.js
 */
require('../node_modules/puppeteer');
const puppeteer = require('puppeteer');

/**
 * The pitfall of using async/await is the need to try/catch your asynchronous call in case of an error
 * node.js might terminate the process in the future if such unhandled promises occur
 */
(async () => {
  try {
    const browser = await puppeteer.launch({
      headless: true,
      args: [
          "--no-sandbox", // linux系统中必须开启, 在linux root账号下启动的puppeteer必须使用无沙箱模式
          "--no-zygote",
          // "--single-process", // 此处关掉单进程
          "--disable-setuid-sandbox",
          "--disable-gpu",
          "--disable-dev-shm-usage",
          "--no-first-run",
          "--disable-extensions",
          "--disable-file-system",
          "--disable-background-networking",
          "--disable-default-apps",
          "--disable-sync", // 禁止同步
          "--disable-translate",
          "--hide-scrollbars",
          "--metrics-recording-only",
          "--mute-audio",
          "--safebrowsing-disable-auto-update",
          "--ignore-certificate-errors",
          "--ignore-ssl-errors",
          "--ignore-certificate-errors-spki-list",
          "--font-render-hinting=medium",
      ]
      // defaultViewport:{
      //   width: 1600,
      //   height: 768
      // }
    });

    const page = await browser.newPage();

    // puppeteer允许对每个tab页单独设置尺寸
    // 设置tab页, 浏览时用的Display resolution，要调试生成pdf合适的分辨率
    // 或用上面defaultViewport
    await page.setViewport({
      width: 1024,
      height: 768
    });

    // var urlPath = 'http://u20.basicone.test/emd/web/htmlguest/meeting/page/2146/1641013200/1650336677';
    const urlPath = guestPageUrl;

    await page.goto(urlPath,
      {waitUntil: 'networkidle0'}
    );

    // 获取meta标签内容
    const metaContent = await page.$eval(
      'meta[name=viewport]',
      el => el.content
    );

    /**
     * 获得页面高度
     */
    const scrollDimension = await page.evaluate( () => {
      return {
        width: document.scrollingElement.scrollWidth,
        height: document.scrollingElement.scrollHeight
      }
    });

    // path, can be relative or absolute path,
    // 因为Drupal的程序是从/var/www/html/emd/web运行的
    // 所以nodejs 运行也从同一级目录从运行程序的位置算起
    await page.addStyleTag(
      {path: 'modules/custom/flexservice/htmlpage/nodejs/style/puppeteer_print_media.css'}
    )

    // 对整个页面截图
    // path: 'modules/custom/flexservice/htmlpage/nodejs/screenshot/screenshot.pdf',
    await page.pdf({
      path: exportFileName,
      // 页面高度, 再加上一个冗余，防止页面溢出导致分页
      height: (scrollDimension.height + 100),
      // 边滚动边截图
      fullPage: true,
      // 页面缩放比例， height_width_ratio
      scale: 1,
      // inconsistent page width or height
      // PDF 文件的宽度, 当没有设置format时，必须设置width
      width: "280mm",
      // CSS
      // preferCSSPageSize: true,
      // 开启渲染背景色，因为 puppeteer 是基于 chrome 浏览器的，浏览器为了打印节省油墨，默认是不导出背景图及背景色的
      // 坑点，必须加
      printBackground: true,
      // margin
      margin: {
        top: 0,
        right: 0,
        bottom: 0,
        left: 0,
      },
      // pdf存储单页大小, 如果规定了format，就会有分页
      format: "Letter",
    });

    await page.close();
    await browser.close();

    // 文件最后一个console.log(）的内容是PHP 调用这个文件的返回值，
    console.log("puppeteer save print successful");
  } catch (err) {
    console.log(err);
  }
})()

