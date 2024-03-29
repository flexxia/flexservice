<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoCalcService.php.
 */

namespace Drupal\flexinfo\Service;

/**
 * An example Service container.
   \Drupal::service('flexinfo.calc.service')->demo($pool_data);
 */
class FlexinfoCalcService {

  /**
   * @deprecated
   */
  public function arrayAverageByCount($pool_data = array(), $decimals = 2) {
    $output = 0;
    return $output;
  }

  /**
   * @deprecated
   */
  public function arrayAverageByMeetingNodes($meeting_nodes = array(), $question_tid = NULL, $decimals = 2) {
    return [];
  }

  /**
   * @deprecated
   * @return NPS/NTS-- Net Promoter Score for 1- 5
   */
  public function calcNtsScoreByMeetingNodes($meeting_nodes = array(), $plus_sign = TRUE) {
    return 0;
  }

  /**
   * @deprecated
   * @return NPS/NTS-- Net Promoter Score for 1- 5
   * NPS formula
   *
   * For First Question of Evaluation
   * Promoters  =  number of (score 5)
   * Passives   =  number of (score 3-4)
   * Detractors =  number of (score 1-2)
   * Total = Promoters + Passives + Detractors;
   * NPS =  (Promoters - Detractors) / Total;
   *
   \Drupal::service('flexinfo.calc.service')->calcNTSScore($pool_data);
   */
  public function calcNTSScore($pool_data = array(), $plus_sign = TRUE) {
    return 0;
  }

  /**
   * @return NPS/NTS-- Net Promoter Score for 1- 10
   * NPS formula  for 1- 10
   *
   * For First Question of Evaluation
   * Promoters  =  number of (score 9, 10)
   * not count  =  number of (score 7, 8)
   * Detractors =  number of (score 1-6)
   * Total = Promoters + Passives + Detractors;
   * NPS =  (Promoters - Detractors) / Total;
   *
   * $question_data = \Drupal::service('ngdata.node.evaluation')
           ->getRaidoQuestionData($question_term, $meeting_nodes);
   */
  public function calcNTSScoreScale10($question_data = array(), $plus_sign = TRUE) {
    $promoters  =  $question_data[0] + $question_data[1];
    $detractors =  $question_data[4] + $question_data[5] + $question_data[6] + $question_data[7] + $question_data[8] + $question_data[9];

    $total = array_sum($question_data);

    $nps = 0;
    if ($total != 0) {
      $nps = ($promoters - $detractors) / $total;
    }

    // format NPS score
    if (is_numeric($nps)) {
      $nps = round($nps, 2) * 100;
      if ($plus_sign) {
        if ($nps > 0) {
          // add "+" sign before Positive integer number
          $nps = '+' . $nps;
        }
      }
    }

    return $nps;
  }

  /**
   * avoid number 2 is zero or null
   \Drupal::service('flexinfo.calc.service')->getPercentage();
   */
  public function getPercentage($num1 = NULL, $num2 = NULL) {
    $output = 0;

    if ($num2 > 0) {
      $output = ($num1 / $num2) * 100;
    }

    return $output;
  }

  /**
   * avoid number 2 is zero or null
   \Drupal::service('flexinfo.calc.service')->getPercentageDecimal()
   */
  public function getPercentageDecimal($num1 = NULL, $num2 = NULL, $decimals = 2) {
    $result = $this->getPercentage($num1, $num2);
    $output = number_format((float)$result, $decimals, '.', '');

    return $output;
  }

  /**
   * @return
   \Drupal::service('flexinfo.calc.service')->getSumFromNodes();
   * count nodes number or count sum for specify field total
   */
  public function getSumFromNodes($entity_array = array(), $count_field = NULL) {
    $output = '';

    if (!$count_field) {
      $output = count($entity_array);
    }
    else {
      $output = array_sum(
        \Drupal::service('flexinfo.field.service')->getFieldFirstValueCollection($entity_array, $count_field)
      );
    }

    return $output;
  }

  /**
   * @return
   */
  public function getSumValue($entity_array = array(), $entity = NULL) {
    $output = '';

    return $output;
  }

  /**
   * @deprecated
   */
  public function percentagePoolDataByAnswerInt($pool_data = array(), $decimals = 2) {
    $output = array();
    return $output;
  }

  /**
   *
   */
  public function percentage5or4ByAnswerInt($pool_data = array(), $decimals = 2) {
    $output = $this->getPercentageDecimal(
      \Drupal::service('flexinfo.calc.service')->sum5or4ByAnswerInt($pool_data),
      array_sum($pool_data),
      $decimals
    );

    return $output;
  }

  /**
   *
   \Drupal::service('flexinfo.calc.service')->percentage5or4ByMeetingNodes($meeting_nodes, $question_tid);
   */
  public function percentage5or4ByMeetingNodes($meeting_nodes = array(), $question_tid = NULL, $decimals = 2) {
    $pool_data = \Drupal::service('flexinfo.querynode.service')->wrapperPoolAnswerIntDataByQuestionTid($meeting_nodes, $question_tid);

    $output = $this->percentage5or4ByAnswerInt($pool_data, $decimals);
    return $output;
  }

  /**
   *
   \Drupal::service('flexinfo.calc.service')->removeArrayEmptyElements();
   */
  public function removeArrayEmptyElements($param_array) {
    $output = array_filter($param_array);

    return $output;
  }

  /**
   *
   \Drupal::service('flexinfo.calc.service')->sum5or4ByAnswerInt();
   */
  public function sum5or4ByAnswerInt($pool_data = array()) {
    $sum = 0;
    if (isset($pool_data[3])) {
      $sum += $pool_data[3];
    }
    if (isset($pool_data[4])) {
      $sum += $pool_data[4];
    }

    return $sum;
  }

  /**
   * @todo Descending
   */
  public function uasortArrayDescending($param_array = array()) {
    uasort($param_array, function($a, $b) {
      return $b - $a;
    });

    return $param_array;
  }

  /**
   * @todo Ascending
   */
  public function uasortArrayAscending($param_array = array()) {
    uasort($param_array, function($a, $b) {
      return $a - $b;
    });

    return $param_array;
  }

}
