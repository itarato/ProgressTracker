<?php
/**
 * @file
 * Reporters.
 */

namespace itarato\ProgressTracker\Reporter;

use ElephantIO\Client;

interface IReporter {

  public function report(array $report);

}

abstract class AbstractReporter implements IReporter {

  public abstract function report(array $report);

}

class ArrayReporter implements IReporter {

  public function report(array $report) {
    return $report;
  }

}

class StringReporter implements IReporter {

  public function report(array $report) {
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

class CSVStringReporter implements IReporter {

  public function report(array $report) {
    $items = array();
    foreach ($report as $category) {
      foreach ($category as $item) {
        $items[] = $item;
      }
    }
    return implode(',', $items);
  }

}

class SocketIOReporter implements IReporter {

  /**
   * @var \ElephantIO\Client
   */
  protected $socketClient;

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
  public function __construct(Client $socket_client, array $filter = array(), $name = '') {
    $this->socketClient = $socket_client;
    $this->socketClient->init();

    $this->filter = $filter ?: array('time', 'step');
    $this->hash = spl_object_hash($this);
    $this->name = $name ?: implode('-', $this->filter);
  }

  public function __destruct() {
    $this->socketClient->close();
  }

  public function report(array $report) {
    $item = $report;
    foreach ($this->filter as $filter) {
      $item = $item[$filter];
    }

    $this->socketClient->send(Client::TYPE_EVENT, NULL, NULL, json_encode(array('name' => 'progress', 'args' => array(
      'line' => $this->hash,
      'value' => $item,
      'name' => $this->name,
    ))));
  }

}
