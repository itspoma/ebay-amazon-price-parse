<?php
namespace app\services;

use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\NotCache;
use Desarrolla2\Cache\Adapter\File;

/**
 *
 */
class FileStorage {
  private $db;
  static private $instance;

  private function __construct() {
    $adapter = new File(DB_DATA_PATH);
    $cache = new Cache($adapter);

    $this->db = $cache;
  }

  //
  static public function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  //
  public function get($key) {
    return $this->db->get($key);
  }

  //
  public function findBy($key, $searchBy, $searchValue) {
    $record = null;

    $queryRecords = (array) $this->get($key);

    foreach ($queryRecords as $queryRecord) {
      if ($queryRecord[$searchBy] == $searchValue) {
        $record = &$queryRecord;
        break;
      }
    }

    return $record;
  }

  //
  public function set($key, $value) {
    $this->db->set($key, $value);
  }

  //
  public function setByKey($key, $searchBy, $searchValue, $value) {
    $queryRecords = (array) $this->get($key);

    foreach ($queryRecords as &$queryRecord) {
      if ($queryRecord[$searchBy] == $searchValue) {
        $queryRecord = $value;
        break;
      }
    }

    $this->db->set($key, $queryRecords);
  }

  //
  public function append($key, $value) {
    $currentValue = $this->get($key);

    if (!isset($currentValue)) {
      $currentValue = [];
    }

    $currentValue[] = $value;
    $this->set($key, $currentValue);
  }

  //
  public function removeByKey($key, $searchBy, $searchValue) {
    $queryRecords = (array) $this->get($key);

    foreach ($queryRecords as &$queryRecord) {
      if ($queryRecord[$searchBy] == $searchValue) {
        $queryRecord = null;
        break;
      }
    }

    $queryRecords = array_values(array_filter($queryRecords));
    $this->db->set($key, $queryRecords);
  }

  //
  public function removeAll($key) {
    $this->db->getAdapter()->del($key);
  }
}
