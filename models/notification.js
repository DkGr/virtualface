var mongoose = require('mongoose');
var mongoosePaginate = require('mongoose-aggregate-paginate');
var Schema = mongoose.Schema;

var Notification = new Schema({
    recipient: String,
    date: Date,
    content: String,
    read: Boolean
});

Notification.plugin(mongoosePaginate);

module.exports = mongoose.model('Notification', Notification);
