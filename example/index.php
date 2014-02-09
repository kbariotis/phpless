<?php

require_once __DIR__.'/../vendor/autoload.php';

$options = array(
  "lessDirectory" => __DIR__ . '/assets/less/',
  "cacheDirectory" => __DIR__ . '/cache/less/',
  "cacheExpiration" => ""
);

header('Content-Type: text/css');

$compiler = new \phpless\PHPLess($options);