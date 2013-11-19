<?php

require_once __DIR__ . '/../src/ProgressTracker.php';

/**
 * @param $dummy_array
 */
function dummy_heavy_resource() {
  static $dummy_array;
  // Illusion of hard CPU/IO work.
  usleep(rand(100000, 900000));
  $dummy_array[] = str_repeat('*', 10000);
}

$string_reporter = new itarato\ProgressTracker\Reporter\StringReporter();
$tracker = new itarato\ProgressTracker\Tracker\ProgressGeneralTracker($string_reporter);

for ($i = 10; $i--;) {
  dummy_heavy_resource();
  echo $tracker->report() . "\n";
}

$csv_reporter = new \itarato\ProgressTracker\Reporter\CSVStringReporter();
$tracker->setReporter($csv_reporter);

for ($i = 10; $i--;) {
  dummy_heavy_resource();
  echo $tracker->report() . "\n";
}

$array_reporter = new \itarato\ProgressTracker\Reporter\ArrayReporter();
$tracker->setReporter($array_reporter);
var_dump($tracker->report());
