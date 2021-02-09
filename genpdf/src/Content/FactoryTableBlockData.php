<?php

/**
 * @file
 */
namespace Drupal\genpdf\Content;

/**
 *
 */
class FactoryTableBlockData {

  public $titleRowHeight = 38;
  public $totalPageHeight = 676;

	// content position
  public $contentXPositionCol12 = 40;
  public $contentYFirstRowPositionCol12 = 72;

  // frame position
  public $frameLeftXPosition = 110 + 12;

  public $currentTableLines = 0;
  public $previousPageTableLines = 0;

  // table characters
  public $tableHeaderHeight = 40;
  public $tableRowHeight = 48;
  public $tableWidth = 1364;
  public $tableFrameHeight = 480;

	public $frameSizeCol12 = 1280;
  public $frameFirstRowYPosition = 80;

  public $linesNumShowedEachPage = 16;

  // frame bg color
  public $headerFillColorR = 0;
  public $headerFillColorG = 157;
  public $headerFillColorB = 223;

  // frame text color
  public $textColorR = 255;
  public $textColorG = 255;
  public $textColorB = 255;

  function getTitleRowHeight() {
    return $this->titleRowHeight;
  }

  function setTitleRowHeight($parameter) {
    $this->titleRowHeight = $parameter;
  }

  function getTotalPageHeight() {
    return $this->totalPageHeight;
  }

  function setTotalPageHeight($parameter) {
    $this->totalPageHeight = $parameter;
  }

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

  function getLinesNumShowedEachPage() {
    return $this->linesNumShowedEachPage;
  }

  function setLinesNumShowedEachPage($parameter) {
    $this->linesNumShowedEachPage = $parameter;
  }

  function getFrameFirstRowYPosition() {
    return $this->frameFirstRowYPosition;
  }

  function setFrameFirstRowYPosition($parameter) {
    $this->frameFirstRowYPosition = $parameter;
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

  function getCurrentTableLines() {
    return $this->currentTableLines;
  }

  function setCurrentTableLines($parameter) {
    $this->currentTableLines = $parameter;
  }

  function getPreviousPageTableLines() {
    return $this->previousPageTableLines;
  }

  function setPreviousPageTableLines($parameter) {
    $this->previousPageTableLines = $parameter;
  }

  function getTableHeaderHeight() {
    return $this->tableHeaderHeight;
  }

  function setTableHeaderHeight($parameter) {
    $this->tableHeaderHeight = $parameter;
  }

  function getTableRowHeight() {
    return $this->tableRowHeight;
  }

  function setTableRowHeight($parameter) {
    $this->tableRowHeight = $parameter;
  }

  function getTableWidth() {
    return $this->tableWidth;
  }

  function setTableWidth($parameter) {
    $this->tableWidth = $parameter;
  }

  function getTableFrameHeight() {
    return $this->tableFrameHeight;
  }

  function setTableFrameHeight($parameter) {
    $this->tableFrameHeight = $parameter;
  }

  function getFrameSizeCol12() {
    return $this->frameSizeCol12;
  }

  function setFrameSizeCol12($parameter) {
    $this->frameSizeCol12 = $parameter;
  }

}