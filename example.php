<?php
/**
 * @file
 * Example usage
 */

require_once 'ProgressTracker.php';

echo "GENERAL: \n\n";
demoGeneral();
echo "\n\n";

echo "BATCH: \n\n";
demoBatch();
echo "\n\n";

/**
 * General.
 */
function demoGeneral() {
  $generalProgress = new ProgressGeneralTracker();

  for ($i = 10; $i--;) {
    usleep(rand(100000, 900000));
    echo $generalProgress->ping() . "\n";
  }
}

/**
 * Batch.
 */
function demoBatch() {
  $batchProcess = new ProgressBatchTracker(10);

  for ($i = 10; $i--;) {
    usleep(500000);
    echo $batchProcess->ping() . "\n";
  }
}
