<?php
/**
 * @file
 * CallerTracker definitions.
 */

/**
 * Class CallerTracker
 *
 * Registering callers of a function. To see what is calling a function and how many times.
 * Usage:
 *  Inside the function (you would like to track) put an instance:
 *
 * @code
 *  function foobar() {
 *    CallerTracker::getInstance(__FUNCTION__)->saveCallStack();
 *    // Other actions in the function.
 *  }
 * @endcode
 */
class CallerTracker {

  /**
   * Storage of stacks.
   *
   * @var array
   */
  protected $stacks;

  /**
   * Name of the function that the instance is called from.
   *
   * @var string
   */
  protected $resultPrintFunction;

  /**
   * Name of the function to call for printing the result.
   *
   * @var callable
   */
  protected $callerFunctionName;

  /**
   * Simple factory to create an instance.
   * If you use only one, you can ignore the key.
   *
   * @param string $key
   *  Key to identify the instance - so it can be called from the static cache.
   * @param string $result_print_function
   *  Function to use for printing the result.
   * @return mixed
   *  CallerTracker instance.
   */
  public static function getInstance($key = 'default', callable $result_print_function = 'var_dump') {
    static $instances = array();
    if (!isset($instances[$key])) {
      $instances[$key] = new static($result_print_function);
    }
    return $instances[$key];
  }

  /**
   * Constructor.
   *
   * @param string $result_print_function
   *  Function to use for printing the result.
   */
  public function __construct($result_print_function = 'var_dump') {
    $this->stacks = array();
    $this->resultPrintFunction = $result_print_function;
    register_shutdown_function(array(&$this, 'printResult'));
  }

  /**
   * Register a call.
   */
  public function saveCallStack() {
    $backtrace = debug_backtrace();

    // Remove this function call.
    array_shift($backtrace);

    // Saving the caller's name;
    $this->callerFunctionName = $backtrace[0]['function'];

    $last_caller = $backtrace[1]['function'];
    if (!isset($this->stacks[$last_caller])) {
      $this->stacks[$last_caller] = array(
        'count' => 0,
        'stacks' => array(),
      );
    }
    $this->stacks[$last_caller]['count']++;
    $this->stacks[$last_caller]['stacks'][] = $backtrace;
  }

  /**
   * Registered shutdown function to present the result.
   */
  public function printResult() {
    $result = array($this->callerFunctionName => array());
    foreach ($this->stacks as $caller_name => $stack_info) {
      $result[$this->callerFunctionName][] = array(
        $caller_name => $stack_info['count'],
      );
    }
    if (is_callable($this->resultPrintFunction)) {
      $this->resultPrintFunction($result);
    }
  }

}
