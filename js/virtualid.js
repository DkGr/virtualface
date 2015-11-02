var map = {
       "<3": "\u2764\uFE0F",
       "</3": "\uD83D\uDC94",
       ":D": "\uD83D\uDE01",
       ":)": "\uD83D\uDE03",
       "xD": "\uD83D\uDE06",
       "^^'": "\uD83D\uDE05",
       ";)": "\uD83D\uDE09",
       ":(": "\uD83D\uDE1E",
       ":p": "\uD83D\uDE1B",
       ";p": "\uD83D\uDE1C",
       ":'(": "\uD83D\uDE22"
};

$(document).ready(function() {
  $("#validate-sub").click(function() {
    subscribe();
  });

  $("#newpost-content").keyup(function () {
    for (var i in map) {
      var regex = new RegExp(escapeSpecialChars(i), 'gim');
      this.value = this.value = this.value.replace(regex, map[i]);
    }
  });

  $("#button-send-newpost").click(function() {
    var userid = $("#newpost-userid").val();
    var content = $("#newpost-content").val();

    $("#newpost-content").val("");

    var dataString = 'userid='+ userid + '&content=' + content;
    $.ajax({
      type: "POST",
      url: "functions/send-newpost.php",
      data: dataString,
      success: function() {
        loadPosts();
      }
    });
    return false;
  });

  $("#btnAddFriend").click(function() {
    addFriend(false);
    return false;
  });

  $("#btnAcceptFriend").click(function() {
    addFriend(true);
    return false;
  });

  $("#notifRead").click(function() {
    setNotifRead();
    return false;
  });

  $('[data-toggle="tooltip"]').tooltip()

  setInterval(updateNotifications, 10000);

  var searchFriendBar = $('#searchFriendBar').magicSuggest({
      allowFreeEntries: false,
      data: 'functions/get-all-users.php',
      valueField: 'id',
      displayField: 'userresult'
  });
  $(searchFriendBar).on('selectionchange', function(e,m){
    showIdentity(this.getValue());
  });
});

function subscribe()
{
  var displayname = $("#displayname").val();
  var username = $("#username").val();
  var email = $("#email").val();
  var password = $("#password").val();
  var passcheck = $("#passwordcheck").val();

  if(password != passcheck)
  {
    $("#errormessage").html("Les mots de passe ne correspondent pas.");
    return;
  }

  var dataString = 'displayname='+displayname+'&username='+username+'&email='+email+'&password='+password+'&subscribe=subscribe';

  if(displayname!='' && username!='' && password!='' && passcheck!='')
  {
    $('#subscribingModal').modal('show');
    $.ajax({
      type: "POST",
      url: "functions/subscribe.php",
      data: dataString,
      complete: function(response) {
        $("#errormessage").html(response.responseText);
        var code = $("#validationcode").val();
        if(code != '')
        {
          var domain = window.location.host.replace('www.','');
          var options = {
              numBits: 2048,
              userId: username+' <'+username+'@'+domain+'>',
              passphrase: code
          };

          openpgp.generateKeyPair(options).then(function(keypair) {
              // success
              var privkey = keypair.privateKeyArmored;
              var pubkey = keypair.publicKeyArmored;

              // collect the form data while iterating over the inputs
              var data = {};
              data['username'] = username;
              data['code'] = code;
              data['privkey'] = privkey;
              data['pubkey'] = pubkey;

              // construct an HTTP request
              var xhr = new XMLHttpRequest();
              xhr.open("POST", "functions/save-keys.php", true);
              xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

              // send the collected data as JSON
              xhr.send(JSON.stringify(data));

              xhr.onloadend = function () {
                window.location = "stream.php";
              };
          }).catch(function(error) {
              // failure
          });
        }
        else
        {
          return;
        }
      }
    });
  }
}

function encryptPost()
{
  var jsonResponse;
  var keys_str = '';
  var data = {};

  data['userid'] = username;
  // construct an HTTP request
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "functions/save-keys.php", true);
  xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
  // send the collected data as JSON
  xhr.send(JSON.stringify(data));
  xhr.onloadend = function () {
     jsonResponse = xhr.responseText;
  };
  for(var k in jsonResponse) {
     keys_str += jsonResponse[k];
  }
  var publicKey = openpgp.key.readArmored(keys_str);

  openpgp.encryptMessage(publicKey.keys, 'Hello, World!').then(function(pgpMessage) {
      // success
      return pgpMessage;
  }).catch(function(error) {
      // failure
  });
}

function escapeSpecialChars(regex) {
   return regex.replace(/([()[{*+.$^\\|?])/g, '\\$1');
}

function sendNewPostLike(postId)
{
  var userId = $("#newpost-userid").val();
  var dataString = 'userid='+ userId + '&targetid=' + postId + '&targettype=post';
  $.ajax({
    type: "POST",
    url: "functions/send-newlike.php",
    data: dataString,
    success: function() {
      loadPosts();
    }
  });
}

function sendNewCommentLike(commentId)
{
  var userId = $("#newpost-userid").val();
  var dataString = 'userid='+ userId + '&targetid=' + commentId + '&targettype=comment';
  $.ajax({
    type: "POST",
    url: "functions/send-newlike.php",
    data: dataString,
    success: function() {
      loadPosts();
    }
  });
}

function deleteLike(likeId)
{
  var userId = $("#newpost-userid").val();
  var dataString = 'userid='+ userId + '&likeid=' + likeId;
  $.ajax({
    type: "POST",
    url: "functions/delete-like.php",
    data: dataString,
    success: function() {
      loadPosts();
    }
  });
}

function deleteComment(commentId)
{
  var userId = $("#newpost-userid").val();
  var dataString = 'userid='+ userId + '&commentid=' + commentId;
  $.ajax({
    type: "POST",
    url: "functions/delete-comment.php",
    data: dataString,
    success: function() {
      loadPosts();
    }
  });
}

function deletePost(postId)
{
  var userId = $("#newpost-userid").val();
  var dataString = 'userid='+ userId + '&postid=' + postId;
  $.ajax({
    type: "POST",
    url: "functions/delete-post.php",
    data: dataString,
    success: function() {
      loadPosts();
    }
  });
}

function refreshPage()
{
  var userId = $("#userid").val();
  var url = 'identity.php?userid='+userId;
  var multipart = true;
  var form = document.createElement("FORM");
  form.method = "POST";
  if(multipart) {
    form.enctype = "multipart/form-data";
  }
  form.style.display = "none";
  document.body.appendChild(form);
  form.action = url.replace(/\?(.*)/, function(_, urlArgs) {
        urlArgs.replace(/\+/g, " ").replace(/([^&=]+)=([^&=]*)/g, function(input, key, value) {
        input = document.createElement("INPUT");
        input.type = "hidden";
        input.name = decodeURIComponent(key);
        input.value = decodeURIComponent(value);
        form.appendChild(input);
      });
    return "";
  });
  form.submit();
}

function showIdentity(userId)
{
  var url = 'identity.php?userid='+userId;
  var multipart = true;
  var form = document.createElement("FORM");
  form.method = "POST";
  if(multipart) {
    form.enctype = "multipart/form-data";
  }
  form.style.display = "none";
  document.body.appendChild(form);
  form.action = url.replace(/\?(.*)/, function(_, urlArgs) {
        urlArgs.replace(/\+/g, " ").replace(/([^&=]+)=([^&=]*)/g, function(input, key, value) {
        input = document.createElement("INPUT");
        input.type = "hidden";
        input.name = decodeURIComponent(key);
        input.value = decodeURIComponent(value);
        form.appendChild(input);
      });
    return "";
  });
  form.submit();
}

function showIdentityFromPost(postId)
{
  var url = 'identity.php?postid='+postId;
  var multipart = true;
  var form = document.createElement("FORM");
  form.method = "POST";
  if(multipart) {
    form.enctype = "multipart/form-data";
  }
  form.style.display = "none";
  document.body.appendChild(form);
  form.action = url.replace(/\?(.*)/, function(_, urlArgs) {
        urlArgs.replace(/\+/g, " ").replace(/([^&=]+)=([^&=]*)/g, function(input, key, value) {
        input = document.createElement("INPUT");
        input.type = "hidden";
        input.name = decodeURIComponent(key);
        input.value = decodeURIComponent(value);
        form.appendChild(input);
      });
    return "";
  });
  form.submit();
}

function loadPosts(){
  var userId = $("#userid").val();
  $.ajax({
    type: "GET",
    url: "functions/display-posts.php",
    complete: function(response) {
      $("#posts-stream").hide();
      $("#posts-stream").html(response.responseText);
      $("#posts-stream").fadeIn();
      $("#newcomment-content").keyup(function () {
        for (var i in map) {
          var regex = new RegExp(escapeSpecialChars(i), 'gim');
          this.value = this.value = this.value.replace(regex, map[i]);
        }
      });
      $(".button-send-newcomment").click(function() {
        console.log('clicked');
        var userid = $("#newpost-userid").val();
        var postid = $(this).val();
        var content = $("#newcomment-content-"+postid).val();

        $("#newcomment-content").val("");

        var dataString = 'postid=' + postid + '&userid='+ userid + '&content=' + content;
        $.ajax({
          type: "POST",
          url: "functions/send-newcomment.php",
          data: dataString,
          success: function() {
            loadPosts();
          }
        });
        return false;
      });
    }
  });
}

function loadIdentityPosts(){
  var userId = $("#userid").val();
  var dataString = 'userid='+ userId;
  $.ajax({
    type: "POST",
    url: "functions/display-user-posts.php",
    data: dataString,
    complete: function(response) {
      $("#posts-stream").hide();
      $("#posts-stream").html(response.responseText);
      $("#posts-stream").fadeIn();
      $("#newcomment-content").keyup(function () {
        for (var i in map) {
          var regex = new RegExp(escapeSpecialChars(i), 'gim');
          this.value = this.value = this.value.replace(regex, map[i]);
        }
      });
      $(".button-send-newcomment").click(function() {
        console.log('clicked');
        var userid = $("#newpost-userid").val();
        var postid = $(this).val();
        var content = $("#newcomment-content-"+postid).val();

        $("#newcomment-content").val("");

        var dataString = 'postid=' + postid + '&userid='+ userid + '&content=' + content;
        $.ajax({
          type: "POST",
          url: "functions/send-newcomment.php",
          data: dataString,
          success: function() {
            loadPosts();
          }
        });
        return false;
      });
    }
  });
}

function addFriend(isAccepting){
  var userId = $("#userid").val();
  var dataString = 'friendid='+ userId;
  $.ajax({
    type: "POST",
    url: "functions/add-friend.php",
    data: dataString,
    success: function(data) {
      if(isAccepting)
      {
        converse.user.logout();
        setTimeout(function() {
              refreshPage();
        }, 1000);
      }
      else{
        refreshPage();
      }
    }
  });
}

function updateNotifications()
{
  var userId = $("#newpost-userid").val();
  var dataString = 'userid='+ userId;
  $.ajax({
    type: "POST",
    url: "functions/update-notifications.php",
    data: dataString,
    complete: function(response) {
      $("#notifPanel").html(response.responseText);
      $('[data-toggle="popover"]').popover({'html':'true','placement':'bottom','trigger':'focus'});
    }
  });
}

function setNotifRead(notifId){
  var dataString = 'notifid='+ notifId;
  $.ajax({
    type: "GET",
    url: "functions/set-notification-read.php",
    data: dataString,
    complete: function(response) {
      updateNotifications();
    }
  });
}
