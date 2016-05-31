<?php
namespace app\services;

/**
 *
 */
class MusicMagpieParser extends AbstractParser {
  const NAME = 'MusicMagpie';

  public function parsePrice($value) {
    $value = urlencode($value);

    return null;

    $url = "http://sbmusicmagpie.empathybroker.com/sb-musicmagpiestore/services/search?rows=32&start=0&q=".$value."&lang=en&filter=inStock%3Atrue%20OR%20backorderable%3Atrue&sort=&q=".$value."&sort=price_sort%20asc";
    vaR_dump($url);die;
  }
}
