ProgressTracker
===============

[![Build Status](https://travis-ci.org/itarato/ProgressTracker.png?branch=master)](https://travis-ci.org/itarato/ProgressTracker)

Tiny PHP classes to measure PHP process

ProgressTracker contains 2 helper classes for measuring progress: a single and a
batch tracker.

# ProgressGeneralTracker

Single tracker simply starts a timer and can be queried anytime.

**Usage**:

    $generalProgress = new ProgressGeneralTracker();
    for ($i = 10; $i--;) {
      usleep(rand(100000, 900000));
      echo $generalProgress->ping() . "\n";
    }

# ProgressBatchTracker

Batch tracker is able to estimate the time left to finish the progress. It has
to be updated whenever iteration loops.

**Usage**:

    $batchProcess = new ProgressBatchTracker(10);
    for ($i = 10; $i--;) {
      usleep(500000);
      echo $batchProcess->ping() . "\n";
    }
