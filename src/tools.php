<?php
/**
 * @file
 * Various helper functions and factories.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use ElephantIO\Client;
use ProgressTracker\Reporter\CSVStringReporter;
use ProgressTracker\Reporter\SocketIOReporter;
use ProgressTracker\Tracker\ProgressGeneralTracker;

function progress_tracker_instant_socket_report($name = 'instant') {
  static $progress_tracker;

  if (!isset($progress_tracker)) {
    $socketio_client = new Client('http://localhost:8888', 'socket.io', 1, FALSE, TRUE, TRUE);
    $socket_reporter = new SocketIOReporter($socketio_client, array(), $name);
    $progress_tracker = new ProgressGeneralTracker($socket_reporter);
  }

  $progress_tracker->report();
}

function progress_tracker_instant_csv_report(callable $print_command) {
  static $progress_tracker;

  if (!isset($progress_tracker)) {
    $csv_reporter = new CSVStringReporter();
    $progress_tracker = new ProgressGeneralTracker($csv_reporter);
  }

  $print_command($progress_tracker->report());
}
