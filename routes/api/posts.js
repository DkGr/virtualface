var express = require('express');
var path = require('path');
var passport = require('passport');
var Account = require('../../models/account');
var Post = require('../../models/post');
var PrivacyGuard = require('../../privacyGuard');
var router = express();

var config = require('../../config/config');

router.get('/:page', function(req, res) {
  var aggregate = Post.aggregate([
        {
          $lookup:
            {
              from: "accounts",
              localField: "author",
              foreignField: "username",
              as: "authorInfos"
            }
        },
        { "$sort": { "date": -1 } }
    ]).allowDiskUse(true);
  var pageNum = 1;
  if(req.params.page){
    pageNum = req.params.page;
  }
  Post.aggregatePaginate(aggregate, { page : pageNum, limit: 5 }, function(err, posts, pageCount, count) {
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
