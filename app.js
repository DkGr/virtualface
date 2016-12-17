var express = require('express');
var fs = require('fs');
var flash = require('connect-flash');
var path = require('path');
var favicon = require('serve-favicon');
var logger = require('morgan');
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var mongoose = require('mongoose');
var passport = require('passport');
var LocalStrategy = require('passport-local').Strategy;
var FacebookStrategy = require('passport-facebook').Strategy;
var XMPPLib = require("./node-xmpp/packages/node-xmpp-server");

var routes = require('./routes/index');
var users = require('./routes/users');
var apiUsers = require('./routes/api/users');
var apiAllUsers = require('./routes/api/allusers');
var apiPosts = require('./routes/api/posts');
var apiComments = require('./routes/api/comments');
var apiLikes = require('./routes/api/likes');
var apiExtractURL = require('./routes/api/extracturl');

var config = require('./config/config');

var privateKey = fs.readFileSync('./privkey.pem').toString();
var certificate = fs.readFileSync('./fullchain.pem').toString();

var app = express();
// view engine setup
app.set('views', path.join(__dirname, '/views'));
app.set('view engine', 'jade');

// uncomment after placing your favicon in /public
//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(require('express-session')({
    secret: 'keyboard cat',
    resave: false,
    saveUninitialized: false
}));
app.use(passport.initialize());
app.use(passport.session());
app.use(flash());
app.use(express.static(path.join(__dirname, 'public')));
app.use('//', routes);
app.use('//api/users', apiUsers);
app.use('//api/allusers', apiAllUsers);
app.use('//api/posts', apiPosts);
app.use('//api/comments', apiComments);
app.use('//api/likes', apiLikes);
app.use('//api/extracturl', apiExtractURL);


// passport config
var Account = require('./models/account');
passport.use(new LocalStrategy(Account.authenticate()));
//passport.serializeUser(Account.serializeUser());
//passport.deserializeUser(Account.deserializeUser());

passport.serializeUser(function(user, done) {
  done(null, user);
});

passport.deserializeUser(function(user, done) {
  done(null, user);
});

if(config.useFacebook){
  passport.use(new FacebookStrategy({
      clientID: config.facebookAppID,
      clientSecret: config.facebookAPIKey,
      callbackURL: config.appBaseUrl+config.appRootFolder+"/auth/facebook/callback"
    },
    function(accessToken, refreshToken, profile, cb) {
      Account.findOrCreate({ facebookId: profile.id }, function (err, user) {
        return cb(err, user);
      });
    }
  ));
}

// MongoDB Server
mongoose.connect(config.mongodbURL);


// XMPP Server
var connectedUser = {
  'admin': 'admin'
};

var Server = XMPPLib.C2S.BOSHServer;

var startServer = function (done) {
  // Sets up the server.
  server = new Server({
    port: config.xmppPort,
    tls: {
      keyPath: config.SSLPrivateKey,
      certPath: config.SSLCertificateFullChain
    }
  });
  // On connection event. When a client connects.
  server.on('connection', function (client) {
    // That's the way you add mods to a given server.
    console.log("new xmpp client connection");
    // Allows the developer to authenticate users against anything they want.
    client.on('authenticate', function (opts, cb) {
      console.log('server:', opts.username, opts.password, 'AUTHENTICATING')
      if(connectedUser[opts.username]){
        if (opts.password === connectedUser[opts.username]) {
          console.log('server:', opts.username, 'AUTH OK')
          cb(null, opts)
        } else {
          console.log('server:', opts.username, 'AUTH FAIL 1')
          cb(false)
        }
      }
      else {
        console.log('server:', opts.username, 'AUTH FAIL 2')
        cb(false)
      }
    })

    client.on('online', function () {
      console.log('server:', client.jid.local, 'ONLINE')
    })

    // Stanza handling
    client.on('stanza', function (stanza) {
      console.log('server:', client.jid.local, 'stanza', stanza.toString())
      var from = stanza.attrs.from
      stanza.attrs.from = stanza.attrs.to
      stanza.attrs.to = from
      client.send(stanza)
    })

    // On Disconnect event. When a client disconnects
    client.on('disconnect', function () {
      console.log('server: client DISCONNECT')
    })
  })

  server.on('listening', done)
}

// process.on("uncaughtException", function (err) {
//   console.log('err uncaught Exception  : ', err);
// })

startServer(function () {
  console.log('XMPP server listening on port '+config.xmppPort);
});

// error handlers

// development error handler
// will print stacktrace
if (app.get('env') === 'development') {
  app.use(function(err, req, res, next) {
    res.status(err.status || 500);
    res.render('error', {
      message: err.message,
      error: err
    });
  });
}

// production error handler
// no stacktraces leaked to user
app.use(function(err, req, res, next) {
  res.status(err.status || 500);
  res.render('error', {
    message: err.message,
    error: {}
  });
});

module.exports = app;
