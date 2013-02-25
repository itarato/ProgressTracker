ProgressTracker
===============

Tiny PHP classes to measure PHP process

ProgressTracker contains 2 helper classes for measuring progress: a single and a
batch tracker.

# ProgressSingleTracker

Single tracker simply starts a timer and can be queried anytime.

**Usage**:

$progressTracker = new ProgressSingleTracker();

for ($i = 0; $i < 10; $i++) {
  // Do some heavy calculations.
  sleep(0.1);
}

echo $progressTracker->report();

# ProgressBatchTracker

Batch tracker is able to estimate the time left to finish the progress. It has
to be updated whenever iteration loops.

**Usage**:

$vector = range(0, 30);
$progressTracker = new ProgressBatchTracker(count($vector));

foreach ($vector as $item) {
  sleep(0.1);
  echo $progressTracker->step() . "\n";
}
