var http = require('http');
var server = http.createServer();

var io = require('socket.io').listen(server);

var node_static = require('node-static');
var staticDir = new node_static.Server('.');

var progressListenerSockets = [];

io.sockets.on('connection', function (socket) {
  socket.on('connect', function (data) {
    progressListenerSockets.push(socket);
  });

  socket.on('progress', function (data) {
    for (var idx = 0; idx < progressListenerSockets.length; idx++) {
      progressListenerSockets[idx].emit('update', data);
    }
  });
});

server.on('request', function (req, res) {
  req.addListener('end', function () {
    staticDir.serve(req, res);
  }).resume();
});

server.listen(8888);
