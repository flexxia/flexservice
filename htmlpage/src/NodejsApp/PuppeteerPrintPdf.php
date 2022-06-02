<?php

/**
 * @file
 */

namespace Drupal\htmlpage\NodejsApp;

/**
 * \Drupal::service('htmlpage.nodejsapp.puppeteer.print.pdf')->demo().
 */
class PuppeteerPrintPdf {

  /**
   *
   */
  public function executeNodejsScriptFromPhp($section = NULL, $entity_id = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {

    $node_js_path = $this->getNodejsPath();
    $guest_page = $this->getGuestPageUrl($section, $entity_id, $start_timestamp, $end_timestamp);
    $file_name = $this->getExportPdfFileName($section);
    $pdf_file_path = $this->getDownloadPdfFilePath($file_name);

    // @param $output is array, 是nodejs 代码中， 所有的console.log()的值
    // nodejs 代码中 最后一个console.log(）的内容是PHP 调用这个文件的返回值，
    $result = exec('node '. $node_js_path . ' --page_url=' . $guest_page . ' --file_name=' . $file_name . ' 2>&1', $out, $err);

    if ($result == 'puppeteer save print successful') {
      $output = '';
      $output .= '<a class="print-pdf-debug-link" href="' . $pdf_file_path . '">';
        $output .= 'Click to download Pdf file';
      $output .= '</a>';
    }
    else{
      $output = 'The Pdf is not generated successfully, Please contact administrator for more information';
    }

    return $output;
  }

  /**
   *
   */
  public function getNodejsPath() {
    /**
     * @todo
     * from terminal run
     * 因为Drupal的程序是从/var/www/html/emd/web运行的
     * 所以nodejs 运行也从同一级目录
     * 主要是为了下面的relativePath
     cd /var/ubuntushare/www/emd/web
     node modules/custom/flexservice/htmlpage/nodejs/puppeteer_print_pdf.js
     *
     *
     * call nodejs script from Drupal code snippet
     * 路径
     * /var/www/html/emd/web/modules/custom/flexservice/htmlpage/nodejs/puppeteer_print_pdf.js
     $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;
     $node_js_path = $app_root . '/modules/custom/flexservice/htmlpage/nodejs/puppeteer_print_pdf.js';
     *
     * 返回输出值
     * @param $output is array, 是nodejs 代码中， 所有的console.log()的值
     $result = exec("node ". $node_js_path . ' 2>&1', $out, $err);
     dpm($out);
     dpm($err);
     */
    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;
    $node_js_path = $app_root . '/modules/custom/flexservice/htmlpage/nodejs/puppeteer_print_pdf.js';

    return $node_js_path;
  }

  /**
   *
   */
  public function getGuestPageUrl($section = NULL, $entity_id = NULL, $start_timestamp = NULL, $end_timestamp = NULL) {
    $base_url = \Drupal::service('flexinfo.setting.service')
      ->getHttpsBaseUrl();
    $output = $base_url . '/htmlguest/' . $section . '/page/' . $entity_id . '/' . $start_timestamp . '/' . $end_timestamp;

    return $output;
  }

  /**
   *
   */
  public function getExportPdfFileName($section = NULL) {
    $pdf_folder_path = 'sites/default/files/pdf/' . $section . '_';
    $output = $pdf_folder_path . date("Y_m_d_H_i_s") . ".pdf";

    return $output;
  }

  /**
   *
   */
  public function getDownloadPdfFilePath($file_name = NULL) {
    $base_url = \Drupal::service('flexinfo.setting.service')
      ->getHttpsBaseUrl();
    $output = $base_url . '/' . $file_name;

    return $output;
  }

}
