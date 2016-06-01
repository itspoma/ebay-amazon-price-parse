<?php
namespace app\services\parsers;

use \app\services\dto\ParseResultDto as ParseResultDto;

/**
 *
 */
class EbayLowestSoldParser extends AbstractParser {
  const KEY = 'EbayLowestSold';
  const NAME = 'eBay (lowest & sold)';

  public function parsePrice($value) {
    $session = new \Requests_Session('http://www.ebay.co.uk/');
    $session->headers['Origin'] = 'http://www.ebay.co.uk/';
    $session->useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';

    $page1 = $session->get('/sch/allcategories/all-categories/?_rdc=1');

    $page2 = $session->get('/sch/i.html?_from=R40&_sacat=0&LH_Complete=1&LH_Sold=1&_trksid=p2055845.m570.l1313.TR0.TRC0.H0.X5026555410212.TRS0&_nkw='.$value.'&_sacat=0&_sop=15', [
      'Referer' => 'http://www.ebay.co.uk/sch/allcategories/all-categories/?_rdc=1',
      'Upgrade-Insecure-Requests' => '1',
    ]);

    $dto = new ParseResultDto;

    if (preg_match('/lvprice prc.+?[\r\n]+.+?bold bidsold">[\r\s\t\n]*(.+?)</', $page2->raw, $m)) {
      $dto->price = trim($m[1]);
    }

    if (preg_match('/src=.http...thumbs.ebaystatic.com..+?alt=\'(.+?)\' \/>/', $page2->raw, $m)) {
      $dto->title = trim($m[1]);
    }

    return $dto;
  }
}
