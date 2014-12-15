<?php
/**
 * @file
 */

namespace ProgressTracker\Tracker;

interface IProgressReporter {

  public function snapshot();

  public function report();

}
