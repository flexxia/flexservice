<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoEntityService.php.
 */
namespace Drupal\flexinfo\Service;

/**
 * An example Service container.
 *
   \Drupal::service('flexinfo.entity.service')->getEntity($entity_type);
 */
class FlexinfoEntityService {

  /**
   * Entity
   * @param $entity_type
   */
  function getEntity($entity_type) {
    switch ($entity_type) {
      case 'calc':
        $container = \Drupal::service('flexinfo.calc.service');
        break;

      case 'chart':
        $container = \Drupal::service('flexinfo.chart.service');
        break;

      case 'entityform':
        $container = \Drupal::service('flexinfo.entityform.service');
        break;

      case 'field':
        $container = \Drupal::service('flexinfo.field.service');
        break;

      case 'json':
        $container = \Drupal::service('flexinfo.json.service');
        break;

      case 'node':
        $container = \Drupal::service('flexinfo.node.service');
        break;

      case 'querynode':
        $container = \Drupal::service('flexinfo.querynode.service');
        break;

      case 'queryterm':
        $container = \Drupal::service('flexinfo.queryterm.service');
        break;

      case 'queryuser':
        $container = \Drupal::service('flexinfo.queryuser.service');
        break;

      case 'setting':
        $container = \Drupal::service('flexinfo.setting.service');
        break;

      case 'term':
        $container = \Drupal::service('flexinfo.term.service');
        break;

      case 'user':
        $container = \Drupal::service('flexinfo.user.service');
        break;

      default:
        break;
    }

    return $container;
  }

}
