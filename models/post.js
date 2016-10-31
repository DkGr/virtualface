var mongoose = require('mongoose');
var supergoose = require('supergoose');
var Schema = mongoose.Schema;
var passportLocalMongoose = require('passport-local-mongoose');

var Post = new Schema({
    author: String,
    date: Date,
    visibility: Number,
    content: String,
    comments: Array,
    likes: Array
});

module.exports = mongoose.model('Post', Post);
