var mongoose = require('mongoose');
var supergoose = require('supergoose');
var Schema = mongoose.Schema;
var passportLocalMongoose = require('passport-local-mongoose');

var Account = new Schema({
    username: String,
    displayname: String,
    email: String,
    avatar: String,
    facebookId: String,
    friends: Array,
    privateKey: String,
    publicKey: String
});

Account.plugin(passportLocalMongoose);
Account.plugin(supergoose);

Account.statics.isMyFriend = function(me, userToCheck){
  for(usrSearch in userToCheck.friends){
    if(usrSearch == me.username){
      return true;
    }
  }
  return false;
};

module.exports = mongoose.model('Account', Account);
