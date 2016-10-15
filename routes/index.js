var express = require('express');
var path = require('path');
var passport = require('passport');
var Account = require('../models/account');
var router = express();

var config = require('../config/config');

router.get('/', function (req, res) {
    console.log('root router');
    if(req.user)
      res.redirect(config.appRootFolder+'/stream');
    res.render('index', { user : req.user , config: config});
});

router.get('/register', function(req, res) {
    res.render('register', { config: config });
});

router.post('/register', function(req, res, next) {
    Account.register(new Account({ username : req.body.username }), req.body.password, function(err, account) {
        if (err) {
          return res.render('register', { config: config, error : err.message });
        }

        passport.authenticate('local')(req, res, function () {
            req.session.save(function (err) {
                if (err) {
                    return next(err);
                }
                res.redirect(config.appRootFolder);
            });
        });
    });
});

router.get('/stream', function(req, res) {
    res.render('stream', { config: config, user : req.user });
});

router.get('/login', function(req, res) {
    console.log('root router login');
    res.render('login', { config: config, user : req.user });
});

router.post('/login', passport.authenticate('local'), function(req, res) {
    res.redirect(config.appRootFolder);
});

router.get('/logout', function(req, res) {
    req.logout();
    res.redirect(config.appRootFolder);
});

router.get('/auth/facebook',function(req, res) {
  passport.authenticate('facebook');
});

router.get('/auth/facebook/callback', passport.authenticate('facebook', { failureRedirect: config.appRootFolder }),
  function(req, res) {
    // Successful authentication, redirect home.
    res.redirect(config.appRootFolder);
  });

router.get('/ping', function(req, res){
    res.status(200).send("pong!");
});

module.exports = router;
