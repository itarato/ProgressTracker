/**
 * @file
 * Client side JS.
 */

var socket = io.connect('http://localhost');
socket.emit('connect', {});

// Separate lines tracked by the chart.
var lines = {};

// Handling incoming messages.
socket.on('update', function (data) {
  // New input source.
  if (!lines.hasOwnProperty(data.line)) {
    lines[data.line] = new TimeSeries();
    var color = getRandomColor();
    // Add new line to the chart.
    smoothie.addTimeSeries(lines[data.line], {
      strokeStyle: color,
      lineWidth: 3
    });
    // Present the new item in the list.
    jQuery('#source_list').append('<li style="color:' + color + '">' + data.name + '</li>');
  }

  // Feed the chart with the new data.
  lines[data.line].append(new Date().getTime(), data.value);
});

// Initialize chart plugin.
var smoothie = new SmoothieChart();
smoothie.streamTo(document.getElementById('display'), 500);

/**
 * Returns a random css color.
 *
 * @returns {string}
 */
function getRandomColor() {
  var components = [];
  for (var i = 3; i--;) {
    components.push(Math.floor(Math.random() * 128 + 128));
  }
  return 'rgb(' + components.join(',') + ')';
}
