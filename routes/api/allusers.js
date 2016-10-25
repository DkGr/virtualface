var express = require('express');
var path = require('path');
var passport = require('passport');
var Account = require('../../models/account');
var PrivacyGuard = require('../../privacyGuard')
var router = express();

var config = require('../../config/config');

router.post('/', function(req, res) {
  Account.find(null, '-_id username displayname', function (err, users) {
    if (err) return next(err);
    res.json(users);
  });
});

module.exports = router;
