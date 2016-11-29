var express = require('express');
var path = require('path');
var fs = require('fs');
var passport = require('passport');
var crypto = require('crypto');
var Account = require('../models/account');
var router = express();

var config = require('../config/config');

router.get('/', function (req, res) {
    if(req.user){
      req.session.user = req.user;
      res.redirect(config.appRootFolder+'/stream');
    }else{
      res.render('index', { user : req.user , config: config});
    }
});

router.get('/register', function(req, res) {
    res.render('register', { config: config, title: "Virtual iD - Créer un compte" });
});

router.post('/register', function(req, res, next) {
  var avatarFilename = req.body.username+'-'+new Date().toISOString().replace(/-/g,"").replace(/T/g,"").replace(/:/g,"").slice(0,14);
  Account.register(new Account({ username: req.body.username, displayname: req.body.displayname, email: req.body.email, avatar: avatarFilename, publicKey: req.body.public_key, privateKey: req.body.private_key }), req.body.password, function(err, account) {
      if (err) {
        return res.render('register', { config: config, error : err.message });
      }
      fs.createReadStream('./public/images/no_avatar.png').pipe(fs.createWriteStream('./public/avatars/'+avatarFilename));
      passport.authenticate('local')(req, res, function () {
          req.session.save(function (err) {
              if (err) {
                  return next(err);
              }
              res.status(200).send();
          });
      });
  });
});

router.get('/stream', function(req, res) {
  if(req.user){
    res.render('stream', { config: config, user : req.user, title: "Virtual iD - Mon flux" });
  }
  else{
    res.redirect(config.appRootFolder);
  }
});

router.get('/login', function(req, res) {
    res.render('login', { config: config, user : req.user });
});

router.post('/login', passport.authenticate('local', { failureFlash: "Nom d'utilisateur ou mot de passe invalide" }), function(req, res) {
    var dateStr = new Date().toString();
    var hash = crypto.createHash('md5').update(dateStr).digest('hex');
    req.user.xmppToken = hash;
    console.log(hash);
    console.log(req.user);
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
