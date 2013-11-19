<?php

require_once __DIR__ . '/../src/ProgressTracker.php';

/**
 * @param $dummy_array
 */
function dummy_heavy_resource() {
  static $dummy_array;
  // Illusion of hard CPU/IO work.
  usleep(rand(100000, 200000));
  $dummy_array[] = str_repeat('*', 10000);
}

$socket_client = new ElephantIO\Client('http://localhost:8888', 'socket.io', 1, FALSE, TRUE, TRUE);
$string_reporter = new itarato\ProgressTracker\Reporter\SocketIOReporter($socket_client);
$tracker = new itarato\ProgressTracker\Tracker\ProgressGeneralTracker($string_reporter);

for ($i = 100; $i--;) {
  dummy_heavy_resource();
  $tracker->report();
}
