<?php
define('ROOT_DIR', realpath(__DIR__.'/..'));
define('SRC_DIR', ROOT_DIR.'/src');
define('WEB_DIR', ROOT_DIR.'/web');

require_once ROOT_DIR.'/vendor/autoload.php';

// local db in file
// define('DB_DATA_PATH', ROOT_DIR."/data");
define('DB_DATA_PATH', "/tmp/data");
define('DB_DATA_FILE', DB_DATA_PATH."/db.txt");

require_once 'controllers.php';
