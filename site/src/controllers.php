<?php
use \app\services\AmazonParser as AmazonParser;
use \app\services\EbayParser as EbayParser;
use \app\services\MusicMagpieParser as MusicMagpieParser;
use \app\services\ZapperParser as ZapperParser;
use \app\services\ZiffitParser as ZiffitParser;
use \app\services\FileStorage as FileStorage;

// request to api.php
if (strpos($_SERVER['SCRIPT_NAME'], 'api.php')) {
  $response = [
    'error' => null,
    'data' => null,
  ];

  if (!isset($_GET['action'])) {
    $response['error'] = 'the "action" param is missed';
  }
  // there're no reason to move below logic to separate files
  else {
    $action = $_GET['action'];

    // so we just parse the request here
    if ($action == 'search') {
      if (!isset($_GET['source'])) {
        $response['error'] = 'the "source" param is missed';
      }
      else {
        $source = $_GET['source'];
        $query = $_GET['query'];

        if (strtolower(AmazonParser::NAME) == $source) {
          $parsed = AmazonParser::parsePrice($query);
        }
        elseif (strtolower(EbayParser::NAME) == $source) {
          $parsed = EbayParser::parsePrice($query);
        }
        elseif (strtolower(MusicMagpieParser::NAME) == $source) {
          $parsed = MusicMagpieParser::parsePrice($query);
        }
        elseif (strtolower(ZapperParser::NAME) == $source) {
          $parsed = ZapperParser::parsePrice($query);
        }
        elseif (strtolower(ZiffitParser::NAME) == $source) {
          $parsed = ZiffitParser::parsePrice($query);
        }

        $price = @$parsed['price'];
        $title = @$parsed['title'];

        $record = null;
        $queryRecords = (array) FileStorage::get('records');
        foreach ($queryRecords as &$queryRecord) {
          if ($queryRecord['query'] == $query) {
            $record = &$queryRecord;
            break;
          }
        }

        if ($record) {
          $record['price-'.$source] = $price;
          $record['title-'.$source] = $title;
          $record['updated_date'] = date('Y-m-d H:i:s');

          FileStorage::set('records', $queryRecords);
        }
        else {
          $records = FileStorage::append('records', [
            'query' => $query,
            'added_date' => date('Y-m-d H:i:s'),
            'update_date' => date('Y-m-d H:i:s'),
            'price-'.$source => $price,
            'title-'.$source => $title,
          ]);
        }

        $response['data']['source'] = $source;
        $response['data']['price'] = $price;
        $response['data']['title'] = $title;
      }
    }

    // list of results
    else if ($action == 'results') {
      $response['data']['records'] = FileStorage::get('records');
    }
  }

  echo json_encode($response);
}
// request to index.php
else {
  require 'views/layout.html';
}
