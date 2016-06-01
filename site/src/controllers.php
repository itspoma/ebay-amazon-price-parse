<?php
use \app\services\ParserService as ParserService;
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

        // get parser name
        $parsedName = ParserService::getParserName($source);

        // parse action
        $parsedDto = ParserService::parse($source, $query);
        $parsedPrice = $parsedDto->price;
        $parsedTitle = $parsedDto->title;

        // check if record already exists (by query)
        $record = FileStorage::getInstance()->findBy('records', 'query', $query);

        // if exists, then update
        if ($record) {
          $record["price-$source"] = $parsedPrice;
          $record["title-$source"] = $parsedTitle;
          $record['updated_date']  = date('Y-m-d H:i:s');

          FileStorage::getInstance()->setByKey('records', 'query', $query, $record);
        }
        // if not - the create new
        else {
          $records = FileStorage::getInstance()->append('records', [
            'query'         => $query,
            'added_date'    => date('Y-m-d H:i:s'),
            'update_date'   => date('Y-m-d H:i:s'),
            "price-$source" => $parsedPrice,
            "title-$source" => $parsedTitle,
          ]);
        }

        $response['data']['source-name'] = $parsedName;
        $response['data']['source'] = $source;
        $response['data']['price'] = $parsedPrice;
        $response['data']['title'] = $parsedTitle;
      }
    }

    // list of results
    else if ($action == 'results') {
      $response['data']['records'] = FileStorage::getInstance()->get('records');
    }

    // delete one record
    else if ($action == 'delete') {
      $query = $_GET['query'];

      FileStorage::getInstance()->removeByKey('records', 'query', $query);
    }

    // delete all records
    else if ($action == 'delete-all') {
      FileStorage::getInstance()->removeAll('records');
    }
  }

  echo json_encode($response);
}
// request to index.php
else {
  require 'views/layout.html';
}
