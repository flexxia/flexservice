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
  public $titleRowHeight = 38;
	public $eachCommentRowHeight = 26;

  public $commentFontSize = 12;

  public $frameSizeCol12 = 1280;

  // content position
  public $contentXPositionCol12 = 128;
  public $contentYFirstRowPositionCol12 = 72;

  // frame position
  public $frameLeftXPosition = 110 + 12;
  public $totalPageHeight = 676;

  // frame bg color
  public $headerFillColorR = 0;
  public $headerFillColorG = 157;
  public $headerFillColorB = 223;

  // frame text color
  public $textColorR = 255;
  public $textColorG = 255;
  public $textColorB = 255;

  function getHeaderFillColorR() {
    return $this->headerFillColorR;
  }

  function setHeaderFillColorR($parameter) {
    $this->headerFillColorR = $parameter;
  }

  function getHeaderFillColorG() {
    return $this->headerFillColorG;
  }

  function setHeaderFillColorG($parameter) {
    $this->headerFillColorG = $parameter;
  }

  function getHeaderFillColorB() {
    return $this->headerFillColorB;
  }

  function setHeaderFillColorB($parameter) {
    $this->headerFillColorB = $parameter;
  }

  function getTextColorR() {
    return $this->textColorR;
  }

  function setTextColorR($parameter) {
    $this->textColorR = $parameter;
  }

  function getTextColorG() {
    return $this->textColorG;
  }

  function setTextColorG($parameter) {
    $this->textColorG = $parameter;
  }

  function getTextColorB() {
    return $this->textColorB;
  }

  function setTextColorB($parameter) {
    $this->textColorB = $parameter;
  }

  function getTitleRowHeight() {
    return $this->titleRowHeight;
  }

  function setTitleRowHeight($parameter) {
    $this->titleRowHeight = $parameter;
  }

  function getEachCommentRowHeight() {
    return $this->eachCommentRowHeight;
  }

  function setEachCommentRowHeight($parameter) {
    $this->eachCommentRowHeight = $parameter;
  }

  function getCommentFontSize() {
    return $this->commentFontSize;
  }

  function setCommentFontSize($parameter) {
    $this->commentFontSize = $parameter;
  }

  function getFrameSizeCol12() {
    return $this->frameSizeCol12;
  }

  function setFrameSizeCol12($parameter) {
    $this->frameSizeCol12 = $parameter;
  }

  function getContentXPositionCol12() {
    return $this->contentXPositionCol12;
  }

  function setContentXPositionCol12($parameter) {
    $this->contentXPositionCol12 = $parameter;
  }

  function getContentYFirstRowPositionCol12() {
    return $this->contentYFirstRowPositionCol12;
  }

  function setContentYFirstRowPositionCol12($parameter) {
    $this->contentYFirstRowPositionCol12 = $parameter;
  }

  function getFrameLeftXPosition() {
    return $this->frameLeftXPosition;
  }

  function setFrameLeftXPosition($parameter) {
    $this->frameLeftXPosition = $parameter;
  }

  function getTotalPageHeight() {
    return $this->totalPageHeight;
  }

  function setTotalPageHeight($parameter) {
    $this->totalPageHeight = $parameter;
  }

}