/*
   Virtual iD Angular Application
   author: n.octeau <padman@protonmail.ch>
   version: 0.0.25
*/
'use strict';

(function (global) {
  'use strict'
  
  var XMPP = global.XMPP

  /* Note these are connection details for a local dev server :) */
  var client = new XMPP.Client({
    // websocket: { url: 'ws://localhost:5280/xmpp-websocket/' },
    bosh: {url: 'https://www.octeau.fr:5280/http-bind/'},
    jid: 'admin@octeau.fr',
    password: 'admin'
  })

  client.on('online', function () {
    console.log('online')
  })

  client.on('error', function (err) {
    console.error(err)
  })
}(this))
var notifIdsUnread = [];
Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};

function updateNotifications()
{
  $.ajax({
    type: "GET",
    url: "api/notifications",
    complete: function(response) {
      if((response.status === 401) || (response.status === 403))
      {
        window.location = "stream";
      }
      var notifContent = '<div style="height:200px;overflow-y:scroll;border: 1px solid #DDD;">';
      var notifCountUnread = 0;
      var hasNotifications = false;
      var htmlNotifications = '';
      var jsonNotifications = JSON.parse(response.responseText);
      jsonNotifications.forEach(function(entry) {
        hasNotifications = true;
        notifContent += renderNotification(entry);
        if(!entry['read'])
        {
            notifCountUnread++;
            notifIdsUnread[notifCountUnread-1] = entry['_id'];
        }
      });
      if(!hasNotifications)
      {
        notifContent = 'Il n\'y a rien à afficher ici pour l\'instant';
        htmlNotifications = '<a tabindex="0" class="btn" role="button" data-toggle="popover" style="width: 250px;" data-content="' + notifContent + '"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span> Notifications</a>';
      }
      else{
        notifContent += '</div>';
        if(notifCountUnread > 0)
        {
          document.title = "("+notifCountUnread+") VirtualID - Votre flux d'actualités";
          htmlNotifications = '<a tabindex="0" class="btn" role="button" data-toggle="popover" style="width: 250px;" data-content="'+
                htmlEntities('<p style="text-align: center;"><a href="javascript:void(0)" onclick="setAllNotifRead();">Tout marquer comme lu</a></p>'+notifContent)+
                '"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span> Notifications <span class="badge" style="background-color:#e60000;">'+notifCountUnread+'</span></a>';
        }
        else {
          htmlNotifications = '<a tabindex="0" class="btn" role="button" data-toggle="popover" style="width: 250px;" data-content="'+htmlEntities(notifContent)+'"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span> Notifications</a>';
        }
      }
      $("#notifPanel").html(htmlNotifications);
      $('[data-toggle="popover"]').popover({'html':'true','placement':'bottom','trigger':'focus'});
    }
  });
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function renderNotification(jsonNotif)
{
    var postedTimeStr = prettyDate(jsonNotif.date);
    if(jsonNotif['read'])
    {
      return '<div style="display: inline-block;width: 100%;" class="list-group-item"><div>'+jsonNotif['content']+'<p style="margin: 0px 0px 0px 0px;font-size: 12px;color:grey;">'+postedTimeStr+'</p></div></div>';
    }
    else {
      return '<div style="display: inline-block;width: 100%;margin-bottom: -6px;" class="list-group-item"><div style="width: 90%;float: left;">'+jsonNotif['content']+'<p style="margin: 0px 0px 0px 0px;font-size: 12px;color:grey;">'+postedTimeStr+'</p></div><div><a style="float:right;" href="javascript:void(0)" onclick="setNotifRead(\''+jsonNotif['_id']+'\')"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></div></div>';
    }
}

function setAllNotifRead(){
  notifIdsUnread.forEach(function(entry) {
    $.ajax({
        async: false,
        type: "POST",
        url: "api/notifications/"+entry,
        complete: function(response) {

        }
      });
  });
  updateNotifications();
}

function setNotifRead(notifId){
  $.ajax({
    type: "POST",
    url: "api/notifications/"+notifId,
    complete: function(response) {
      updateNotifications();
    }
  });
}

$(document).ready(function() {
    
  setTimeout(function(){
      var searchFriendBar = $('#searchFriendBar').magicSuggest({
          allowFreeEntries: false,
          data: 'api/allusers/',
          valueField: 'username',
          displayField: 'username',
          maxSelection: 1,
          maxSuggestions: 10
      });
      var subscribeHashTag = $('#subscribeHashTag').magicSuggest({
          allowFreeEntries: true,
          maxSuggestions: 10
      });
      $(searchFriendBar).on('selectionchange', function(e,m){
        window.location = 'identity/'+this.getValue();
      });
      $('[data-toggle="tooltip"]').tooltip({'trigger':'focus'});

      $("#subscribeButton").click(function() {
        subscribe();
      });
  }, 500);
  updateNotifications();
  setInterval(updateNotifications, 30000);
});

function prettyDate(time){
  var d = new Date();
  var date = new Date(time),
      diff = ((d.getTime() - date.getTime()) / 1000),
      day_diff = Math.floor(diff / 86400);
   if ( isNaN(day_diff) || day_diff < 0 )
      return;
  var dayStr = "dimanche";
  switch (date.getDay()) {
    case 0:
      dayStr = "dimanche";
      break;
    case 1:
      dayStr = "lundi";
      break;
    case 2:
      dayStr = "mardi";
      break;
    case 3:
      dayStr = "mercredi";
      break;
    case 4:
      dayStr = "jeudi";
      break;
    case 5:
      dayStr = "vendredi";
      break;
    case 6:
      dayStr = "samedi";
      break;
    default:  
  }
  var monthStr = "janvier"
  switch (date.getMonth()) {
    case 0:
      monthStr = "janvier";
      break;
    case 1:
      monthStr = "février";
      break;
    case 2:
      monthStr = "mars";
      break;
    case 3:
      monthStr = "avril";
      break;
    case 4:
      monthStr = "mai";
      break;
    case 5:
      monthStr = "juin";
      break;
    case 6:
      monthStr = "juillet";
      break;
    case 7:
      monthStr = "août";
      break;
    case 8:
      monthStr = "septembre";
      break;
    case 9:
      monthStr = "octobre";
      break;
    case 10:
      monthStr = "novembre";
      break;
    case 11:
      monthStr = "décembre";
      break;
    default:  
  }
  
  return day_diff == 0 && (
          diff < 60 && "à l'instant" ||
          diff < 120 && "il y a 1 minute" ||
          diff < 3600 && "il y a " + Math.floor( diff / 60 ) + " minutes" ||
          diff < 7200 && "il y a 1 heure" ||
          diff < 86400 && "il y a " + Math.floor( diff / 3600 ) + " heures") ||
      day_diff == 1 && "Hier" ||
      day_diff < 7 && "il y a " + day_diff + " jours" ||
      day_diff >= 7 && "le " + dayStr+ " " + ((date.getDate()<10?'0':'')+date.getDate()) + " " + monthStr + " " + date.getFullYear() + " à " + ((date.getHours()<10?'0':'')+date.getHours()) + ":" + ((date.getMinutes()<10?'0':'')+date.getMinutes()) + ":" + ((date.getSeconds()<10?'0':'')+date.getSeconds());
}

function subscribe()
{
  var displayname = $("#displayname-sub").val();
  var username = $("#username-sub").val();
  var email = $("#email-sub").val();
  var password = $("#password-sub").val();
  var passcheck = $("#passwordcheck-sub").val();

  if(password != passcheck)
  {
    $("#errormessage").html('Les mots de passe ne correspondent pas.');
    return;
  }

  if(username!='' && password!='' && passcheck!='')
  {
    if(displayname == '')
    {
        displayname = username;
    }
    $.ajax({
        url: "api/allusers/check/",
        type: "POST",
        contentType: 'application/json',
        data: JSON.stringify({name: username}),
        success: function(data) {
          $('#subscribingModal').modal('show');
          var domain = window.location.host;
          var options = {
              numBits: 2048,
              userIds: [{name:username, email:username+'@'+domain}],
              passphrase: password,
              unlocked: false
          };

          var privkey;
          var pubkey;

          openpgp.generateKey(options).then(function(keypair) {
              // success
              privkey = keypair.privateKeyArmored;
              pubkey = keypair.publicKeyArmored;
              var jsonUser = { 'displayname':displayname, 'username':username, 'email':email, 'password':password, 'private_key' : privkey, 'public_key' : pubkey };
              $.ajax({
                  url: "register",
                  type: "POST",
                  contentType: 'application/json',
                  data: JSON.stringify(jsonUser),
                  success: function(data) {
                    window.location = 'stream';
                  }
              });
          }).catch(function(error) {
              $('#subscribingModal').modal('hide');
              $("#errormessage").html("Impossible de générer les clés de chiffrement.");
              return;
          });
        },
        error: function(data){
          $("#errormessage").html("Nom d'utilisateur non-disponible.");
        }
    });
  }
}

var virtualidApp = angular.module('virtualidApp', ['ngResource', 'ngSanitize', 'ngEmbed', 'infinite-scroll'])

.factory('Posts', ['$resource', '$http', function($resource, $http){
  var Posts = $resource('./api/posts/:id', null, {
    'update': { method:'PUT' }
  });
  
  delete Posts.prototype.items;
  Posts.prototype.items = [];
  Posts.prototype.busy = false;
  Posts.prototype.page = 0;
  
  Posts.prototype.nextPage = function() {
    if (this.busy) return;
    this.busy = true;
    this.page++;
    var postsUrl = "api/posts/" + this.page;
    var commentsUrl = "api/comments/";
    var likesUrl = "api/likes/post/";
    $http.get(postsUrl).then(function successCallback(response) {
      var recItems = response.data;
      if(recItems.length == 0){
        this.page--;
      }
      for (var i = 0; i < recItems.length; i++) {
        this.items.push(recItems[i]);
        var idx = this.items.length-1;
        (function (idx, currentPosts){
          $http.get(commentsUrl + recItems[i]._id).then(function successCallback(response) {
            var comments = response.data;
            this.items[idx].commentList = [];
            if(comments.length > 0){
              this.items[idx].commentList = comments;
            }
          }.bind(currentPosts), function errorCallback(response) {
            console.log(response);
          });
          $http.get(likesUrl + recItems[i]._id).then(function successCallback(response) {
            var likes = response.data;
            this.items[idx].likeList = [];
            if(likes.length > 0){
              this.items[idx].likeList = likes;
            }
          }.bind(currentPosts), function errorCallback(response) {
            console.log(response);
          });
        })(idx, this);
      }
      console.log(this);
      this.busy = false;
    }.bind(this), function errorCallback(response) {
      console.log(response);
    });
  };
  return Posts;
}])

.controller('StreamController', ['$scope', '$http', 'Posts', function StreamController($scope, $http, Posts) {
  $http.get('api/users/me').then(function successCallback(response) {
    $scope.user = response.data;
  });
  $scope.posts = new Posts();
  $scope.posts.nextPage();
  $scope.formatDate = prettyDate;
  $scope.printCommentLinkText = function(commentCount) {
    switch (commentCount) {
      case 0:
        return "Aucuns commentaires"
        break;
      case 1:
        return "1 commentaire"
        break;
      default:
        return "" + commentCount + " commentaires"
        break;
    }
  };
  
  $scope.printPostLikeLinkText = function(postid) {
    if(!postid || postid == '') return;
    for(var i = 0; i < $scope.posts.items.length; i++){
      if($scope.posts.items[i]._id == postid){
        if($scope.posts.items[i].likeList){
          for(var j = 0; j < $scope.posts.items[i].likeList.length; j++){
            if($scope.posts.items[i].likeList[j].author == $scope.user.username){
              return "Je n'aime plus";
            }
          }
        }
      }
    }
    return "J'aime";
  };
  
  $scope.embedOptions = {
    fontSmiley       : true,      //convert ascii smileys into font smileys
    emoji            : true,      //convert emojis short names into images
    link             : true,      //convert links into anchor tags
    linkTarget       : '_self',   //_blank|_self|_parent|_top|framename
    pdf              : {
      embed: true                 //to show pdf viewer.
    },
    image            : {
      embed: true                //to allow showing image after the text gif|jpg|jpeg|tiff|png|svg|webp.
    },
    audio            : {
      embed: true                 //to allow embedding audio player if link to
    },
    code             : {
        highlight  : false,        //to allow code highlighting of code written in markdown
        //requires highligh.js (https://highlightjs.org/) as dependency.
        lineNumbers: false        //to show line numbers
    },
    basicVideo       : true,     //to allow embedding of mp4/ogg format videos
    gdevAuth         :'AIzaSyAZ12iOdOJqs5R6ZEgBlVThSBql2z_ldM0', // Google developer auth key for youtube data api
    video            : {
        embed           : true,    //to allow youtube/vimeo video embedding
        width           : null,     //width of embedded player
        height          : 360,     //height of embedded player
        ytTheme         : 'dark',   //youtube player theme (light/dark)
        details         : true,    //to show video details (like title, description etc.)
        autoPlay        : true,     //to autoplay embedded videos
    },
    tweetEmbed       : true,
    tweetOptions     : {
        //The maximum width of a rendered Tweet in whole pixels. Must be between 220 and 550 inclusive.
        maxWidth  : 550,
        //When set to true or 1 links in a Tweet are not expanded to photo, video, or link previews.
        hideMedia : false,
        //When set to true or 1 a collapsed version of the previous Tweet in a conversation thread
        //will not be displayed when the requested Tweet is in reply to another Tweet.
        hideThread: false,
        //Specifies whether the embedded Tweet should be floated left, right, or center in
        //the page relative to the parent element.Valid values are left, right, center, and none.
        //Defaults to none, meaning no alignment styles are specified for the Tweet.
        align     : 'none',
        //Request returned HTML and a rendered Tweet in the specified.
        //Supported Languages listed here (https://dev.twitter.com/web/overview/languages)
        lang      : 'fr'
    },
    twitchtvEmbed    : true,
    dailymotionEmbed : true,
    tedEmbed         : true,
    dotsubEmbed      : true,
    liveleakEmbed    : true,
    soundCloudEmbed  : true,
    soundCloudOptions: {
        height      : 160, themeColor: 'f50000',   //Hex Code of the player theme color
        autoPlay    : false,
        hideRelated : false,
        showComments: true,
        showUser    : true,
        showReposts : false,
        visual      : true,         //Show/hide the big preview image
        download    : true          //Show/Hide download buttons
    },
    spotifyEmbed     : true,
    codepenEmbed     : false,        //set to true to embed codepen
    codepenHeight    : 300,
    jsfiddleEmbed    : false,        //set to true to embed jsfiddle
    jsfiddleHeight   : 300,
    jsbinEmbed       : false,        //set to true to embed jsbin
    jsbinHeight      : 300,
    plunkerEmbed     : false,        //set to true to embed plunker
    githubgistEmbed  : false,
    ideoneEmbed      : false,        //set to true to embed ideone
    ideoneHeight:300
  };
  $scope.sendNewPostComment = function(postid){
    if(!postid || postid == '') return;
    for(var i = 0; i < $scope.posts.items.length; i++){
      if($scope.posts.items[i]._id == postid){
        var newComment = {
          author: $scope.user.username,
          date: new Date(),
          content: $("#newcomment-content-"+postid).val(),
          likes: []
        };
        var commentsUrl = "api/comments/";
        $http({
            url: commentsUrl + postid,
            method: "POST",
            data: newComment,
            headers: {'Content-Type': 'application/json'}
        }).success(function (data, status, headers, config) {
            var comment = data;
            newComment = comment;
        }).error(function (data, status, headers, config) {
        });
        newComment.authorInfos = [];
        newComment.authorInfos[0] = $scope.user;
        $scope.posts.items[i].commentList.push(newComment);
        $("#newcomment-content-"+postid).val('');
      }
    }
  }
  $scope.sendNewPost = function(){
    if(!$scope.newPostContent || $scope.newPostContent.length < 1) return;
    var newPost = new Posts({ author: $scope.user.username, date: new Date(), content: $scope.newPostContent });
    newPost.$save(function(){
      newPost.commentList = [];
      newPost.authorInfos = [];
      newPost.authorInfos[0] = $scope.user;
      $scope.posts.items.unshift(newPost);
      $scope.newPostContent = '';
    });
  }
  
  $scope.sendNewPostLike = function(postid){
    if(!postid || postid == '') return;
    for(var i = 0; i < $scope.posts.items.length; i++){
      if($scope.posts.items[i]._id == postid){
        if($scope.posts.items[i].likeList){
          for(var j = 0; j < $scope.posts.items[i].likeList.length; j++){
            if($scope.posts.items[i].likeList[j].author == $scope.user.username){
              $http.delete('api/likes/post/' + $scope.posts.items[i]._id + '/' + $scope.posts.items[i].likeList[j]._id).then(function successCallback(response) {
              });
              $scope.posts.items[i].likes.remove(j);
              $scope.posts.items[i].likeList.remove(j);
              console.log($scope.post);
              $("#postlike-"+postid).html("J'aime");
              return;
            }
          }
        }
      }
    }
    $("#postlike-"+postid).html("Je n'aime plus");
    for(var i = 0; i < $scope.posts.items.length; i++){
      if($scope.posts.items[i]._id == postid){
        var newLike = {
          author: $scope.user.username,
          date: new Date(),
          target: 'ObjectId("'+postid+'")'
        };
        var postLikesUrl = "api/likes/post/";
        (function (i){
          $http({
              url: postLikesUrl + postid,
              method: "POST",
              data: newLike,
              headers: {'Content-Type': 'application/json'}
          }).success(function (data, status, headers, config) {
              var like = data;
              like.authorInfos = [];
              like.authorInfos[0] = $scope.user;
              $scope.posts.items[i].likes.push(like._id);
              $scope.posts.items[i].likeList.push(like);
          }).error(function (data, status, headers, config) {
          }); 
        })(i);       
      }
    }
  }
}])

.controller('MyFriendsController', function MyFriendsController($scope) {
  //load and display my friends
});
