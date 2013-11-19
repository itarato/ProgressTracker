<?php

require_once __DIR__ . '/../src/ProgressTracker.php';

$string_reporter = new itarato\ProgressTracker\Reporter\StringReporter();
$tracker = new itarato\ProgressTracker\Tracker\ProgressGeneralTracker($string_reporter);

for ($i = 10; $i--;) {
  // Illusion of hard CPU/IO work.
  usleep(rand(100000, 900000));
  echo $tracker->report() . "\n";
}

$array_reporter = new \itarato\ProgressTracker\Reporter\ArrayReporter();
$tracker->setReporter($array_reporter);
var_dump($tracker->report());