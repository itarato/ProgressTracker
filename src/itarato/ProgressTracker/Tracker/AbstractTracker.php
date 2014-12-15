<?php
/**
 * @file
 */

namespace ProgressTracker\Tracker;

use ProgressTracker\Reporter\IReporter;

abstract class AbstractTracker implements IProgressReporter {

  const NUMBER_PRECISION = 2;

  /**
   * @var array
   */
  protected $report;

  /**
   * @var IReporter
   */
  protected $reporter;

  public function __construct(IReporter $reporter) {
    $this->report = array();
    $this->reporter = $reporter;
  }

  public function report() {
    $this->snapshot();
    return $this->reporter->report($this->report);
  }

  public function setReporter(IReporter $reporter) {
    $this->reporter = $reporter;
  }

}
