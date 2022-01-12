<?php

namespace Drupal\mongo\Services;

/**
 *
 $output = \Drupal::service('mongo.driver.set')
   ->runDatabaseStats();
 */

/**
 * Class MongoDriverSet.
 */
// class MongoDriverSet implements MongoDriverSetInterface {
class MongoDriverSet {

  /**
   *
   */
  public $manager;
  public $db_collection;

  /**
   * Constructs a new MongoDriverSet object.
   */
  public function __construct() {
    $this->manager = new \MongoDB\Driver\Manager("mongodb://localhost:27705");
    $this->db_collection = 'lundbeck`.evaluation';
  }

  /**
   *
   */
  public function getBulkWrite() {
    $bulk = new \MongoDB\Driver\BulkWrite;

    return $bulk;
  }

  /**
   * insert
   */
  function bulkInsertFields($doc = []) {
    // $doc = ['name' => 'Toyota', 'price' => 26700];
    // $doc = ['_id' => new MongoDB\BSON\ObjectID, 'name' => 'Toyota', 'price' => 26700];

    $bulk = $this->getBulkWrite();
    $bulk->insert($doc);

    $this->manager->executeBulkWrite($this->db_collection, $bulk);
  }

  /**
   * update
   * The MongoDB\BSON\ObjectID generates a new ObjectId. It is a value used to uniquely identify documents in a collection.
   */
  function bulkUpdateFields($doc = []) {
    // $doc = ['name' => 'Audi'], ['$set' => ['price' => 52000]];

    $bulk = $this->getBulkWrite();
    $bulk->update($doc);

    $this->manager->executeBulkWrite($this->db_collection, $bulk);
  }

  /**
   * delete
   */
  function bulkDeleteFields($doc = []) {
    // $doc = ['name' => 'Hummer'];

    $bulk = $this->getBulkWrite();
    $bulk->delete($doc);

    $this->manager->executeBulkWrite($this->db_collection, $bulk);
  }

  /**
   * MongoDB\Driver\Query
   */
  function runQueryFields() {
    $filter = [];
    $filter = ['name' => 'Volkswagen'];
    $options = [
      'projection' => [
        '_id' => 0,
        'ave_win' => 0,
      ],
      'sort' => ['ave_win' => -1],    // 1 or -1
      'maxTimeMS' => 3000,
      'limit' => 5
    ];
    $options = [];

    $query = new MongoDB\Driver\Query($filter, $options);
    $rows = $this->manager->executeQuery($this->db_collection, $query);

    foreach ($rows as $document) {
      // ksm($document);
      var_dump($document);
    }
  }

  /**
   * MongoDB\Driver\Query
   * Projections
   * Projections can be used to specify which fields should be returned.
   * Here we hide the '_id' field and 'ave_win' field 但是显示其它的.
   [
     '_id' => 0,
     'price' => 0,
   ]
   * 只显示下面两个指定的field
   [
     '_id' => 0,
     'price' => 1,
     'year' => 1,
   ]
   *
   *
   $output = \Drupal::getContainer()
     ->get('mongo.driver.set')
     ->runQueryFieldsWithHideFields();
   */
  function runQueryFieldsWithHideFields() {
    $options = [
      "projection" => [
        '_id' => 0,
        'answer' => 1,
      ],
      'limit' => 5
    ];
    $filter = [];
    $filter = ['meeting_nid' => 10];
    $filter = ['answer.question_tid' => 2153];

    $query = new \MongoDB\Driver\Query($filter, $options);

    $rows = $this->manager->executeQuery($this->db_collection, $query);

    foreach ($rows as $row) {
      ($row);

      // dpm($row->_id->__toString());
      // dpm($row->_id->getTimestamp());

      // dpm($row->price);

      // dpm($row->answer[0]);
      // dpm($row->answer[0]->question_tid);
    }
  }

  /**
   *
   */
  function runCommandCount() {
    $options = [
      'count' => "evaluation",
      'query' => [
        'meeting_nid' => 10
      ]
    ];

    $this->runExecuteCommand($options);
  }

  /**
   * Query Aggregate
   */
  function runCommandAggregate() {
    $options = [
      'aggregate' => "evaluation",
      'pipeline' =>[
        ['$match' => [
            'meeting_nid' => 10
          ]
        ],
        ['$group'=>['_id'=> '$meeting_nid', 'count' => ['$sum' => 1]]],
      ],
      'cursor'=> new \stdClass(),
      // 'cursor'=> (object)[],
      // 'cursor'=> ['batchSize' => 1],
      // 'explain'=> TRUE,
    ];

    $this->runExecuteCommand($options);
  }

  /**
   * Query Aggregate
   */
  function runCommandAggregateProject() {
    $options = [
      'aggregate' => "evaluation",
      'pipeline' =>[
        ['$match' => [
            'meeting_nid' => ['$in' => [4, 10]],
            'answer.question_tid' => 2153
          ]
        ],
        ['$project' => [
            // '_id' => 'meeting_nid',
            'sumvalue' => ['$sum' => '$answer.question_answer'],
            'avg' => ['$avg' => '$answer.question_answer'],
          ]
        ],
      ],
      'cursor'=> new \stdClass(),
    ];

    $this->runExecuteCommand($options);
  }

  /**
   * Query Aggregate
   */
  function runCommandAggregateGroup() {
    $options = [
      'aggregate' => "evaluation",
      'pipeline' =>[
        ['$unwind'=> '$answer'],
        ['$match' => [
            // 'meeting_nid' => ['$in' => [4, 10]],
            'answer.question_tid' => 2153
          ]
        ],
        ['$group' => [
            '_id' => '$answer.question_tid',
            'sumvalue' => ['$sum' => '$answer.question_answer'],
            'avg' => ['$avg' => '$answer.question_answer']
            // 'count' => ['$sum' => 1]
          ]
        ],
      ],
      'cursor'=> new \stdClass(),
    ];

    $this->runExecuteCommand($options);
  }

  /**
   * Query Aggregate
   */
  function runCommandAggregateCountResult() {
    $options = [
      'aggregate' => "evaluation",
      'pipeline' =>[
        ['$match' => [
            'meeting_nid' => ['$in' => [4, 10]],
            'answer.question_tid' => 2153
          ]
        ],
        ['$count'=> 'numsresult`'],
      ],
      'cursor'=> new \stdClass(),
    ];

    $this->runExecuteCommand($options);
  }

  /**
   * Find Aggregate
   * 第一个'$match', 结果只显示包含条件的'evaluation'
   * 第二个'$match', 只保留条件的'Answer' Set
   * 用'$project'保留的父级字段
   */
  function runCommandAggregateFindFilter() {
    $options = [
      'aggregate' => "evaluation",
      'pipeline' =>[
        ['$match' => [
            'answer.question_tid' => 2153
          ]
        ],
        ['$unwind'=> '$answer'],
        ['$project' => [
            '_id' => 0,
            'answer' => 1,
          ],
        ],
        ['$match' => [
            'answer.question_tid' => 2153
          ]
        ],
      ],
      'cursor'=> new \stdClass(),
    ];

    $this->runExecuteCommand($options);
  }

  /**
   * Database statistics
   */
  function runDatabaseStats() {
    $options = ["dbstats" => 1];

    $this->runExecuteCommand($options);
  }

  /**
   * Database statistics
   */
  function runExecuteCommand($options) {
    $command = new \MongoDB\Driver\Command($options);
    $cursor = $this->manager->executeCommand("novartis8", $command);
    $response = $cursor->toArray();
    print_r($response);
  }


}
