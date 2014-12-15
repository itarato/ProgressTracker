<?php
/**
 * @file
 */

namespace ProgressTracker\Reporter;

class CSVStringReporter implements IReporter {

  public function report(array $report) {
    $items = array();
    foreach ($report as $category) {
      foreach ($category as $item) {
        $items[] = $item;
      }
    }
    return implode(',', $items);
  }

}
