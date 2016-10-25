var express = require('express');
var path = require('path');
var passport = require('passport');
var Account = require('../models/account');
var router = express();

var config = require('../config/config');

router.get('/', function (req, res) {
    console.log('root router');
    if(req.user){
      console.log(req.user);
      req.session.user = req.user;
      res.redirect(config.appRootFolder+'/stream');
    }else{
      res.render('index', { user : req.user , config: config});
    }
});

router.get('/register', function(req, res) {
    res.render('register', { config: config });
});

router.post('/register', function(req, res, next) {
    if(req.body.password === req.body.passwordcheck)
    {
      Account.register(new Account({ username: req.body.username, displayname: req.body.displayname, email: req.body.email }), req.body.password, function(err, account) {
          if (err) {
            return res.render('register', { config: config, error : err.message });
          }

          passport.authenticate('local')(req, res, function () {
              req.session.save(function (err) {
                  if (err) {
                      return next(err);
                  }
                  res.redirect(config.appRootFolder+'/stream');
              });
          });
      });
    }
    else {
      return res.render('register', { config: config, error : "Les mots de passe ne correspondent pas." });
    }
});

router.get('/stream', function(req, res) {
  if(req.user){
    res.render('stream', { config: config, user : req.user });
  }
  else{
    res.redirect(config.appRootFolder);
  }
});

router.get('/login', function(req, res) {
    console.log('root router login');
    res.render('login', { config: config, user : req.user });
});

router.post('/login', passport.authenticate('local', { failureFlash: "Nom d'utilisateur ou mot de passe invalide" }), function(req, res) {

    res.redirect(config.appRootFolder);
});

router.get('/logout', function(req, res) {
    req.logout();
    res.redirect(config.appRootFolder);
});

router.get('/auth/facebook',passport.authenticate('facebook'),function(req, res){});

router.get('/auth/facebook/callback', passport.authenticate('facebook', { config: config, failureRedirect: config.appRootFolder }),
  function(req, res) {
    // Successful authentication, redirect home.
    res.redirect(config.appRootFolder+'/stream');
  });

router.get('/ping', function(req, res){
    res.status(200).send("pong!");
});

module.exports = router;
