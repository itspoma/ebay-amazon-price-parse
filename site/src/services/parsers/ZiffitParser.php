<?php
namespace app\services\parsers;

use \app\services\dto\ParseResultDto as ParseResultDto;

/**
 *
 */
class ZiffitParser extends AbstractParser {
  const NAME = 'Ziffit';

  public function parsePrice($value) {
    $session = new \Requests_Session('https://www.ziffit.com/');
    $session->headers['Origin'] = 'https://www.ziffit.com';
    $session->useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';

    $session->post('/processEan', [
      'Content-Type' => 'application/x-www-form-urlencoded',
    ], [
      'ean' => $value,
      'basetn' => 'cfzAizbeP2',
    ]);

    $response = $session->get('/basket?', [
      'Referer' => 'https://www.ziffit.com/processEan'
    ]);

    $dto = new ParseResultDto;

    if (preg_match('/data-title="Offer".+?([\d\.]+)</', $response->raw, $m)) {
      $dto->price = trim($m[1]);
    }

    if (preg_match('/id="titleColumn" data-title="Title">(.+?)</', $response->raw, $m)) {
      $dto->title = trim(html_entity_decode($m[1]));
    }

    return $dto;
  }
}
