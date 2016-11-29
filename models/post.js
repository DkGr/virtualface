var mongoose = require('mongoose');
var mongoosePaginate = require('mongoose-aggregate-paginate');
var Schema = mongoose.Schema;

var Post = new Schema({
    author: String,
    date: Date,
    visibility: Number,
    content: String,
    comments: Array,
    likes: Array
});

Post.plugin(mongoosePaginate);

module.exports = mongoose.model('Post', Post);
