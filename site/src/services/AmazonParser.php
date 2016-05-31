<?php
namespace app\services;

/**
 *
 */
class AmazonParser extends AbstractParser {
  const NAME = 'Amazon';

  public function parsePrice($value) {
    return null;
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

    // var_dump($elem);die;
    // $price = $elem['0']->plaintext;

    return rand(1,9);
  }
}
