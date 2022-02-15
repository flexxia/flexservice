<?php

/**
 *
 */

use Drupal\Component\Utility\Timer;

/**
 *
 require_once(DRUPAL_ROOT . '/modules/custom/phpdebug/import_json/import_node_meeting.php');
 _run_create_meeting_from_json();
 */
function _run_create_meeting_from_json() {
  $url = 'http://lillymedical.education/node/45354?_format=json';
  $url = 'http://lillymedical.education/superexport/meeting/entitylist';
  $url = 'http://localhost/mildder8/superexport/meeting/entitylist';
  $url = 'http://localhost/mildder8/node/45354?_format=json';

  $response = \Drupal::httpClient()
    ->get(
      $url,
      array(
        'auth' => ['siteadmin', 'flexia123'],
      )
      // array('allow_redirects' => false)
    );

  $json_string = (string) $response->getBody();
}

function _run_create_meeting_from_json2() {
  $client = new GuzzleHttp\Client();

  $request = $client->request('GET', 'http://www.google.com');

  // $request->getEmitter()->on('before', public function (GuzzleHttp\Event\BeforeEvent $e) {
  //     echo $e->getRequest()->getUrl() . PHP_EOL;
  // });
  // $response = $client->send($request);
}

/**
 *
require_once(DRUPAL_ROOT . '/modules/custom/flexservice/phpdebug/import_json/import_node_meeting.php');
 _run_convert_json_to_array();
 */
function _run_convert_json_to_array() {
  $php_array = \Drupal::service('flexinfo.json.service')
    ->fetchConvertJsonToArrayFromInternalPath('/modules/custom/flexservice/phpdebug/import_json/product.json');
  ksort($php_array[0]);
  dpm($php_array[0]);
}
