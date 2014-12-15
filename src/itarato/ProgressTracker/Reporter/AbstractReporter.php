<?php
/**
 * @file
 */

namespace ProgressTracker\Reporter;

abstract class AbstractReporter implements IReporter {

  public abstract function report(array $report);

}
