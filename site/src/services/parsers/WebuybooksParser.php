<?php
namespace app\services\parsers;

use \app\services\dto\ParseResultDto as ParseResultDto;

/**
 *
 */
class WebuybooksParser extends AbstractParser {
  const KEY = 'Webuybooks';
  const NAME = 'WeBuyBooks';

  // https://www.webuybooks.co.uk/selling-basket/
  public function parsePrice($value) {
    $session = new \Requests_Session('https://www.webuybooks.co.uk/');
    $session->headers['Origin'] = 'https://www.webuybooks.co.uk';
    $session->useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';

    $session->get('selling-basket/');

    $page1 = $session->post('/wp-admin/admin-ajax.php', [
      'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
      'X-Requested-With' => 'XMLHttpRequest',
    ], [
      'action' => 'addItemToBasket',
      'query' => $value,
    ]);

    $dto = new ParseResultDto;

    if (preg_match('/"price":"(.+?)"/', $page1->raw, $m)) {
      $dto->price = html_entity_decode($m[1]);
    }

    if (preg_match('/tdtitle..>(.+?)</', $page1->raw, $m)) {
      $dto->title = $m[1];
    }

    return $dto;
  }
}
