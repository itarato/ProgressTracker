<?php
/**
 * @file
 * Reporters.
 */

namespace itarato\ProgressTracker\Reporter;

require_once __DIR__ . '/../vendor/autoload.php';

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

  public function __construct($connectionURI = 'http://localhost:8888') {
    $this->connectionURI = $connectionURI;
    $this->filter = array('time', 'step');
    $this->hash = spl_object_hash($this);
  }

  public function report($report) {
//    require_once __DIR__ . '/elephant.io/lib/ElephantIO/Client.php';

    $item = $report;
    foreach ($this->filter as $filter) {
      $item = $item[$filter];
    }

    $elephant = new Client($this->connectionURI, 'socket.io', 1, FALSE, TRUE, TRUE);
    $elephant->init();

    $elephant->send(Client::TYPE_EVENT, NULL, NULL, json_encode(array('name' => 'progress', 'args' => array(
      'line' => $this->hash,
      'value' => $item,
    ))));

    $elephant->close();
  }

}
