var mongoose = require('mongoose');
var mongoosePaginate = require('mongoose-aggregate-paginate');
var Schema = mongoose.Schema;

var Like = new Schema({
    author: String,
    date: Date,
    target: String
});

Like.plugin(mongoosePaginate);

module.exports = mongoose.model('Like', Like);
