<?php
/**
 * @file
 */

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

class ProgressSingleTracker extends ProgressBasicTracker {

  protected  $startTime;

  public function __construct() {
    parent::__construct();

    $this->startTime = time();
  }

  public function report() {
    $time_elapsed = time() - $this->startTime;
    return 'Time elapsed: ' . $time_elapsed . ' sec (' . ($time_elapsed / 60) . ' min) ' . $this->memoryTracker->report();
  }

}

class ProgressBatchTracker extends ProgressSingleTracker {

  protected  $itemsTotalCount;

  protected  $itemsFinishedCount = 0;

  public function __construct($itemCount) {
    parent::__construct();

    $this->itemsTotalCount = $itemCount;
  }

  public function step() {
    $this->itemsFinishedCount++;
    return $this->report();
  }

  public function report() {
    $time_elapsed = time() - $this->startTime;
    $time_left = ($time_elapsed / $this->itemsFinishedCount) * ($this->itemsTotalCount - $this->itemsFinishedCount);

    return 'Processed items: ' . $this->itemsFinishedCount . ' / ' . $this->itemsTotalCount .
      ' Time elapsed: ' . $time_elapsed . ' sec (' . ($time_elapsed / 60) . ' min) ' .
      'Time left: ' . $time_left . ' sec (' . ($time_left / 60) . ' min) ' .
      $this->memoryTracker->report();
  }

}

class ProgressMemoryTracker implements iProgressReporter {

  protected  $memoryInitial;

  protected  $memoryInitialReal;

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
    return 'Memory consumption: ' . $this->getConsumption() . ' (total: ' . memory_get_usage(TRUE) . ')' . ' Available memory: ' . $this->getAvailableMemory();
  }

}
