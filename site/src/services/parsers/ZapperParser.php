<?php
namespace app\services\parsers;

use \app\services\dto\ParseResultDto as ParseResultDto;

/**
 *
 */
class ZapperParser extends AbstractParser {
  const KEY = 'Zapper';
  const NAME = 'Zapper';

  // https://zapper.co.uk/get-started.html
  public function parsePrice($value) {
    $session = new \Requests_Session('https://zapper.co.uk/');
    $session->headers['Origin'] = 'https://zapper.co.uk';
    $session->useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';

    $session->post('/process.php', [
      'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
      'X-Requested-With' => 'XMLHttpRequest',
    ], [
      'action' => 'storesubmittedidentifiers',
      'storedidentifier' => $value,
    ]);

    $session->post('/responder.php', [], [
      'target' => 'APIResponder',
      'action' => 'BarcodeSubmitAllowed',
    ]);

    $page1 = $session->post('/embedded-list.html', [], [
      'listid' => '-1',
      'identifier' => $value,
    ], [
      'timeout' => 100,
      'connect_timeout' => 100,
    ]);

    $dto = new ParseResultDto;

    if (preg_match('/TOTAL &pound;([\d\.]+)</', $page1->raw, $m)) {
      $dto->price = $m[1];
    }

    if (preg_match('/<td class="title"><div>(.+?)<.div><.td>/', $page1->raw, $m)) {
      $dto->title = $m[1];
    }

    return $dto;
  }
}
