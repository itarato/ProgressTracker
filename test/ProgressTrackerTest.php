<?php
/**
 * @file
 * Example usage
 */

use ProgressTracker\Reporter\ArrayReporter;
use ProgressTracker\Reporter\CSVStringReporter;
use ProgressTracker\Reporter\SocketIOReporter;
use ProgressTracker\Reporter\StringReporter;
use ProgressTracker\Tracker\ProgressBatchTracker;
use ProgressTracker\Tracker\ProgressGeneralTracker;
use ProgressTracker\Tracker\ProgressMemoryTracker;

require_once __DIR__ . '/../vendor/autoload.php';

class ProgressTrackerTest extends PHPUnit_Framework_TestCase {

  public $defaultStringReporter;

  public $clientMock;

  public function setUp() {
    parent::setUp();
    $this->defaultStringReporter = new StringReporter();

    $engineMock = $this->getMock('\ElephantIO\Engine\SocketIO\Version1X', [], ['']);
    $this->clientMock = $this->getMock('\ElephantIO\Client', [], [$engineMock, new \Psr\Log\NullLogger()]);
  }

  public function testMemoryTracker() {
    $mt = new ProgressMemoryTracker($this->defaultStringReporter);
    $this->assertNotEmpty($mt->report());
  }

  public function testGeneralTracker() {
    $generalProgress = new ProgressGeneralTracker($this->defaultStringReporter);

    for ($i = 10; $i--;) {
      $this->assertNotEmpty($generalProgress->report());
    }
  }

  public function testBatchTracker() {
    $batchProcess = new ProgressBatchTracker($this->defaultStringReporter, 10);

    for ($i = 10; $i--;) {
      $this->assertNotEmpty($batchProcess->report());
    }
  }

  public function testArrayReporter() {
    $r = new ArrayReporter();
    $p = new ProgressGeneralTracker($r);
    $this->assertTrue(is_array($p->report()));
  }

  public function testStringReporter() {
    $r = new StringReporter();
    $p = new ProgressGeneralTracker($r);
    $this->assertTrue(is_string($p->report()));
  }

  public function testCSVStringReporter() {
    $r = new CSVStringReporter();
    $p = new ProgressGeneralTracker($r);
    $this->assertTrue(is_string($p->report()));
    $this->assertEquals(preg_match('/^[\d.,]*$/', $p->report()), 1);
  }

  public function testSocketIOReporter() {
    $r = new SocketIOReporter($this->clientMock);
    $generalProgress = new ProgressGeneralTracker($r);

    for ($i = 100; $i--;) {
      $generalProgress->report();
    }
  }

  public function testSocketIOReporterForMemory() {
    $r = new SocketIOReporter($this->clientMock, array('mem', 'all'));
    $generalProgress = new ProgressGeneralTracker($r);

    $dummyArray = array();

    for ($i = 100; $i--;) {
      $dummyArray[] = str_repeat('*', 10000);
      $generalProgress->report();
    }
  }

}
