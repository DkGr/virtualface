var express = require('express');
var path = require('path');
var passport = require('passport');
var Account = require('../../models/account');
var Post = require('../../models/post');
var Comment = require('../../models/comment');
var PrivacyGuard = require('../../privacyGuard');
var router = express();

var config = require('../../config/config');

router.get('/:postid', function(req, res) {
  Post.findById(req.params.postid, function(err, post){
    var aggregate = Comment.aggregate([
          {
            $lookup:
              {
                from: "accounts",
                localField: "author",
                foreignField: "username",
                as: "authorInfos"
              }
          },
          { "$sort": { "date": 1 } }
      ]).allowDiskUse(true);
    aggregate.match({ '_id': { $in: post.comments } });
    Comment.aggregatePaginate(aggregate, {}, function(err, comments, pageCount, count) {  
      console.log(comments);
      res.json(comments);
    });
  });
});

router.post('/:postid', function(req, res, next) {
  Post.findById(req.params.postid, function(err, post){
    console.log(post);
    /*Comment.create(req.body, function (err, post) {
      if (err) return next(err);
      res.json(post);
    });*/
  });
});

router.put('/:id', function(req, res, next) {
  Comment.findByIdAndUpdate(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

router.delete('/:id', function(req, res, next) {
  Comment.findByIdAndRemove(req.params.id, req.body, function (err, post) {
    if (err) return next(err);
    res.json(post);
  });
});

module.exports = router;
