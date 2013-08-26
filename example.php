<?php

require_once 'ProgressTracker.php';

$string_reporter = new \ProgressTracker\Reporter\StringReporter();
$tracker = new \ProgressTracker\Tracker\ProgressGeneralTracker($string_reporter);

for ($i = 10; $i--;) {
  // Illusion of hard CPU/IO work.
  usleep(rand(100000, 900000));
  echo $tracker->report() . "\n";
}
