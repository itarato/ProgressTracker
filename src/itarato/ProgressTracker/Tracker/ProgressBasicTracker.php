<?php
/**
 * @file
 */

namespace ProgressTracker\Tracker;

use ProgressTracker\Reporter\IReporter;

abstract class ProgressBasicTracker extends AbstractTracker {

  protected $memoryTracker;

  public function __construct(IReporter $reporter) {
    parent::__construct($reporter);

    $this->memoryTracker = new ProgressMemoryTracker($reporter);
  }

}
