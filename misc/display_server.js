/**
 * @file
 * Server side JS.
 */

var http = require('http');
var server = http.createServer();

// Initialize and start the socket.
var io = require('socket.io').listen(server);

// For static resource handler.
var node_static = require('node-static');
var staticDir = new node_static.Server('.');

// All the connections.
var progressListenerSockets = [];

// On connection event.
io.sockets.on('connection', function (socket) {
  // Connecting to the socket.
  socket.on('connect', function (data) {
    progressListenerSockets.push(socket);
  });

  // On incoming data.
  socket.on('progress', function (data) {
    for (var idx = 0; idx < progressListenerSockets.length; idx++) {
      progressListenerSockets[idx].emit('update', data);
    }
  });
});

// Simple http server.
server.on('request', function (req, res) {
  req.addListener('end', function () {
    staticDir.serve(req, res);
  }).resume();
}).listen(8888);
