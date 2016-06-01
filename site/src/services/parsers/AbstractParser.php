<?php
namespace app\services\parsers;

/**
 *
 */
abstract class AbstractParser {
  abstract public function parsePrice($value);
}
