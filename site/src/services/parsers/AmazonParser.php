<?php
namespace app\services\parsers;

use \app\services\dto\ParseResultDto as ParseResultDto;

/**
 *
 */
class AmazonParser extends AbstractParser {
  const KEY = 'Amazon';
  const NAME = 'Amazon';

  public function parsePrice($value) {
    $session = new \Requests_Session('https://www.amazon.co.uk/');
    $session->useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';

    $page1 = $session->get('/ref=nav_logo');

    $page2 = $session->get('/s/ref=nb_sb_noss?url=search-alias%3Daps&field-keywords='.$value);
    $offerListingId = preg_match('/offer-listing\/(.+?)\/ref/', $page2->raw, $m) ? $m[1] : null;

    $page3 = $session->get('/gp/offer-listing/'.$offerListingId.'/ref=sr_1_1_twi_aud_2_olp?ie=UTF8&qid='.rand(1111,99999999).'&sr=8-1&keywords='.$value);

    $dto = new ParseResultDto;

    if (preg_match('/olpOfferPrice.+?>(.+?)</', $page3->raw, $m)) {
      $dto->price = trim($m[1]);
    }

    if (preg_match('/olpProductDetails.+?[\r\n]+.*<h1.+?<.div>(.+?)<.h1>/', $page3->raw, $m)) {
      $dto->title = trim($m[1]);
    }

    return $dto;
  }
}
