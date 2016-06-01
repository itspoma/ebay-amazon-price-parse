<?php
namespace app\services;

use \app\services\parsers\AmazonParser as AmazonParser;
use \app\services\parsers\EbayParser as EbayParser;
use \app\services\parsers\MusicMagpieParser as MusicMagpieParser;
use \app\services\parsers\ZapperParser as ZapperParser;
use \app\services\parsers\ZiffitParser as ZiffitParser;

/**
 *
 */
class ParserService {

  //
  public function parse($service, $query) {
    $parsed = null;

    if (strtolower(AmazonParser::NAME) == $service) {
      $parsed = AmazonParser::parsePrice($query);
    }
    elseif (strtolower(EbayParser::NAME) == $service) {
      $parsed = EbayParser::parsePrice($query);
    }
    elseif (strtolower(MusicMagpieParser::NAME) == $service) {
      $parsed = MusicMagpieParser::parsePrice($query);
    }
    elseif (strtolower(ZapperParser::NAME) == $service) {
      $parsed = ZapperParser::parsePrice($query);
    }
    elseif (strtolower(ZiffitParser::NAME) == $service) {
      $parsed = ZiffitParser::parsePrice($query);
    }

    return $parsed;
  }

}
