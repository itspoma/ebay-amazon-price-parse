<?php
namespace app\services\parsers;

use \app\services\dto\ParseResultDto as ParseResultDto;

/**
 *
 */
class MusicMagpieParser extends AbstractParser {
  const KEY = 'MusicMagpie';
  const NAME = 'MusicMagpie';

  // http://www.musicmagpie.co.uk/start-selling/basket-media/
  public function parsePrice($value) {
    $session = new \Requests_Session('http://www.musicmagpie.co.uk/');
    $session->useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';

    $page1 = $session->get('/start-selling/basket-media/');

    $page2 = $session->post('/start-selling/basket-media/', [
      'Content-Type' => 'application/x-www-form-urlencoded'
    ], [
      '__EVENTTARGET' => 'ctl00$ctl00$ctl00$ContentPlaceHolderDefault$mainContent$valueEngine_9$getValSmall',
      '__EVENTARGUMENT' => '',
      '__LASTFOCUS' => '',
      '__VIEWSTATE' => preg_match('/id="__VIEWSTATE" value="(.+?)"/', $page1->raw, $m) ? $m[1] : '',
      'ctl00$ctl00$ctl00$ContentPlaceHolderDefault$signIn_7$hdn_BasketValue' => '1',
      'ctl00$ctl00$ctl00$ContentPlaceHolderDefault$mainContent$valueEngine_9$txtBarcode' => $value,
      'ctl00$ctl00$ctl00$ContentPlaceHolderDefault$mainContent$valueEngine_9$wtmBarcode_ClientState' => 'undefined',
      '__SCROLLPOSITIONX' => '0',
      '__SCROLLPOSITIONY' => rand(1,255),
    ]);

    $dto = new ParseResultDto;

    if (preg_match('/<div class="col_Price">(.+?)<\/div>/', $page2->raw, $m)) {
      $dto->price = trim($m[1]);
    }

    if (preg_match('/<div class="col_Title">(.+?)<\/div>/', $page2->raw, $m)) {
      $dto->title = trim($m[1]);
    }

    return $dto;
  }
}
