var express = require('express');
var path = require('path');
var passport = require('passport');
var Account = require('../../models/account');
var PrivacyGuard = require('../../privacyGuard');
var router = express();

var config = require('../../config/config');

router.get('/:username', function(req, res) {
  var requesterUsername = '#anonymous#';
  if(req.user){
    requesterUsername = req.user.username;
  }
  else{
    console.log("anonymous request");
  }
  
  if(req.params.username == "me"){
    req.params.username = requesterUsername;
  }
  PrivacyGuard.pleaseShowMeUserInformation(res, requesterUsername, req.params.username);
});

module.exports = router;
