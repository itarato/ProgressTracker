<?php
/**
 * @file
 */

namespace ProgressTracker\Tracker;

use ProgressTracker\Reporter\IReporter;

class ProgressGeneralTracker extends ProgressBasicTracker {

  protected $startTime;

  protected $currentTime;

  protected $previousTime;

  public function __construct(IReporter $reporter) {
    parent::__construct($reporter);

    $this->startTime = microtime(TRUE);
    $this->currentTime = microtime(TRUE);
    $this->previousTime = microtime(TRUE);
  }

  public function snapshot() {
    $this->previousTime = $this->currentTime;
    $this->currentTime = microtime(TRUE);

    $time_elapsed = number_format(microtime(TRUE) - $this->startTime, self::NUMBER_PRECISION);
    $time_ping = number_format($this->currentTime - $this->previousTime, self::NUMBER_PRECISION);

    $this->report = $this->memoryTracker->snapshot();
    $this->report['time'] = array(
      'step' => $time_ping,
      'total' => $time_elapsed,
    );
    return $this->report;
  }

}
