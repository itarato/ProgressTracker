<?php
/**
 * @file
 * Example usage
 */

require_once 'ProgressTracker.php';

use ProgressTracker\Tracker as Tracker;
use ProgressTracker\Reporter as Reporter;

echo "MEMORY: \n\n";
demoMemory();
echo "\n\n";

echo "GENERAL: \n\n";
demoGeneral();
echo "\n\n";

echo "BATCH: \n\n";
demoBatch();
echo "\n\n";

/**
 * Memory
 */
function demoMemory() {
  $r = new Reporter\ArrayReporter();
  $mt = new Tracker\ProgressMemoryTracker($r);
  var_dump($mt->report());
}

/**
 * General.
 */
function demoGeneral() {
  $r = new Reporter\StringReporter();
  $generalProgress = new Tracker\ProgressGeneralTracker($r);

  for ($i = 10; $i--;) {
    usleep(rand(100000, 900000));
    echo $generalProgress->report() . "\n";
  }
}

/**
 * Batch.
 */
function demoBatch() {
  $r = new Reporter\StringReporter();
  $batchProcess = new Tracker\ProgressBatchTracker($r, 10);

  for ($i = 10; $i--;) {
    usleep(500000);
    echo $batchProcess->report() . "\n";
  }
}
