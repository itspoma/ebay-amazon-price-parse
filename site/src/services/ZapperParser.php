<?php
namespace app\services;

/**
 *
 */
class ZapperParser extends AbstractParser {
  const NAME = 'Zapper';

  public function parsePrice($value) {
    $value = urlencode($value);

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

    $request = $session->post('/embedded-list.html', [], [
      'listid' => '-1',
      'identifier' => $value,
    ], [
      'timeout' => 100,
      'connect_timeout' => 100,
    ]);

    if (preg_match('/TOTAL &pound;([\d\.]+)</', $request->raw, $m)) {
      $title = null;
      $price = $m[1];

      if (preg_match('/<td class="title"><div>(.+?)<.div><.td>/', $request->raw, $m)) {
        $title = $m[1];
      }

      return [
        'title' => $title,
        'price' => $price,
      ];
    }

    return null;
  }
}
