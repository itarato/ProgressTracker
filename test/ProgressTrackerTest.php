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

  public function testCSVStringReporter() {
    $r = new Reporter\CSVStringReporter();
    $p = new Tracker\ProgressGeneralTracker($r);
    $this->assertTrue(is_string($p->report()));
    $this->assertEquals(preg_match('/^[\d.,]*$/', $p->report()), 1);
  }

  public function testSocketIOReporter() {
    $elephant = $elephant = new ElephantClientStub();
    $r = new Reporter\SocketIOReporter($elephant);
    $generalProgress = new Tracker\ProgressGeneralTracker($r);

    for ($i = 100; $i--;) {
      $generalProgress->report();
    }
  }

  public function testSocketIOReporterForMemory() {
    $elephant = new ElephantClientStub();
    $r = new Reporter\SocketIOReporter($elephant, array('mem', 'all'));
    $generalProgress = new Tracker\ProgressGeneralTracker($r);

    $dummyArray = array();

    for ($i = 100; $i--;) {
      $dummyArray[] = str_repeat('*', 10000);
      $generalProgress->report();
    }
  }

}

class ElephantClientStub extends \ElephantIO\Client {

  public function __construct() {}

  public function init($keepalive = false) {}

  public function send($type, $id = null, $endpoint = null, $message = null) {}

  public function close() {}

}
