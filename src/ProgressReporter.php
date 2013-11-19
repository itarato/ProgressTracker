<?php
/**
 * @file
 * Reporters.
 */

namespace itarato\ProgressTracker\Reporter;

use ElephantIO\Client;

interface IReporter {

  public function report($report);

}

abstract class AbstractReporter implements IReporter {

  public abstract function report($report);

}

class ArrayReporter implements IReporter {

  public function report($report) {
    return $report;
  }

}

class StringReporter implements IReporter {

  public function report($report) {
    $out_array = array();
    foreach ($report as $section_id => $section) {
      $section_array = array();
      foreach ($section as $data_name => $data) {
        $section_array[] = "$data_name $data";
      }
      $out_array[] = '[' . $section_id . '] ' . implode(' ', $section_array);
    }
    return implode(' | ', $out_array);
  }

}

class SocketIOReporter implements IReporter {

  public $connectionURI;

  protected $filter;

  protected $hash;

  protected $name;

  /**
   * Constructor.
   *
   * @param string $connectionURI
   *  URI for the node server.
   * @param array $filter
   *  Selecting the value to send to the monitor. Examples:
   *    - [mem, all],
   *    - [mem, init],
   *    - [mem, total],
   *    - [time, step],
   *    - [time, total].
   * @param $name
   *  Name to identify. Optional.
   */
  public function __construct($connectionURI = 'http://localhost:8888', array $filter = array(), $name = '') {
    $this->connectionURI = $connectionURI;
    $this->filter = $filter ?: array('time', 'step');
    $this->hash = spl_object_hash($this);
    $this->name = $name ?: implode('-', $this->filter);
  }

  public function report($report) {
    require_once __DIR__ . '/../vendor/autoload.php';

    $item = $report;
    foreach ($this->filter as $filter) {
      $item = $item[$filter];
    }

    $elephant = new Client($this->connectionURI, 'socket.io', 1, FALSE, TRUE, TRUE);
    $elephant->init();

    $elephant->send(Client::TYPE_EVENT, NULL, NULL, json_encode(array('name' => 'progress', 'args' => array(
      'line' => $this->hash,
      'value' => $item,
      'name' => $this->name,
    ))));

    $elephant->close();
  }

}
