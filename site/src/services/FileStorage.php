<?php
namespace app\services;

/**
 *
 */
class FileStorage {
  static private $db;

  //
  public function init() {
    // touch db file
    if (!file_exists(DB_DATA_FILE)) {
      touch(DB_DATA_FILE);
    }

    if (!self::$db) {
      $db = file_get_contents(DB_DATA_FILE);
      $db = json_decode($db, true);

      self::$db = $db;
    }
  }

  //
  static public function save() {
    $db = json_encode(self::$db);
    file_put_contents(DB_DATA_FILE, $db);
  }

  //
  static public function set($key, $value) {
    self::init();

    self::$db[$key] = $value;
    self::save();
  }

  //
  static public function append($key, $value) {
    self::init();

    if (!isset(self::$db[$key])) {
      self::$db[$key] = [];
    }

    self::$db[$key][] = $value;
    self::save();
  }

  //
  static public function getAll() {
    self::init();
    return self::$db;
  }

  //
  static public function get($key) {
    self::init();
    return isset(self::$db[$key]) ? self::$db[$key] : null;
  }
}
