<?php
/**
 * @file
 * Example usage
 */

require_once __DIR__ . '/../src/ProgressTracker.php';

use itarato\ProgressTracker\Tracker as Tracker;
use itarato\ProgressTracker\Reporter as Reporter;

class ProgressTrackerTest extends PHPUnit_Framework_TestCase {

  public $defaultStringReporter;

  public function setUp() {
    parent::setUp();
    $this->defaultStringReporter = new Reporter\StringReporter();
  }

  public function testMemoryTracker() {
    $mt = new Tracker\ProgressMemoryTracker($this->defaultStringReporter);
    $this->assertNotEmpty($mt->report());
  }

  public function testGeneralTracker() {
    $generalProgress = new Tracker\ProgressGeneralTracker($this->defaultStringReporter);

    for ($i = 10; $i--;) {
      $this->assertNotEmpty($generalProgress->report());
    }
  }

  public function testBatchTracker() {
    $batchProcess = new Tracker\ProgressBatchTracker($this->defaultStringReporter, 10);

    for ($i = 10; $i--;) {
      $this->assertNotEmpty($batchProcess->report());
    }
  }

  public function testArrayReporter() {
    $r = new Reporter\ArrayReporter();
    $p = new Tracker\ProgressGeneralTracker($r);
    $this->assertTrue(is_array($p->report()));
  }

  public function testStringReporter() {
    $r = new Reporter\StringReporter();
    $p = new Tracker\ProgressGeneralTracker($r);
    $this->assertTrue(is_string($p->report()));
  }

  public function testSocketIOReporter() {
    $this->markTestSkipped();

    $r = new Reporter\SocketIOReporter();
    $generalProgress = new Tracker\ProgressGeneralTracker($r);

    for ($i = 100; $i--;) {
      usleep(rand(100000, 200000));
      $generalProgress->report();
    }
  }

  public function testSocketIOReporterForMemory() {
    $this->markTestSkipped();

    $r = new Reporter\SocketIOReporter('http://localhost:8888', array('mem', 'all'));
    $generalProgress = new Tracker\ProgressGeneralTracker($r);

    $dummyArray = array();

    for ($i = 100; $i--;) {
      usleep(rand(100000, 200000));

      $dummyArray[] = str_repeat('*', 10000);

      $generalProgress->report();
    }
  }

}
