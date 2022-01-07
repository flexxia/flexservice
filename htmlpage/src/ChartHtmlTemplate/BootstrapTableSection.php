<?php

/**
 * @file
 */

namespace Drupal\htmlpage\ChartHtmlTemplate;

/**
 * BootstrapTable.
 * \Drupal::service('htmlpage.charthtmltemplate.section.bootstraptable')->demo();
 */
class BootstrapTableSection {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   * BootstrapTable.
   */
  public function blockBootstrapTableTemplate(array $block_data = []) {
    $output = $this->tableWrapper($this->tableThead(), $this->tableTbody());
    return $output;
  }

  /**
   *
   */
  public function tableWrapper($table_thead, $table_tbody = NULL) {
    $output = '';
    $output .= '<div class="">';
      $output .= '<table
          data-toggle="table"
          data-pagination="true"
          data-page-number="2"
          data-search="true"
        >';
        $output .= $table_thead;
        $output .= $table_tbody;
      $output .= '</table>';
    $output .= '</div>';

    return $output;
  }

  /**
   * BootstrapTable
   */
  public function tableThead(array $block_data = []) {
    $output = '
      <thead>
        <tr>
          <th data-sortable="true">Item ID</th>
          <th>Item Name</th>
          <th>Item Price</th>
        </tr>
      </thead>';

    return $output;
  }

  /**
   *
   */
  public function tableTbody(array $block_data = []) {
    $output = '
      <tbody>
        <tr>
          <td>1</td>
          <td>Item 1</td>
          <td>$1</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Item 2</td>
          <td>$3</td>
        </tr>
      </tbody>';

    return $output;
  }

}
