var Account = require('./models/account');
var config = require('./config/config');

var visibilityType = {
  Me: 0,
  Friends: 1,
  Everybody: 2
};

var privacyGuard = {
  pleaseShowMeUserInformation : function(requester, wantedUser, callbackGuard){
    Account.findOne({ 'username' : requester }, function (err, user1) {
      if(!user1){
        user1 = { username: '#anonymous#' };
      }
      Account.findOne({ 'username' : wantedUser }, function (err, user2) {
        visibility = privacyGuard.getUserVisibilityTypeBetween(user1, user2);
        switch(visibility)
        {
          case visibilityType.Me:
            var rtnUser = {
                username: user2.username,
                displayname: user2.displayname,
                email: user2.email,
                avatar: user2.avatar,
                facebookId: user2.facebookId,
                friends: user2.friends,
                privateKey: user2.privateKey,
                publicKey: user2.publicKey
            };
            console.log("me");
            callbackGuard(rtnUser);
            break;
          case visibilityType.Friends:
            var rtnUser = {
                username: user2.username,
                displayname: user2.displayname,
                email: user2.email,
                avatar: user2.avatar,
                friends: user2.friends,
                publicKey: user2.publicKey
            };
            console.log("friends");
            callbackGuard(rtnUser);
            break;
          case visibilityType.Everybody:
            var rtnUser = {
                username: user2.username,
                avatar: "../images/no_avatar.png",
                publicKey: user2.publicKey
            };
            console.log("everybody");
            callbackGuard(rtnUser);
            break;
        }
      });
    });
  },
  getUserVisibilityTypeBetween : function(requester, wantedUser){
    if(requester.username == wantedUser.username) {
      return visibilityType.Me;
    }
    else if (Account.isMyFriend(requester, wantedUser)) {
      return visibilityType.Friends;
    }
    else {
      return visibilityType.Everybody;
    }
  }
};

module.exports = privacyGuard;
