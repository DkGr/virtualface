$(document).ready(function() {
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

  $("#newpost-content").keyup(function () {
    for (var i in map) {
      var regex = new RegExp(escapeSpecialChars(i), 'gim');
      this.value = this.value = this.value.replace(regex, map[i]);
    }
  });

  $("#button-send-newpost").click(function() {
    //$('.error').hide();
    var userid = $("#newpost-userid").val();
    /*if (name == "") {
      $("label#name_error").show();
      $("input#name").focus();
      return false;
    }*/
    var content = $("#newpost-content").val();
    /*if (email == "") {
      $("label#email_error").show();
      $("input#email").focus();
      return false;
    }*/

    $("#newpost-content").val("");

    var dataString = 'userid='+ userid + '&content=' + content;
    //alert (dataString);return false;
    $.ajax({
      type: "POST",
      url: "functions/send-newpost.php",
      data: dataString,
      success: function() {
        loadPosts();
        /*$('#contact_form').html("<div id='message'></div>");
        $('#message').html("<h2>Contact Form Submitted!</h2>")
        .append("<p>We will be in touch soon.</p>")
        .hide()
        .fadeIn(1500, function() {
          $('#message').append("<img id='checkmark' src='images/check.png' />");
        });*/
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

  updateNotifications();
  $('[data-toggle="popover"]').popover({'html':'true','placement':'bottom','trigger':'focus'})

  var searchFriendBar = $('#searchFriendBar').magicSuggest({
      data: 'functions/get-all-users.php',
      valueField: 'id',
      displayField: 'username'
  });
  $(searchFriendBar).on('selectionchange', function(e,m){
    showIdentity(this.getValue());
  });
});

function escapeSpecialChars(regex) {
   return regex.replace(/([()[{*+.$^\\|?])/g, '\\$1');
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
      }
      refreshPage();
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
      $('[data-toggle="popover"]').popover({'html':'true','placement':'bottom','trigger':'focus'})
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
