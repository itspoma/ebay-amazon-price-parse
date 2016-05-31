<?php
namespace app\services;

/**
 *
 */
class ZiffitParser extends AbstractParser {
  const NAME = 'Ziffit';

  public function parsePrice($value) {
    $value = urlencode($value);

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

    if (preg_match('/data-title="Offer".+?([\d\.]+)</', $response->raw, $m)) {
      $title = null;
      $price = $m[1];

      if (preg_match('/id="titleColumn" data-title="Title">(.+?)</', $response->raw, $m)) {
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
