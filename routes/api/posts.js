var express = require('express');
var path = require('path');
var passport = require('passport');
var Account = require('../../models/account');
var Post = require('../../models/post');
var PrivacyGuard = require('../../privacyGuard')
var router = express();

var config = require('../../config/config');

router.get('/', function(req, res) {
  Post.aggregate([
        {
          $lookup:
            {
              from: "accounts",
              localField: "author",
              foreignField: "username",
              as: "authorInfos"
            }
       },
       { "$sort": { "date": -1 } },
       { "$limit": 5 }
    ]).allowDiskUse(true).exec(function(err, posts){
      res.json(posts);
    });
});

router.post('/', function(req, res, next) {
  Post.create(req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

router.put('/:id', function(req, res, next) {
  Post.findByIdAndUpdate(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

router.delete('/:id', function(req, res, next) {
  Post.findByIdAndRemove(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

module.exports = router;
