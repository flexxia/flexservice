<?php

/**
 * @file
 */
namespace Drupal\genpdf\Content;

/**
 *
 */
class FactoryChartBlockData {

  // frame
  public $frameHeight = 472;
  public $frameWidth = 600;
  public $frameFirstRowYPosition = 148;

  // chart x position
  public $chartLeftXPositioncol6 = 294;
  public $chartRightXPositioncol6 = 908;

  // frame x position
  public $frameLeftXPositionCol6 = 110 + 12;
  public $frameRightXPositionCol6 = 110 + 626;

  public $currentPageNum = 0;
  public $barChartFirstRowYPositionCol = 472;
  public $percentTextShowedOnPieChart = 110;
  public $barChartHeight = 268;

  // header text
  public $headerLengthPerLine = "74";

  public $bottomTextSpaceXPosition = 110;
  public $bottomTextSpaceYPosition = 536;

  public $frameFirstRowBottomYPosition = 0;
  public $spaceBetweenFrameX = 24;
  public $spaceBetweenFrameY = 36;

  // bottom
  public $enableBottom = TRUE;
  public $bottomTextYPosition = 56;
  public $chartLeftYPosition = 56;
  public $barChartXPositionCol6L = 140;

  // frame bg color
  public $headerFillColorR = 0;
  public $headerFillColorG = 157;
  public $headerFillColorB = 223;

  // frame text color
  public $textColorR = 255;
  public $textColorG = 255;
  public $textColorB = 255;

  /**
   * magic methods
   */
  public function __set($propertyName, $value) {
    $this->$propertyName = $value;
  }

  /**
   * magic methods
   */
  public function __get($propertyName) {
    return $this->$propertyName;
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

  function getFrameHeight() {
    return $this->frameHeight;
  }

  function setFrameHeight($parameter) {
    $this->frameHeight = $parameter;
  }

  function getFrameWidth() {
    return $this->frameWidth;
  }

  function setFrameWidth($parameter) {
    $this->frameWidth = $parameter;
  }

  function getFrameFirstRowYPosition() {
    return $this->frameFirstRowYPosition;
  }

  function setFrameFirstRowYPosition($parameter) {
    $this->frameFirstRowYPosition = $parameter;
  }

  function getChartLeftXPositioncol6() {
    return $this->chartLeftXPositioncol6;
  }

  function setChartLeftXPositioncol6($parameter) {
    $this->chartLeftXPositioncol6 = $parameter;
  }

  function getChartRightXPositioncol6() {
    return $this->chartRightXPositioncol6;
  }

  function setChartRightXPositioncol6($parameter) {
    $this->chartRightXPositioncol6 = $parameter;
  }

  function getFrameLeftXPositionCol6() {
    return $this->frameLeftXPositionCol6;
  }

  function setFrameLeftXPositionCol6($parameter) {
    $this->frameLeftXPositionCol6 = $parameter;
  }

  function getFrameRightXPositionCol6() {
    return $this->frameRightXPositionCol6;
  }

  function setFrameRightXPositionCol6($parameter) {
    $this->frameRightXPositionCol6 = $parameter;
  }

  function getCurrentPageNum() {
    return $this->currentPageNum;
  }

  function setCurrentPageNum($parameter) {
    $this->currentPageNum = $parameter;
  }

  function getBarChartFirstRowYPositionCol() {
    return $this->barChartFirstRowYPositionCol;
  }

  function setBarChartFirstRowYPositionCol($parameter) {
    $this->barChartFirstRowYPositionCol = $parameter;
  }

  function getPercentTextShowedOnPieChart() {
    return $this->percentTextShowedOnPieChart;
  }

  function setPercentTextShowedOnPieChart($parameter) {
    $this->percentTextShowedOnPieChart = $parameter;
  }

  function getBarChartHeight() {
    return $this->barChartHeight;
  }

  function setBarChartHeight($parameter) {
    $this->barChartHeight = $parameter;
  }

  function getHeaderLengthPerLine() {
    return $this->headerLengthPerLine;
  }

  function setHeaderLengthPerLine($parameter) {
    $this->headerLengthPerLine = $parameter;
  }

  function getBottomTextSpaceXPosition() {
    return $this->bottomTextSpaceXPosition;
  }

  function setBottomTextSpaceXPosition($parameter) {
    $this->bottomTextSpaceXPosition = $parameter;
  }

  function getBottomTextSpaceYPosition() {
    return $this->bottomTextSpaceYPosition;
  }

  function setBottomTextSpaceYPosition($parameter) {
    $this->bottomTextSpaceYPosition = $parameter;
  }

  function getFrameFirstRowBottomYPosition() {
    return $this->frameFirstRowBottomYPosition;
  }

  function setFrameFirstRowBottomYPosition($parameter) {
    $this->frameFirstRowBottomYPosition = $parameter;
  }

  function getSpaceBetweenFrameX() {
    return $this->spaceBetweenFrameX;
  }

  function setSpaceBetweenFrameX($parameter) {
    $this->spaceBetweenFrameX = $parameter;
  }

  function getSpaceBetweenFrameY() {
    return $this->spaceBetweenFrameY;
  }

  function setSpaceBetweenFrameY($parameter) {
    $this->spaceBetweenFrameY = $parameter;
  }

  function getEnableBottom() {
    return $this->enableBottom;
  }

  function setEnableBottom($parameter) {
    $this->enableBottom = $parameter;
  }

  function getBottomTextYPosition() {
    return $this->bottomTextYPosition;
  }

  function setBottomTextYPosition($parameter) {
    $this->bottomTextYPosition = $parameter;
  }

  function getChartLeftYPosition() {
    return $this->chartLeftYPosition;
  }

  function setChartLeftYPosition($parameter) {
    $this->chartLeftYPosition = $parameter;
  }

  function getBarChartXPositionCol6L() {
    return $this->barChartXPositionCol6L;
  }

  function setBarChartXPositionCol6L($parameter) {
    $this->barChartXPositionCol6L = $parameter;
  }
}
