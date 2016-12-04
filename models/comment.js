var mongoose = require('mongoose');
var mongoosePaginate = require('mongoose-aggregate-paginate');
var Schema = mongoose.Schema;

var Comment = new Schema({
    author: String,
    date: Date,
    content: String,
    likes: Array
});

Comment.plugin(mongoosePaginate);

module.exports = mongoose.model('Comment', Comment);
