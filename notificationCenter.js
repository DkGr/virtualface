var Notification = require('./models/notification');
var config = require('./config/config');

var notificationCenter = {
  notify : function(sender, targetUser, content){
    if(sender != targetUser){
      console.log("sender:"+sender+" target:"+targetUser);
      var notif = {
        recipient: targetUser,
        content: content,
        date: new Date(),
        read: false
      };
      Notification.create(notif, function (err, notification) {
        if (err) return next(err);
      });
    }
  }
};

module.exports = notificationCenter;
