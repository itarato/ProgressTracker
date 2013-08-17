<?php
/**
 * @file
 */

define('PROGRESS_REPORTER_NUMBER_PRECISION', 2);

interface iProgressReporter {

  public function report();

}

abstract class ProgressBasicTracker implements iProgressReporter {

  protected $memoryTracker;

  public function __construct() {
    $this->memoryTracker = new ProgressMemoryTracker();
  }

  abstract function report();

}

class ProgressGeneralTracker extends ProgressBasicTracker {

  protected $startTime;

  protected $currentTime;

  protected $previousTime;

  public function __construct() {
    parent::__construct();

    $this->startTime = microtime(TRUE);
    $this->currentTime = microtime(TRUE);
    $this->previousTime = microtime(TRUE);
  }

  public function ping() {
    $this->previousTime = $this->currentTime;
    $this->currentTime = microtime(TRUE);
    return $this->report();
  }

  public function report() {
    $time_elapsed = number_format(microtime(TRUE) - $this->startTime, PROGRESS_REPORTER_NUMBER_PRECISION);
    $time_ping = number_format($this->currentTime - $this->previousTime, PROGRESS_REPORTER_NUMBER_PRECISION);
    return 'S ' . $time_ping . ' ' .
      'A ' . $time_elapsed . ' | ' .
      $this->memoryTracker->report();
  }

}

class ProgressBatchTracker extends ProgressGeneralTracker {

  protected $itemsTotalCount;

  protected $itemsFinishedCount = 0;

  public function __construct($itemCount) {
    parent::__construct();

    $this->itemsTotalCount = $itemCount;
  }

  public function ping() {
    $this->itemsFinishedCount++;
    return parent::ping();
  }

  public function report() {
    $time_elapsed = microtime(TRUE) - $this->startTime;
    $time_left = number_format(($time_elapsed / $this->itemsFinishedCount) * ($this->itemsTotalCount - $this->itemsFinishedCount), PROGRESS_REPORTER_NUMBER_PRECISION);

    return 'P ' . $this->itemsFinishedCount . ' / ' . $this->itemsTotalCount .
      ' L: ' . number_format($time_left, PROGRESS_REPORTER_NUMBER_PRECISION) . ' | ' .
      parent::report();
  }

}

class ProgressMemoryTracker implements iProgressReporter {

  protected $memoryInitial;

  protected $memoryInitialReal;

  public function __construct() {
    $this->memoryInitial = memory_get_usage(FALSE);
    $this->memoryInitialReal = memory_get_usage(TRUE);
  }

  public function getConsumption($real = FALSE) {
    return $real ?
      memory_get_usage(TRUE) - $this->memoryInitialReal :
      memory_get_usage(FALSE) - $this->memoryInitial;
  }

  public function getAvailableMemory() {
    $totalAvailable = ProgressMemoryTracker::returnBytes(ini_get('memory_limit'));
    return $totalAvailable - memory_get_usage(TRUE);
  }

  protected static function returnBytes($iniValue) {
    $iniValue = trim($iniValue);
    $last = strtolower($iniValue[strlen($iniValue) - 1]);

    switch($last) {
      case 'g':
        $iniValue *= 1024;
      case 'm':
        $iniValue *= 1024;
      case 'k':
        $iniValue *= 1024;
    }

    return $iniValue;
  }

  public function report() {
    return 'MEM ' . $this->getConsumption() . ' init ' . memory_get_usage(TRUE) . ' total ' . $this->getAvailableMemory() . ' left';
  }

}
