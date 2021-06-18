<?php

/**
 *
  require_once(DRUPAL_ROOT . '/modules/custom/flexrepo/phpdebug/debug_user.php');
  _loadUser();
 */
use Drupal\Component\Utility\Timer;

function _loadUser($target_id = NULL) {
  if (!$target_id) {
    $target_id = 1;
  }

  $user = \Drupal::entityTypeManager()->getStorage('user')->load($target_id);

  ksm($user);

  dpm($user->getLastLoginTime());
  dpm(\Drupal::service('date.formatter')->format($user->getLastLoginTime(), 'html_datetime'));
}


function _deleteUser() {
  $users = array();

  $query = \Drupal::entityQuery('user');
  $uids = $query->execute();

  $uids = array();

  $controller = \Drupal::entityTypeManager()->getStorage('user');
  $entities = $controller->loadMultiple($uids);


  foreach ($entities as $entity) {
    if (!in_array('administrator', $entity->getRoles($exclude_locked_roles = True))) {
      $users[] = $entity;
    }
  }

  $controller->delete($users);
}

function _runUpdateUserValue() {
  $query = \Drupal::entityQuery('user');
  $uids = $query->execute();
  $users = user_load_multiple($uids);

  $num = 1;
  foreach ($users as $key => $user) {
    dpm($user->id());
    // Update email Id
    // $user->setEmail($content['email']);

    // Update username
    // $user->setUsername($content['email']);

    // Update password reset
    // $user->setPassword($content['password']);

    // user role
    // $user->addRole('administrator');
    // $user->removeRole('administrator');

    // For User field
    // $user->set("field_first_name", $firstName);
    // $user->set("field_last_name", $lastName);

    // $user->save();
    $num++;
  }
}
