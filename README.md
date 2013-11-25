ProgressTracker
===============

[![Build Status](https://travis-ci.org/itarato/ProgressTracker.png?branch=master)](https://travis-ci.org/itarato/ProgressTracker)

# What is ProgressTracker ?

ProgressTracker is a tiny developer tool to analyze your PHP code performance - time and memory. It has 2 main parts: the trackers and reporters. A tracker is for measuring memory consumption and process time.  It can be an overall measurement, or iterative (per-step). Reporter is the output part - which can be a simple string, one value, csv line or a message to a SocketIO server.

Example usage
-------------

    <?php
    use itarato\ProgressTracker\Reporter;
    use itarato\ProgressTracker\Tracker;

    $string_reporter = new Reporter\StringReporter();
    $tracker = new Tracker\ProgressGeneralTracker($string_reporter);

    for ($i = 10; $i--;) {
      // ... something process heavy operation ...
      echo $tracker->report() . "\n";
    }
    ?>

Example string output
---------------------

    [mem] all 1176 init 524288 total 1073217536 | [time] step 0.35 total 0.35
    [mem] all 2952 init 524288 total 1073217536 | [time] step 0.28 total 0.63
    [mem] all 2928 init 524288 total 1073217536 | [time] step 0.49 total 1.12
    [mem] all 2928 init 524288 total 1073217536 | [time] step 0.87 total 1.99
    [mem] all 2928 init 524288 total 1073217536 | [time] step 0.64 total 2.63
    [mem] all 2928 init 524288 total 1073217536 | [time] step 0.89 total 3.53
    [mem] all 2928 init 524288 total 1073217536 | [time] step 0.70 total 4.23
    [mem] all 2928 init 524288 total 1073217536 | [time] step 0.68 total 4.92
    [mem] all 2928 init 524288 total 1073217536 | [time] step 0.22 total 5.13
    [mem] all 2928 init 524288 total 1073217536 | [time] step 0.16 total 5.29

Progress trackers
-----------------

**Memory tacker**

Tracks memory consumption and available memory.

**General progress tracker**

Measure memory and current time for a process. It can be used also in iterations or multiple points in code where it tracks the time spent in each sections.

**Batch process tracker**

Same as the previous one, except it know about the current state of the process and tries to predict the leftover time.


Reporters
---------

**Array reporter**

Returns a structured array of data.

**String reporter**

Return format is a single line string.

**SocketIO reporter**

Returns only the current step time to a SocketIO server. An example implementation can be found at *display_server* and *display_client.html*.
The server listens for clients to join. Then the reporter send the data packets to the server, which distributes it to the clients.

**CSV reporter**

Return format is a comma separated string, which is easy to paste to any kind of spreadsheet application for further analysis.


Usage
-----

    // Load the library:
    require_once 'PATH_TO_LIB/src/ProgressTracker.php';

    // To avoid using long namespaces:
    use itarato\ProgressTracker\Reporter;
    use itarato\ProgressTracker\Tracker;


    // First - create a reporter:
    // string reporter
    $reporter = new Reporter\StringReporter();

    // or array reporter:
    $reporter = new Reporter\ArrayReporter();

    // or csv reporter:
    $reporter = new Reporter\CSVStringReporter();

    // or socketio reporter:
    $socketio_client = new ElephantIO\Client('http://localhost:8888', 'socket.io', 1, FALSE, TRUE, TRUE);
    $reporter = new Reporter\SocketIOReporter($socketio_client, array(), $name);

    // Second param for socketio reporter is the path to the item you want to report.
    // Default is [time, step]. To report memory, use [mem, all]:
    $reporter = new Reporter\SocketIOReporter($socketio_client, array('mem', 'all'), $name);


    // Second - create the tracker:
    // memory tracker:
    $tracker = new Tracker\ProgressMemoryTracker($reporter);

    // or simple tracker:
    $tracker = new Tracker\ProgressGeneralTracker($reporter);

    // or batch process tracker (10 is the total number of process):
    $tracker = new Tracker\ProgressBatchTracker($reporter, 10);


    // Third - track:

    // Simply generate a snapshot of the current state:
    $tracker->snapshot();

    // Create the snapshot and generate a report:
    $tracker->report();


    // If you need to change the reporter on the fly:
    $tracker->setReporter($other_reporter);


Handy tools
-----------

There are 2 wrappers for the most common use cases:

Reporting single steps in an iterative process - just insert this code:

    // csv generator

    require_once 'PATH_TO_LIB/src/tools.php';
    progress_tracker_instant_csv_report('echo');

    // socketio version

    require_once 'PATH_TO_LIB/src/tools.php';
    progress_tracker_instant_socket_report('my process');
