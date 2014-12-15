<?php
/**
 * @file
 */

namespace ProgressTracker\Tracker;

use ProgressTracker\Reporter\IReporter;

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
