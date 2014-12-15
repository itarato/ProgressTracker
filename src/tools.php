<?php
/**
 * @file
 * Various helper functions and factories.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use ElephantIO\Client;

function progress_tracker_instant_socket_report($name = 'instant') {
  static $progress_tracker;

  if (!isset($progress_tracker)) {
    $socketio_client = new Client('http://localhost:8888', 'socket.io', 1, FALSE, TRUE, TRUE);
    $socket_reporter = new Reporter\SocketIOReporter($socketio_client, array(), $name);
    $progress_tracker = new Tracker\ProgressGeneralTracker($socket_reporter);
  }

  $progress_tracker->report();
}

function progress_tracker_instant_csv_report(callable $print_command) {
  static $progress_tracker;

  if (!isset($progress_tracker)) {
    $csv_reporter = new Reporter\CSVStringReporter();
    $progress_tracker = new Tracker\ProgressGeneralTracker($csv_reporter);
  }

  $print_command($progress_tracker->report());
}
