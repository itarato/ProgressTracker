<?php
/**
 * @file
 */

namespace itarato\ProgressTracker\Tracker;

require_once __DIR__ . '/../vendor/autoload.php';

use itarato\ProgressTracker\Reporter\IReporter;

require_once __DIR__ . '/ProgressReporter.php';

define('PROGRESS_REPORTER_NUMBER_PRECISION', 2);

interface IProgressReporter {

  public function snapshot();

  public function report();

}

abstract class AbstractTracker implements IProgressReporter {

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

abstract class ProgressBasicTracker extends AbstractTracker {

  protected $memoryTracker;

  public function __construct(IReporter $reporter) {
    parent::__construct($reporter);

    $this->memoryTracker = new ProgressMemoryTracker($reporter);
  }

}

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

    $time_elapsed = number_format(microtime(TRUE) - $this->startTime, PROGRESS_REPORTER_NUMBER_PRECISION);
    $time_ping = number_format($this->currentTime - $this->previousTime, PROGRESS_REPORTER_NUMBER_PRECISION);

    $this->report = $this->memoryTracker->snapshot();
    $this->report['time'] = array(
      'step' => $time_ping,
      'total' => $time_elapsed,
    );
    return $this->report;
  }

}

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
    $time_left = number_format(($time_elapsed / $this->itemsFinishedCount) * ($this->itemsTotalCount - $this->itemsFinishedCount), PROGRESS_REPORTER_NUMBER_PRECISION);

    $this->report['batch'] = array(
      'done' => $this->itemsFinishedCount,
      'total' => $this->itemsTotalCount,
      'percentage' => ($this->itemsFinishedCount / $this->itemsTotalCount) * 100,
      'left' => number_format($time_left, PROGRESS_REPORTER_NUMBER_PRECISION),
    );

    return $this->report;
  }

}

class ProgressMemoryTracker extends AbstractTracker {

  protected $memoryInitial;

  protected $memoryInitialReal;

  public function __construct(IReporter $reporter) {
    parent::__construct($reporter);

    $this->memoryInitial = memory_get_usage(FALSE);
    $this->memoryInitialReal = memory_get_usage(TRUE);
  }

  public function getConsumption($real = FALSE) {
    return $real ?
      memory_get_usage(TRUE) - $this->memoryInitialReal :
      memory_get_usage(FALSE) - $this->memoryInitial;
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

  public function snapshot() {
    return $this->report = array(
      'mem' => array(
        'all' => $this->getConsumption(),
      ),
    );
  }

}
