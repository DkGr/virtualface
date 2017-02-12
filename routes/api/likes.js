var express = require('express');
var path = require('path');
var passport = require('passport');
var Account = require('../../models/account');
var Post = require('../../models/post');
var Comment = require('../../models/comment');
var Like = require('../../models/like');
var PrivacyGuard = require('../../privacyGuard');
var NotificationCenter = require('../../notificationCenter');
var router = express();
var mongoose = require('mongoose');

var config = require('../../config/config');

router.get('/post/:postid', function(req, res) {
  Post.findById(req.params.postid, function(err, post){
    if (err) return next(err);
    var aggregate = Like.aggregate().allowDiskUse(true);
    aggregate.match({ '_id': { $in: post.likes } });
    Like.aggregatePaginate(aggregate, {sortBy: {'date': -1}}, function(err, likes, pageCount, count) {  
      if (err) return next(err);
      var current = 0;
      (function getAuthorInfos (likes){
        if (current == likes.length) {
          res.json(likes);
          return;
        }
        PrivacyGuard.pleaseShowMeUserInformation(req.user.username, likes[current].author, function(user){
          likes[current].authorInfos = [];
          likes[current].authorInfos[0] = user;
          ++current;
          getAuthorInfos(likes);
        });
      })(likes);
    });
  });
});

router.get('/comment/:commentid', function(req, res) {
  Comment.findById(req.params.commentid, function(err, comment){
    if (err) return next(err);
    var aggregate = Like.aggregate([
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
    aggregate.match({ '_id': { $in: comment.likes } });
    Like.aggregatePaginate(aggregate, {}, function(err, likes, pageCount, count) {  
      res.json(likes);
    });
  });
});

router.post('/post/:postid', function(req, res, next) {
  Post.findById(req.params.postid, function(err, post){
    if (err) return next(err);
    Like.create(req.body, function (err, like) {
      if (err) return next(err);
      res.json(like);
      post.likes.push(like._id);
      post.save();
      NotificationCenter.notify(req.user.username, post.author, "<a>"+req.user.username+"</a> aime votre <a>publication</a>");
    });
  });
});

router.post('/comment/:commentid', function(req, res, next) {
  Comment.findById(req.params.commentid, function(err, comment){
    if (err) return next(err);
    Like.create(req.body, function (err, like) {
      if (err) return next(err);
      res.json(like);
      comment.likes.push(like._id);
      comment.save();
      NotificationCenter.notify(req.user.username, post.author, "<a>"+req.user.username+"</a> aime votre <a>commentaire</a>");
    });
  });
});

router.delete('/post/:postid/:likeid', function(req, res, next) {
  Like.findByIdAndRemove(req.params.likeid, req.body, function (err, post) {
    if (err) return next(err);
    var objID = new mongoose.Types.ObjectId(req.params.likeid);
    Post.findById(req.params.postid, function(err, post){
      if (err) return next(err);
      post.likes.remove(objID);
      post.save();
      res.json(post);   
    });
  });
});

module.exports = router;
