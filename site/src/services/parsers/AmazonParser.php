<?php
namespace app\services\parsers;

use \app\services\dto\ParseResultDto as ParseResultDto;

/**
 *
 */
class AmazonParser extends AbstractParser {
  const NAME = 'Amazon';

  public function parsePrice($value) {
    $dto = new ParseResultDto;
    return $dto;

    $value = urlencode($value);

    $url = sprintf(
      'http://www.amazon.com/s/ref=sr_st_price-asc-rank?keywords=%s&sort=price-asc-rank',
      $value
    );
    $html = file_get_html($url);

    $elem = $html
      ->getElementById("atfResults")
      ->getElementById("result_0")
      // ->find("div[class='a-row'] span[class]")
      // ->find("div[class=a-row] span[class=a-size-base]")
      ;

    $dto = new ParseResultDto;

    // var_dump($elem);die;
    // $price = $elem['0']->plaintext;

    return $dto;
  }
}
