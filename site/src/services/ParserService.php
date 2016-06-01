<?php
namespace app\services;

use \app\services\parsers\AmazonParser as AmazonParser;
use \app\services\parsers\EbayLowestParser as EbayLowestParser;
use \app\services\parsers\EbayLowestSoldParser as EbayLowestSoldParser;
use \app\services\parsers\MusicMagpieParser as MusicMagpieParser;
use \app\services\parsers\ZapperParser as ZapperParser;
use \app\services\parsers\ZiffitParser as ZiffitParser;

/**
 *
 */
class ParserService {

  //
  public function getParserName($service) {
    $parserName = null;

    if (strtolower(AmazonParser::KEY) == $service) {
      $parserName = AmazonParser::NAME;
    }
    elseif (strtolower(EbayLowestParser::KEY) == $service) {
      $parserName = EbayLowestParser::NAME;
    }
    elseif (strtolower(EbayLowestSoldParser::KEY) == $service) {
      $parserName = EbayLowestSoldParser::NAME;
    }
    elseif (strtolower(MusicMagpieParser::KEY) == $service) {
      $parserName = MusicMagpieParser::NAME;
    }
    elseif (strtolower(ZapperParser::KEY) == $service) {
      $parserName = ZapperParser::NAME;
    }
    elseif (strtolower(ZiffitParser::KEY) == $service) {
      $parserName = ZiffitParser::NAME;
    }

    return $parserName;
  }

  //
  public function parse($service, $query) {
    $dto = null;

    if (strtolower(AmazonParser::KEY) == $service) {
      $dto = AmazonParser::parsePrice($query);
    }
    elseif (strtolower(EbayLowestParser::KEY) == $service) {
      $dto = EbayLowestParser::parsePrice($query);
    }
    elseif (strtolower(EbayLowestSoldParser::KEY) == $service) {
      $dto = EbayLowestSoldParser::parsePrice($query);
    }
    elseif (strtolower(MusicMagpieParser::KEY) == $service) {
      $dto = MusicMagpieParser::parsePrice($query);
    }
    elseif (strtolower(ZapperParser::KEY) == $service) {
      $dto = ZapperParser::parsePrice($query);
    }
    elseif (strtolower(ZiffitParser::KEY) == $service) {
      $dto = ZiffitParser::parsePrice($query);
    }

    return $dto;
  }

}
