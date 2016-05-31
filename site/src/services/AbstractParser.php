<?php
namespace app\services;

/**
 *
 */
abstract class AbstractParser {
  abstract public function parsePrice($value);
}
