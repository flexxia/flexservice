<?php

/**
 * @file
 * Contains Drupal\flexinfo\Service\FlexinfoQueryUserService.php.
 */
namespace Drupal\flexinfo\Service;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\Component\Utility\Timer;
/**
 * An example Service container.
 * \Drupal::getContainer()->get('flexinfo.queryuser.service')->runQueryWithGroup();
 */
class FlexinfoQueryUserService extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /** - - - - - - execute - - - - - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return array, nids
   */
  public function runQueryWithGroup($query = NULL) {
    $result = $query->execute();

    return array_values($result);
  }

  /** - - - - - - query not run execute() - - - - - - - - - - - - - - - - -  */

  /**
   * @return array, nids
   */
  public function queryUidsByStatus($status = 1) {
    $query = \Drupal::entityQuery('user');
    $query->condition('status', 1);

    $uids = $this->runQueryWithGroup($query);

    return $uids;
  }

  /** - - - - - - Group Condition - - - - - - - - - - - - - - - - - - - - -  */

  /**
   * @return array,
   */
  public function groupByRoleName($query = NULL, $role_name = NULL) {
    $group = $query->andConditionGroup()
      ->condition('roles', $role_name);

    return $group;
  }

  /**
   * @return array,
   */
  public function groupByRoles($query = NULL, $role_names = array(), $operator = 'IN') {
    $group = $query->andConditionGroup()
      ->condition('roles', $role_names, $operator);

    return $group;
  }

  /** - - - - - - wrapper - - - - - - - - - - - - - - - - - - - - - - - - - -  */
  /**
   * @deprecated by 2017 Nov
   * @see $this->wrapperUidsByRoles();
   */
  public function wrapperUidsByRoleName($role_name = NULL) {
    $uids = $this->wrapperUidsByRoles(array($role_name));
    return $uids;
  }

  /**
   * @return array,
   */
  public function wrapperUidsByRoles($role_names = array(), $operator = 'IN') {
    $query = \Drupal::entityQuery('user');
    $query->condition('status', 1);

    $group = $this->groupByRoles($query, $role_names, $operator);
    $query->condition($group);

    $uids = $this->runQueryWithGroup($query);

    return $uids;
  }

  /**
   * @deprecated by 2017 Dec
   * @see $this->wrapperUidsByRoles();
   */
  public function wrapperUsersByRoleName($role_name = NULL) {
    $users = $this->wrapperUsersByRoleNames(array($role_name));
    return $users;
  }

  /**
   * @param $role_names is array
   */
  public function wrapperUsersByRoleNames($role_names = array()) {
    $uids = $this->wrapperUidsByRoles($role_names);
    $users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple($uids);

    return $users;
  }

}
