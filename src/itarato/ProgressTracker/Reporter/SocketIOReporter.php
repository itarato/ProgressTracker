<?php
/**
 * @file
 */

namespace ProgressTracker\Reporter;

use ElephantIO\Client;

class SocketIOReporter implements IReporter {

  /**
   * @var Client
   */
  protected $socketClient;

  protected $filter;

  protected $hash;

  protected $name;

  /**
   * Constructor.
   *
   * @param Client $socket_client
   * @param array $filter
   *  Selecting the value to send to the monitor. Examples:
   *    - [mem, all],
   *    - [mem, init],
   *    - [mem, total],
   *    - [time, step],
   *    - [time, total].
   * @param string $name
   *  Name to identify. Optional.
   */
  public function __construct(Client $socket_client, array $filter = array(), $name = '') {
    $this->socketClient = $socket_client;
    $this->socketClient->initialize(TRUE);

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

    $this->socketClient->emit(__METHOD__, array('name' => 'progress', 'args' => array(
      'line' => $this->hash,
      'value' => $item,
      'name' => $this->name,
    )));
  }

}
