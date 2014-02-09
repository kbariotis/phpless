<?php

require_once __DIR__.'/../vendor/autoload.php';

$options = array(
  "lessDirectory" => __DIR__ . '/assets/less/',
  "cacheDirectory" => __DIR__ . '/cache/less/',
  "cacheExpiration" => 60 * 60 * 24 * 3 // Three days
);

$compiler = new \phpless\PHPLess($options);