#!/usr/bin/env node

/**
 * Module dependencies.
 */

var app = require('./main');
var config = require('./config/config')
var debug = require('debug')('passport-auth-test:server');
var https = require('https');
var http = require('http');
var fs = require('fs');

/**
 * Get port from environment and store in Express.
 */

var port = process.env.PORT || config.httpListeningPort;
app.set('port', port);

/**
 * Create HTTP server.
 */
var server;
 
if(config.SSLCertificateFullChain != '' && config.SSLPrivateKey != ''){
	var privateKey = fs.readFileSync(config.SSLPrivateKey).toString();
	var certificate = fs.readFileSync(config.SSLCertificateFullChain).toString();
	server = https.createServer({
		key: privateKey,
		cert: certificate
	}, app);
}
else{
	server = http.createServer(app);
}

/**
 * Listen on provided port, on all network interfaces.
 */

server.listen(port);
server.on('error', onError);
server.on('listening', onListening);

/**
 * Normalize a port into a number, string, or false.
 */

function normalizePort(val) {
  var port = parseInt(val, 10);

  if (isNaN(port)) {
    // named pipe
    return val;
  }

  if (port >= 0) {
    // port number
    return port;
  }

  return false;
}

/**
 * Event listener for HTTP server "error" event.
 */

function onError(error) {
  if (error.syscall !== 'listen') {
    throw error;
  }

  var bind = typeof port === 'string'
    ? 'Pipe ' + port
    : 'Port ' + port;

  // handle specific listen errors with friendly messages
  switch (error.code) {
    case 'EACCES':
      console.error(bind + ' requires elevated privileges');
      process.exit(1);
      break;
    case 'EADDRINUSE':
      console.error(bind + ' is already in use');
      process.exit(1);
      break;
    default:
      throw error;
  }
}

/**
 * Event listener for HTTP server "listening" event.
 */

function onListening() {
  var addr = server.address();
  var bind = typeof addr === 'string'
    ? 'pipe ' + addr
    : 'port ' + addr.port;
  console.log('HTTP server listening on ' + bind)
  debug('HTTP server listening on ' + bind);
} 
