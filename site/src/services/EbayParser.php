<?php
namespace app\services;

/**
 *
 */
class EbayParser extends AbstractParser {
  const NAME = 'Ebay';

  public function parsePrice($value) {
    $value = urlencode($value);

    $url = sprintf(
      'http://www.ebay.com/sch/?_nkw=%s&_sop=15&LH_Complete=1&LH_Sold=1',
      $value
    );
    $page = file_get_contents($url);

    preg_match_all('/bold bidsold">[\r\n\t]+(.+?)</', $page, $m);
    $price = @$m[1][0];

    preg_match_all('/lvtitle"><a.+?>(.+?)</', $page, $m);
    $title = @$m[1][0];

    return [
      'price' => $price,
      'title' => $title,
    ];
  }
}
