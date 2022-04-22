<?php

namespace Drupal\htmlpage\Atomic\Atom;

use Drupal\htmlpage\Atomic\HtmlpageAtomic;

/**
 * Class HtmlpageAtomicAtom.
 \Drupal::service('htmlpage.atomic.atom')->demo();
 */
class HtmlpageAtomicAtom extends HtmlpageAtomic {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   *
   */
  public function generateUniqueId() {
    $output = hexdec(substr(uniqid(NULL, TRUE), 15, 8));
    return $output;
  }

}
