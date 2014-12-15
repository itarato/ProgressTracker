<?php
/**
 * @file
 */

namespace ProgressTracker\Reporter;

class ArrayReporter implements IReporter {

  public function report(array $report) {
    return $report;
  }

}
