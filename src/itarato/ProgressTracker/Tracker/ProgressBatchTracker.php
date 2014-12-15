<?php
/**
 * @file
 */

namespace ProgressTracker\Tracker;

use ProgressTracker\Reporter\IReporter;

class ProgressBatchTracker extends ProgressGeneralTracker {

  protected $itemsTotalCount;

  protected $itemsFinishedCount = 0;

  public function __construct(IReporter $reporter, $itemCount) {
    parent::__construct($reporter);

    $this->itemsTotalCount = $itemCount;
  }

  public function snapshot() {
    $this->itemsFinishedCount++;

    $this->report = parent::snapshot();

    $time_elapsed = microtime(TRUE) - $this->startTime;
    $time_left = number_format(($time_elapsed / $this->itemsFinishedCount) * ($this->itemsTotalCount - $this->itemsFinishedCount), self::NUMBER_PRECISION);

    $this->report['batch'] = array(
      'done' => $this->itemsFinishedCount,
      'total' => $this->itemsTotalCount,
      'percentage' => ($this->itemsFinishedCount / $this->itemsTotalCount) * 100,
      'left' => number_format($time_left, self::NUMBER_PRECISION),
    );

    return $this->report;
  }

}
