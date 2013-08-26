ProgressTracker
===============

[![Build Status](https://travis-ci.org/itarato/ProgressTracker.png?branch=master)](https://travis-ci.org/itarato/ProgressTracker)

Example usage
-------------

    $string_reporter = new Reporter\StringReporter();
    $tracker = new Tracker\ProgressGeneralTracker($string_reporter);

    for ($i = 10; $i--;) {
      // Illusion of hard CPU/IO work.
      usleep(rand(100000, 900000));
      echo $tracker->report() . "\n";
    }
    

Tiny PHP classes to measure PHP process, performance and resource consumption.

ProgressTracker has 3 type of performance trackers and 3 types of reporters.


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
