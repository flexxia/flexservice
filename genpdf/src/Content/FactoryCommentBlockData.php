<?php

/**
 * @file
 */
namespace Drupal\genpdf\Content;

/**
 *
 */
class FactoryCommentBlockData {

  // row height
  private $titleRowHeight = 38;
  private $eachCommentRowHeight = 26;

  private $commentFontSize = 12;

  private $frameSizeCol12 = 1280;

  // content position
  private $contentXPositionCol12 = 128;
  private $contentYFirstRowPositionCol12 = 72;

  // frame position
  private $frameLeftXPosition = 110 + 12;
  private $totalPageHeight = 676;

  // frame bg color
  private $headerFillColorR = 0;
  private $headerFillColorG = 157;
  private $headerFillColorB = 223;

  // frame text color
  private $textColorR = 255;
  private $textColorG = 255;
  private $textColorB = 255;

  // Get data from inaccessible (protected or private) or non-existing properties.
  public  function __get($name){
    return $this->{$name};
  }

}
