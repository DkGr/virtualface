/*
   VirtualID Angular Application
   author: n.octeau <padman@protonmail.ch>
   version: 0.0.25
*/
'use strict';
openpgp.initWorker({ path:'/virtualid/javascripts/openpgp.worker.min.js' });
openpgp.config.aead_protect = true;

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
});

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

var virtualidApp = angular.module('virtualidApp', ['ngResource'])

.factory('Posts', ['$resource', function($resource){
  return $resource('./api/posts/:id', null, {
    'update': { method:'PUT' }
  });
}])

.controller('StreamController', ['$scope', '$http', 'Posts', function StreamController($scope, $http, Posts) {
  $scope.posts = Posts.query();

  $scope.sendNewPost = function(){
    if(!$scope.newPostContent || $scope.newPostContent.length < 1) return;
    var newPost = new Posts({ author: $("#username").val(), date: new Date(), content: $scope.newPostContent });
    newPost.$save(function(){
      $scope.posts = Posts.query();
      $scope.newPostContent = '';
    });
  }
}])

.controller('MyFriendsController', function MyFriendsController($scope) {
  //load and display my friends
});
