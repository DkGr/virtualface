var express = require('express');
var Notification = require('../../models/notification');
var router = express();

router.get('/', function(req, res) {
  var aggregate = Notification.aggregate().allowDiskUse(true);
  var pageNum = 1;
  Notification.find({recipient: req.user.username}, null, {sort: '-date'}, function(err, notifs) {
    if (err) return next(err);
    res.json(notifs);
  });
});

router.post('/:notifid', function(req, res) {
  Notification.findById(req.params.notifid, function(err, notif){
    if (err) return next(err);
    notif.read = true;
    notif.save();
    res.json();
  });
});

module.exports = router;
