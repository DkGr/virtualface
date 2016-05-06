var emotmap = {
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
var newpostVisibility = 1;
var notifIdsUnread = [];
var friendRequestSent = false;

var createCookie = function(name, value, days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}

function prettyDate(time){
    d = new Date();
    var date = new Date((time || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
        diff = ((d.getTime() - date.getTime()) / 1000),
        day_diff = Math.floor(diff / 86400);
     if ( isNaN(day_diff) || day_diff < 0 )
        return;

    return day_diff == 0 && (
            diff < 60 && "à l'instant" ||
            diff < 120 && "il y a 1 minute" ||
            diff < 3600 && "il y a " + Math.floor( diff / 60 ) + " minutes" ||
            diff < 7200 && "il y a 1 heure" ||
            diff < 86400 && "il y a " + Math.floor( diff / 3600 ) + " heures") ||
        day_diff == 1 && "Hier" ||
        day_diff < 7 && "il y a " + day_diff + " jours" ||
        day_diff >= 7 && "le " + d.getDate() + "/" + d.getMonth() + "/" + d.getFullYear() + " à " + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();
}

function changeNewpostVisibility()
{
    switch(newpostVisibility){
        case 0:
            $("#newpost-visibility-btn").html('<span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Moi uniquement <span class="caret"></span>');
            break;
        case 1:
            $("#newpost-visibility-btn").html('<span class="glyphicon glyphicon-user" aria-hidden="true"></span> Amis <span class="caret"></span>');
            break;
        case 2:
            $("#newpost-visibility-btn").html('<span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Public <span class="caret"></span>');
            break;
    }
}

//let's define some variables for stylish stuff:
var radius = 50;
var arrowStrength = 18;
var triangleSide = 20;
var distanceArrows = 0.3; //this is the distance ( n * pi) between the arrows, do not exagerate
var colorBody = '#313131';
var colorTriangle = '#000000';
var pi = Math.PI;

$(document).ready(function() {
    friendRequestSent = false;
    $("#validate-sub").click(function() {
      subscribe();
    });

    $("#login-btn").click(function() {
      login(null, null);
    });

    $("#saveAccountInfosBtn").click(function() {
      saveInfosSettings();
    });

    $("#newpost-content").keyup(function () {
      for (var i in emotmap) {
        var regex = new RegExp(escapeSpecialChars(i), 'gim');
        this.value = this.value = this.value.replace(regex, emotmap[i]);
      }
    });

    $("#newpost-visibility-public-btn").click(function() {
      newpostVisibility = 2;
      changeNewpostVisibility();
      $("#newpost-visibility-btn").dropdown('toggle');
      return false;
    });

    $("#newpost-visibility-friend-btn").click(function() {
      newpostVisibility = 1;
      changeNewpostVisibility();
      $("#newpost-visibility-btn").dropdown('toggle');
      return false;
    });

    $("#button-send-newpost").click(function() {
      sendPost();
      return false;
    });

    $("#notifRead").click(function() {
      setNotifRead();
      return false;
    });

    $("#username").change(function() {
        $("#username").val($("#username").val().toLowerCase());
    });

    $("#sub-username").change(function() {
        $("#sub-username").val($("#sub-username").val().toLowerCase());
    });
});

function login(subusername, subpassword)
{
    var username = $("#username").val();
    var password = $("#password").val();

    if(username!='' && password!='')
    {
        var datastringLogin = "username="+username+"&password="+password;
        $.ajax({
            url: "webservice/login",
            type: "POST",
            data: datastringLogin,
            processData: false,
            success: function(response) {
                var resp = JSON.parse(response);
                if(resp.hasOwnProperty('error'))
                {
                    $("#login-error").html(resp.error);
                    return;
                }
                else
                {
                    window.location = "stream.php";
                }
            }
        });
    }
    else
    {
        var datastringLogin = "username="+subusername+"&password="+subpassword;
        $.ajax({
            url: "webservice/login",
            type: "POST",
            data: datastringLogin,
            processData: false,
            success: function(response) {
                var resp = JSON.parse(response);
                if(resp.hasOwnProperty('error'))
                {
                    $("#login-error").html(resp.error);
                    return;
                }
                else
                {
                    window.location = "stream.php";
                }
            }
        });
    }
}

function loginfb(fbUserID)
{
    var datastringLogin = "fbuserid="+fbUserID;
    $.ajax({
        url: "webservice/loginfb",
        type: "POST",
        data: datastringLogin,
        processData: false,
        success: function(response) {
            var resp = JSON.parse(response);
            if(resp.hasOwnProperty('error'))
            {
                window.location = "facebook-validation.php";
            }
            else
            {
                window.location = "stream.php";
            }
        }
    });
}

function linktofb(fbUserID)
{
    var datastringLogin = "fbuserid="+fbUserID;
    $.ajax({
        url: "webservice/linktofb",
        type: "POST",
        data: datastringLogin,
        processData: false,
        success: function(response) {
            var resp = JSON.parse(response);
            if(resp.hasOwnProperty('error'))
            {
                $("#fblinked").html('Désactivé <a style="color:red;">('+resp.error+')</a>');
            }
            else
            {
                $("#fblinked").html('Activé');
            }
        }
    });
}

function unlinktofb()
{
    var datastringLogin = "";
    $.ajax({
        url: "webservice/unlinktofb",
        type: "POST",
        data: datastringLogin,
        processData: false,
        success: function(response) {
            location.reload();
        }
    });
}

function logout() {
  converse.user.logout();
  if ((typeof FB !== 'undefined') && connectedWithFacebook) {
    FB.logout(function(response) {
      // Person is now logged out
      $.ajax({
        type: "GET",
        url: "functions/logout.php",
        complete: function(response) {
          window.location = "index.php";
        }
      });
    });
  }
  else{
    $.ajax({
      type: "GET",
      url: "functions/logout.php",
      complete: function(response) {
        window.location = "index.php";
      }
     });
   }
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

function subscribe()
{
  var displayname = $("#sub-displayname").val();
  var username = $("#sub-username").val();
  var email = $("#sub-email").val();
  var password = $("#sub-password").val();
  var passcheck = $("#sub-passwordcheck").val();

  if(password != passcheck)
  {
    $("#errormessage").html("Les mots de passe ne correspondent pas.");
    return;
  }

  if(username!='' && password!='' && passcheck!='')
  {
    if(displayname == '')
    {
        displayname = username;
    }
    $('#subscribingModal').modal('show');
    var jsonUser = { 'displayname':displayname, 'username':username, 'email':email, 'password':password };
    $.ajax({
        url: "webservice/users",
        type: "POST",
        contentType: 'application/json',
        data: JSON.stringify(jsonUser),
        dataType: "json",
        processData: false,
        success: function(response) {
            var resp = response;
            if(resp.hasOwnProperty('error'))
            {
                $('#subscribingModal').modal('hide');
                $("#errormessage").html("Impossible de créer le compte :" + resp.error);
                return;
            }
            else
            {
                var domain = window.location.host;
                var options = {
                    numBits: 2048,
                    userIds: [{name:username, email:username+'@'+domain}],
                    passphrase: resp.infos.password,
                    unlocked: false
                };

                var privkey;
                var pubkey;

                openpgp.generateKey(options).then(function(keypair) {
                    // success
                    privkey = keypair.privateKeyArmored;
                    pubkey = keypair.publicKeyArmored;
                    var jsonKeys = { 'private_key' : privkey, 'public_key' : pubkey };
                    $.ajax({
                        url: "webservice/users/savekeys",
                        type: "POST",
                        contentType: 'application/json',
                        data: JSON.stringify(jsonKeys),
                        dataType: "json",
                        processData: false,
                        success: function(response) {
                            login(username, password);
                        }
                    });
                }).catch(function(error) {
                    $('#subscribingModal').modal('hide');
                    $("#errormessage").html("Impossible de générer les clés de chiffrement.");
                    return;
                });
            }
        }
    });
  }
}

function subscribeFromFacebook(fbUserID, fbUserAvatarURL)
{
  var displayname = $("#displayname").val();
  var username = $("#username").val();
  var email = $("#email").val();
  var password = $("#password").val();
  var passcheck = $("#passcheck").val();

  if(password != passcheck)
  {
    $("#errormessage").html("Les mots de passe ne correspondent pas.");
    return;
  }

  if(username!='' && password!='' && passcheck!='')
  {
    if(displayname == '')
    {
        displayname = username;
    }
    $('#subscribingModal').modal('show');

    var jsonUser = { 'fbuserid':fbUserID, 'fbavatarurl':fbUserAvatarURL, 'displayname':displayname, 'username':username, 'email':email, 'password':password };
    $.ajax({
        url: "webservice/fbusers",
        type: "POST",
        contentType: 'application/json',
        data: JSON.stringify(jsonUser),
        dataType: "json",
        processData: false,
        success: function(response) {
            var resp = response;
            if(resp.hasOwnProperty('error'))
            {
                $('#subscribingModal').modal('hide');
                $("#errormessage").html("Impossible de créer le compte :" + resp.error);
                return;
            }
            else
            {
                var domain = window.location.host;
                var options = {
                    numBits: 2048,
                    userIds: [{name:username, email:username+'@'+domain}],
                    passphrase: resp.infos.password,
                    unlocked: false
                };

                var privkey;
                var pubkey;

                openpgp.generateKey(options).then(function(keypair) {
                    // success
                    privkey = keypair.privateKeyArmored;
                    pubkey = keypair.publicKeyArmored;
                    var jsonKeys = { 'private_key' : privkey, 'public_key' : pubkey };
                    $.ajax({
                        url: "webservice/users/savekeys",
                        type: "POST",
                        contentType: 'application/json',
                        data: JSON.stringify(jsonKeys),
                        dataType: "json",
                        processData: false,
                        success: function(response) {
                            loginfb(fbUserID);
                        }
                    });
                }).catch(function(error) {
                    $('#subscribingModal').modal('hide');
                    $("#errormessage").html("Impossible de générer les clés de chiffrement.");
                    return;
                });
            }
        }
    });
  }
}

function sortJson(arr, prop, asc) {
    arr = arr.sort(function(a, b) {
        if (asc) return (a[prop] > b[prop]) ? 1 : ((a[prop] < b[prop]) ? -1 : 0);
        else return (b[prop] > a[prop]) ? 1 : ((b[prop] < a[prop]) ? -1 : 0);
    });
    return arr;
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function sendPost()
{
    var content = $("#newpost-content").val();
    if(content)
    {
        if(newpostVisibility == 1)
        {
            sendEncryptedPost(content);
        }
        else
        {
            var jsonPost = { 'visibility' : newpostVisibility, 'content' : content };
            $.ajax({
                url: "webservice/posts",
                type: "POST",
                contentType: 'application/json',
                data: JSON.stringify(jsonPost),
                dataType: "json",
                processData: false,
              success: function() {
                loadPosts();
              }
            });
        }
        $("#newpost-content").val("");
    }
}

function sendComment(postid, content, authorid, isPostView)
{
    var jsonComment = { 'postid' : postid, 'content' : content };
    $.ajax({
        url: "webservice/comments",
        type: "POST",
        contentType: 'application/json',
        data: JSON.stringify(jsonComment),
        dataType: "json",
        processData: false,
      success: function() {
          refreshPageContent();
      }
    });
}

function sendEncryptedPost(content)
{
    $.ajax({
        async: false,
        type: "GET",
        url: "webservice/users/current",
        complete: function(response) {
            var keys_str = '';
            var sender = JSON.parse(response.responseText);
            keys_str = sender.public_key;
            var publicKeys = openpgp.key.readArmored(keys_str);
            for(var k in sender.friends) {
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "webservice/users/"+k,
                    complete: function(response) {
                        var recipient = JSON.parse(response.responseText);
                        keys_str = recipient.public_key;
                        publicKeys.keys.push(openpgp.key.readArmored(keys_str).keys[0]);
                    }
                });
            }
            console.log(publicKeys);
            var options;
            options = {
                data: content,
                publicKeys: publicKeys.keys,
                armor: true
            };
            openpgp.encrypt(options).then(function(pgpMessage) {
                // success
                var jsonPost = { 'visibility' : newpostVisibility, 'content' : pgpMessage.data };
                $.ajax({
                    url: "webservice/posts",
                    type: "POST",
                    contentType: 'application/json',
                    data: JSON.stringify(jsonPost),
                    dataType: "json",
                    processData: false,
                  success: function() {
                    loadPosts();
                  }
                });
            }).catch(function(error) {
                console.log(error);
            });
        }
    });
}

function decryptMessage(message)
{
    if(!getCookie('vidcrypt'))
    {

    }
    var dmessage = message;
    $.ajax({
        async: false,
        type: "GET",
        url: "webservice/users/current",
        contentType: 'application/json',
        dataType: "json",
        processData: false,
        success: function(response) {
            var currentUser = response;
            var privateKey = openpgp.key.readArmored(currentUser.private_key).keys[0];
            var unlockedKey = privateKey.decrypt(currentUser.infos.password);
            var pgpMessage = openpgp.message.readArmored(message);
            var decryptedMessage = pgpMessage.decrypt(privateKey);
            dmessage = decryptedMessage.getText();
        }
    });
    return dmessage;
}

function escapeSpecialChars(regex) {
   return regex.replace(/([()[{*+.$^\\|?])/g, '\\$1');
}

function sendNewPostLike(postId)
{
  var jsonLike = { 'targetid' : postId, 'targettype' : 'post' };
  $.ajax({
    url: "webservice/likes",
    type: "POST",
    contentType: 'application/json',
    data: JSON.stringify(jsonLike),
    dataType: "json",
    processData: false,
    complete: function() {
      refreshPageContent();
    }
  });
}

function sendNewCommentLike(commentId)
{
  var jsonLike = { 'targetid' : commentId, 'targettype' : 'comment' };
  $.ajax({
    url: "webservice/likes",
    type: "POST",
    contentType: 'application/json',
    data: JSON.stringify(jsonLike),
    dataType: "json",
    processData: false,
    complete: function() {
      refreshPageContent();
    }
  });
}

function deleteLike(likeId)
{
  var data = { 'likeid' : likeId };
  $.ajax({
    type: "POST",
    url: "webservice/likes/delete",
    contentType: 'application/json',
    data: JSON.stringify(data),
    dataType: "json",
    processData: false,
    complete: function() {
        refreshPageContent();
    }
  });
}

function deleteComment(commentId)
{
  var data = { 'id' : commentId };
  $.ajax({
    type: "POST",
    url: "webservice/comments/delete",
    contentType: 'application/json',
    data: JSON.stringify(data),
    dataType: "json",
    processData: false,
    complete: function() {
        refreshPageContent();
    }
  });
}

function deletePost(postId)
{
  var data = { 'id' : postId };
  $.ajax({
    type: "POST",
    url: "webservice/posts/delete",
    contentType: 'application/json',
    data: JSON.stringify(data),
    dataType: "json",
    processData: false,
    success: function() {
      refreshPageContent();
    }
  });
}

function refreshIdentityPage()
{
  var userid = $("#identity-id").val();
  showIdentity(userid);
}

function showIdentity(userId)
{
  var url = 'identity.php?userid='+userId;
  var multipart = true;
  var form = document.createElement("FORM");
  form.method = "GET";
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
  $.ajax({
    type: "GET",
    url: "webservice/posts",
    complete: function(response) {
      $("#posts-stream").hide();
      var jsonPosts = JSON.parse(response.responseText);
      jsonPosts = sortJson(jsonPosts, 'date', false);
      var htmlStream = '';
      jsonPosts.forEach(function(entry) {
        var htmlPostRenderer = renderPost(entry);
        htmlStream += htmlPostRenderer;
      });
      if(htmlStream.length == 0)
      {
          $("#posts-stream").html("<p style=\"text-align:center;\">Il n'y a rien à afficher pour l'instant</p>");
      }
      else{
          $("#posts-stream").html(htmlStream);
      }

      $("#posts-stream").fadeIn();
      $("#newcomment-content").keyup(function () {
        for (var i in emotmap) {
          var regex = new RegExp(escapeSpecialChars(i), 'gim');
          this.value = this.value = this.value.replace(regex, emotmap[i]);
        }
      });
      $(".button-send-newcomment").click(function() {
        var postid = $(this).val();
        var content = $("#newcomment-content-"+postid).val();

        $("#newcomment-content").val("");
        sendComment(postid, content, null);
        return false;
      });
    }
  });
}

function loadPost(){
    var postid = $("#post-id").val();
  $.ajax({
    type: "GET",
    url: "webservice/posts/"+postid,
    complete: function(response) {
      $("#posts-stream").hide();
      var htmlStream = '';
      var jsonPost;
      if(response.responseText == '')
      {
          htmlStream = '<p style="text-align:center;">Erreur 404 : Contenu introuvable</p>'
      }
      else{
          jsonPost = JSON.parse(response.responseText);
          var htmlPostRenderer = renderPost(jsonPost);
          htmlStream += htmlPostRenderer;
      }
      if(htmlStream.length == 0)
      {
          $("#posts-stream").html("<p style=\"text-align:center;\">Il n'y a rien à afficher pour l'instant</p>");
      }
      else{
          $("#posts-stream").html(htmlStream);
      }

      $("#posts-stream").fadeIn();
      $("#newcomment-content").keyup(function () {
        for (var i in emotmap) {
          var regex = new RegExp(escapeSpecialChars(i), 'gim');
          this.value = this.value = this.value.replace(regex, emotmap[i]);
        }
      });
      $(".button-send-newcomment").click(function() {
        var postid = $(this).val();
        var content = $("#newcomment-content-"+postid).val();

        $("#newcomment-content").val("");
        sendComment(postid, content, null, true);
        return false;
      });
    }
  });
}

function loadIdentityPosts(userid){
  $.ajax({
    type: "GET",
    url: "webservice/users/"+userid+"/posts",
    complete: function(response) {
      $("#posts-stream").hide();
      var jsonPosts = JSON.parse(response.responseText);
      jsonPosts = sortJson(jsonPosts, 'date', false);
      var htmlStream = '';
      jsonPosts.forEach(function(entry) {
        var htmlPostRenderer = renderPost(entry);
        htmlStream += htmlPostRenderer;
      });
      if(htmlStream.length == 0)
      {
          $("#posts-stream").html("<p style=\"text-align:center;\">Il n'y a rien à afficher pour l'instant</p>");
      }
      else{
          $("#posts-stream").html(htmlStream);
      }

      $("#posts-stream").fadeIn();
      $("#newcomment-content").keyup(function () {
        for (var i in emotmap) {
          var regex = new RegExp(escapeSpecialChars(i), 'gim');
          this.value = this.value = this.value.replace(regex, emotmap[i]);
        }
      });
      $(".button-send-newcomment").click(function() {
        var postid = $(this).val();
        var content = $("#newcomment-content-"+postid).val();

        $("#newcomment-content").val("");
        sendComment(postid, content, userid);
        return false;
      });
    }
  });
}

function loadPostComments(postid){
  var jsonComments;
  $.ajax({
    async: false,
    type: "GET",
    url: "webservice/posts/"+postid+"/comments",
    complete: function(response) {
      jsonComments = JSON.parse(response.responseText);
    }
  });
  return jsonComments;
}

function renderComment(jsonComment, commentpair)
{
    var htmlComment;
    $.ajax({
        async: false,
        type: "GET",
        url: "webservice/users/"+jsonComment['author']['$id'],
        complete: function(response) {
            var author = JSON.parse(response.responseText);
            var postedTimeStr = prettyDate(jsonComment.date);
            var authorActionButton = '';
            if(author.hasOwnProperty('private_key'))
            {
                authorActionButton = '<!-- Split button -->'+
                '<div style="display:table-cell;vertical-align: top;" class="btn-group">'+
                  '<button style="padding: 0px 4px;float:right;" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                    '<span class="caret"></span>'+
                    '<span class="sr-only">Toggle Dropdown</span>'+
                  '</button>'+
                  '<ul style="top: 0;left: -165px;" class="dropdown-menu">'+
                    '<li><a href="#">Modifier</a></li>'+
                    '<li><a href="javascript:void(0)" onclick="deleteComment(\''+jsonComment['_id']['$id']+'\');">Supprimer</a></li>'+
                  '</ul>'+
                '</div>';
            }
            var commentpaircolor = 'style="border:none;background-color: rgba(53, 53, 53, 0.03);"';
            if(commentpair)
            {
              commentpaircolor = 'style="border:none;background-color: rgba(53, 53, 53, 0.01);"';
            }
            var myid = $("#myid").val();
            var jsonCommentLikes;
            var commentLikes = 0;
            var hasLiked = false;
            var likeId = '';
            $.ajax({
                async: false,
                type: "GET",
                url: "webservice/comments/"+jsonComment['_id']['$id']+"/likes",
                complete: function(response) {
                    jsonCommentLikes = JSON.parse(response.responseText);
                }
            });
            jsonCommentLikes.forEach(function(entry) {
                commentLikes++;
                if(entry.author.$id == myid)
                {
                    hasLiked = true;
                    likeId = entry._id.$id;
                }
            });
            var commentLikeLink = '<a href="javascript:void(0)" onclick="sendNewCommentLike(\''+jsonComment['_id']['$id']+'\');">J\'aime</a>';
            if(hasLiked)
            {
              commentLikeLink = '<a href="javascript:void(0)" onclick="deleteLike(\''+likeId+'\');">Je n\'aime plus</a>';
            }

            var displayname = author.infos.username;
            if(author.infos.hasOwnProperty('displayname'))
            {
                displayname = author.infos.displayname;
            }
            var d = new Date();
            htmlComment = '<li ' + commentpaircolor + ' class="list-group-item">'+
                            '<div class="media">'+
                              '<div style="text-align:center;width: 12%;" class="media-left">'+
                                '<a href="identity.php?userid=' + jsonComment['author']['$id'] + '">'+
                                  '<img class="media-object img-rounded" style="width: 32px; height: 32px; margin: auto;" src="avatars/' + jsonComment['author']['$id'] + '?' + d.getTime() + '" alt="...">'+
                                '</a>'+
                                '<a href="identity.php?userid=' + jsonComment['author']['$id'] + '">' + displayname + '</a>'+
                              '</div>'+
                              '<div class="media-body">'+
                              findURL(jsonComment['content']) + " " +
                              '</div>' + authorActionButton +
                              '<div style="float: left;margin: 10px 0 0 75px;">' + commentLikeLink + ' - ' + commentLikes + ' <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span class="p-date" style="font-size: 12px;color:grey;">Publié ' + postedTimeStr + '</span></div>'+
                            '</div>'+
                          '</li>';
        }
    });
    return htmlComment;
}

function refreshPageContent()
{
    var currentWebPage = location.pathname.substring(location.pathname.lastIndexOf("/") + 1);
    if(currentWebPage === "stream.php")
    {
        loadPosts();
    }
    else if(currentWebPage === "identity.php")
    {
        loadIdentityPosts();
    }
    else if(currentWebPage === "postview.php")
    {
        loadPost();
    }
}

function renderPost(jsonPost)
{
    var htmlPost;
    $.ajax({
        async: false,
        type: "GET",
        url: "webservice/users/"+jsonPost['author']['$id'],
        complete: function(response) {
            var author = JSON.parse(response.responseText);
            var postedTimeStr = prettyDate(jsonPost.date);
            var authorActionButton = '';
            if(author.hasOwnProperty('private_key'))
            {
                authorActionButton = '<!-- Split button -->'+
                '<div style="display:table-cell;vertical-align: top;" class="btn-group">'+
                  '<button style="padding: 0px 4px;float:right;" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                    '<span class="caret"></span>'+
                    '<span class="sr-only">Toggle Dropdown</span>'+
                  '</button>'+
                  '<ul style="top: 0;left: -165px;" class="dropdown-menu">'+
                    '<li><a href="#">Modifier</a></li>'+
                    '<li><a href="javascript:void(0)" onclick="deletePost(\''+jsonPost['_id']['$id']+'\');">Supprimer</a></li>'+
                  '</ul>'+
                '</div>';
            }

            var myid = $("#myid").val();
            var jsonPostLikes;
            var pLikes = 0;
            var hasLiked = false;
            var likeId = '';
            $.ajax({
                async: false,
                type: "GET",
                url: "webservice/posts/"+jsonPost['_id']['$id']+"/likes",
                complete: function(response) {
                    jsonPostLikes = JSON.parse(response.responseText);
                }
            });
            jsonPostLikes.forEach(function(entry) {
                pLikes++;
                if(entry.author.$id == myid)
                {
                    hasLiked = true;
                    likeId = entry._id.$id;
                }
            });
            var postLikeLink = '<a href="javascript:void(0)" onclick="sendNewPostLike(\''+jsonPost['_id']['$id']+'\');">J\'aime</a>';
            if(hasLiked)
            {
              postLikeLink = '<a href="javascript:void(0)" onclick="deleteLike(\''+likeId+'\');">Je n\'aime plus</a>';
            }

            var jsonComments = loadPostComments(jsonPost['_id']['$id']);
            var htmlComments = '';
            var commentpair = false;
            var commentcount = 0;
            jsonComments.forEach(function(entry) {
                commentpair = !commentpair;
                htmlComments += renderComment(entry, commentpair);
                commentcount++;
            });

            var commentlist = '';

            commentlist = '<ul class="list-group">';
            commentlist += htmlComments;
            commentlist = commentlist + '</ul>';

            var commentLinkText = 'Aucuns commentaires';
            if(commentcount == 1)
            {
              commentLinkText = '1 commentaire';
            }
            else if (commentcount > 1) {
              commentLinkText = commentcount + ' commentaires';
            }
            var postLikes = 0;
            var displayname = author.infos.username;
            if(author.infos.hasOwnProperty('displayname'))
            {
                displayname = author.infos.displayname;
            }
            var postVisibilityStr = '';
            var postContent = '';
            var cryptedGlyph = '';
            switch(jsonPost['visibility'])
            {
                case 0:
                    postVisibilityStr = '<span class="glyphicon glyphicon-lock" aria-hidden="true"></span> Moi uniquement';
                    cryptedGlyph = '<div style="float: right;margin: 15px 0 0 75px;font-size:12px;color:grey;">Message crypté <span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>';
                    postContent = decryptMessage(jsonPost['content']);
                    break;
                case 1:
                    postVisibilityStr = '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> Amis';
                    cryptedGlyph = '<div style="float: right;margin: 15px 0 0 75px;font-size:12px;color:grey;">Message chiffré <span style="color: green;" class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>';
                    postContent = decryptMessage(jsonPost['content']);
                    break;
                case 2:
                    postVisibilityStr = '<span class="glyphicon glyphicon-globe" aria-hidden="true"></span> Tout le monde';
                    break;
            }
            if(postContent == '')
            {
                postContent = jsonPost['content'];
            }
            var d = new Date();
            htmlPost = '<div style="box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">'+
              '<div class="panel-body" style="border-radius: 5px;">'+
                '<div class="media">'+
                  '<div style="text-align:center;" class="media-left">'+
                    '<a href="javascript:void(0)" onclick="showIdentity(\''+jsonPost['author']['$id']+'\');">'+
                      '<img class="media-object img-rounded" style="width: 64px; height: 64px;" src="avatars/'+jsonPost['author']['$id']+ '?' + d.getTime() + '" alt="...">'+
                    '</a>'+
                    '<a href="javascript:void(0)" onclick="showIdentity(\''+jsonPost['author']['$id']+'\');">'+displayname+'</a>'+
                  '</div>'+
                  '<div class="media-body">'+
                  findURL(postContent) + ' ' +
                  '</div>' + authorActionButton +
                  '<div style="float: left;margin: 10px 0 0 75px;">'+ pLikes + ' <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> - <span class="p-date" style="font-size: 12px;color:grey;">Publié ' + postedTimeStr + '</span> - <span style="font-size: 12px;color:grey;">visibilité : ' + postVisibilityStr + '</span></div>'+
                  cryptedGlyph+
                '</div>'+
              '</div>'+
              '<div class="panel-footer" style="border-bottom-right-radius:5px;border-bottom-left-radius:5px;">'+
                '<a role="button" data-toggle="collapse" href="#comments-scroll-'+jsonPost['_id']['$id']+'" aria-expanded="false" aria-controls="comments-scroll-'+jsonPost['_id']['$id']+'">' + commentLinkText +'</a> - ' + postLikeLink +
              '</div>'+
              '<div class="collapse" id="comments-scroll-'+jsonPost['_id']['$id']+'">'+
                commentlist +
                '<div style="border: none; margin-bottom: 0px;box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.2);" class="panel panel-default">'+
                  '<textarea id="newcomment-content-'+jsonPost['_id']['$id']+'" style="resize:vertical;border-radius:0px;" class="form-control" rows="3" placeholder="Inserez votre commentaire..."></textarea>'+
                  '<div class="panel-footer" style="border-bottom-right-radius: 5px;border-bottom-left-radius: 5px;">'+
                    '<button style="font-weight: bold;font-size: 12px;" class="button-send-newcomment btn btn-info" value="'+jsonPost['_id']['$id']+'" class="btn btn-info">Publier <span class="glyphicon glyphicon-send"></span></button>'+
                  '</div>'+
                '</div>'+
              '</div>'+
            '</div>';
        }
    });
    return htmlPost;
}

function addFriend(isAccepting){
  if(!friendRequestSent){
    var userid = $("#identity-id").val();
    var data = { 'userid' : userid };
    $.ajax({
      type: "POST",
      url: "webservice/friends/add",
      contentType: 'application/json',
      data: JSON.stringify(data),
      dataType: "json",
      processData: false,
      complete: function() {
        /*if(isAccepting)
        {
          converse.user.logout();
          setTimeout(function() {
                refreshIdentityPage();
          }, 1000);
        }
        else{*/
          refreshIdentityPage();
        //}
      }
    });
  }
  friendRequestSent = true;
}

function updateNotifications()
{
  $.ajax({
    type: "GET",
    url: "webservice/notifications",
    complete: function(response) {
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
            notifIdsUnread[notifCountUnread-1] = entry['_id']['$id'];
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
          htmlNotifications = '<a tabindex="0" class="btn" role="button" data-toggle="popover" style="width: 250px;" data-content="'+
                htmlEntities('<p style="text-align: center;"><a href="javascript:void(0)" onclick="setAllNotifRead();">Tout marquer comme lu</a></p>'+notifContent)+
                '"><span class="glyphicon glyphicon-bell" aria-hidden="true"></span> Notifications <span class="badge">'+notifCountUnread+'</span></a>';
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
      return '<div style="display: inline-block;width: 100%;margin-bottom: -6px;" class="list-group-item"><div style="width: 90%;float: left;">'+jsonNotif['content']+'<p style="margin: 0px 0px 0px 0px;font-size: 12px;color:grey;">'+postedTimeStr+'</p></div><div><a style="float:right;" href="javascript:void(0)" onclick="setNotifRead(\''+jsonNotif['_id']['$id']+'\')"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a></div></div>';
    }
}

function setAllNotifRead(){
  notifIdsUnread.forEach(function(entry) {
    $.ajax({
        async: false,
        type: "POST",
        url: "webservice/notifications/"+entry,
        complete: function(response) {

        }
      });
  });
  updateNotifications();
}

function setNotifRead(notifId){
  $.ajax({
    type: "POST",
    url: "webservice/notifications/"+notifId,
    complete: function(response) {
      updateNotifications();
    }
  });
}

function showMyFriendsPanel()
{
    $.ajax({
        type: "GET",
        url: "webservice/users/current",
        complete: function(response) {
            var htmlUserList = '';
            var me = JSON.parse(response.responseText);
            var myFriends = me['friends'];
            var friendCount = 0;
            for(var k in myFriends) {
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "webservice/users/"+k,
                    complete: function(response) {
                        var friend = JSON.parse(response.responseText);
                        var displayname = friend.infos.username;
                        if(friend.infos.hasOwnProperty('displayname'))
                        {
                            displayname = friend.infos.displayname +' ('+friend.infos.username+')';
                        }
                        if(myFriends[k])
                        {
                            friendCount++;
                        }
                        var d = new Date();
                        htmlUserList += '<div style="padding-right: 5px;padding-left: 5px;" class="col-lg-3 col-sm-4 col-xs-5">'+
                          '<a href="identity.php?userid='+k+'">'+
                            '<img data-toggle="tooltip" data-placement="top" data-original-title="'+displayname+'" style="margin-bottom: 0px;" src="avatars/'+k+'?'+d.getTime()+'" class="thumbnail img-responsive">'+
                          '</a>'+
                        '</div>';
                    }
                });
            }
            if(friendCount == 0){
                htmlUserList = '<p style="margin: 15px 15px 10px;">:( Vous n\'avez pas encore d\'amis...</p>';
            }
            $('#my-user-panel').html(htmlUserList);
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
}

function showHisFriendsPanel(userid)
{
    $.ajax({
        type: "GET",
        url: "webservice/users/"+userid,
        complete: function(response) {
            var htmlUserList = '';
            var me = JSON.parse(response.responseText);
            var myFriends = me['friends'];
            console.log(myFriends);
            var friendCount = 0;
            for(var k in myFriends) {
                $.ajax({
                    async: false,
                    type: "GET",
                    url: "webservice/users/"+k,
                    complete: function(response) {
                        var friend = JSON.parse(response.responseText);
                        var displayname = friend.infos.username;
                        if(friend.infos.hasOwnProperty('displayname'))
                        {
                            displayname = friend.infos.displayname +' ('+friend.infos.username+')';
                        }
                        if(myFriends[k])
                        {
                            friendCount++;
                        }
                        var d = new Date();
                        htmlUserList += '<div style="padding-right: 5px;padding-left: 5px;" class="col-lg-3 col-sm-4 col-xs-5">'+
                          '<a href="identity.php?userid='+k+'">'+
                            '<img data-toggle="tooltip" data-placement="top" data-original-title="'+displayname+'" style="margin-bottom: 0px;" src="avatars/'+k+'?'+d.getTime()+'" class="thumbnail img-responsive">'+
                          '</a>'+
                        '</div>';
                    }
                });
            }
            if(friendCount == 0){
                htmlUserList = '<p style="margin: 15px 15px 10px;">:( Aucun ami à afficher...</p>';
            }
            $('#his-user-panel').html(htmlUserList);
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
}

function loadIdentity()
{
    var userid = $("#identity-id").val();
    $.ajax({
        type: "GET",
        url: "webservice/users/"+userid,
        complete: function(response) {
            var user = JSON.parse(response.responseText);
            var avatar_path = "avatars/"+userid;
            $("#identity-avatar").attr("src", function() {
                var d = new Date();
                return "avatars/" + userid + "?" + d.getTime();
            });
            var displayname = user.infos.username;
            if(user.infos.hasOwnProperty('displayname'))
            {
                displayname = user.infos.displayname +' ('+user.infos.username+')';
            }
            $("#identity-name").text(displayname);
            if(user.hasOwnProperty('private_key'))
            {
                $("#identity-actions").remove();
            }
            else{
                $.ajax({
                    type: "GET",
                    url: "webservice/friends/status/"+userid,
                    complete: function(response) {
                        var json = JSON.parse(response.responseText);
                        if(json.friendStatus == 'asked')
                        {
                            $("#identity-actions").prepend('<span class="label label-info">Demande d\'ami envoyée</span><br/>');
                        }
                        else if(json.friendStatus == 'acceptation')
                        {
                            $("#identity-actions").prepend('<span class="label label-warning">Attends votre acceptation</span><br/>');
                            $("#identity-actions-buttons").append('<button onclick="addFriend(true);" class="btn btn-default" type="button"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span> Accepter</button>');
                        }
                        else if(json.friendStatus == 'friend')
                        {
                            $("#identity-actions").prepend('<span class="label label-success">Ami</span><br/>');
                        }
                        else
                        {
                            $("#identity-actions-buttons").append('<button onclick="addFriend(false);" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Ajouter</button>');
                        }
                    }
                });
            }
        }
    });
    loadIdentityPosts(userid);
    showHisFriendsPanel(userid);
}

function loadAccountSettings()
{
    $.ajax({
        type: "GET",
        url: "webservice/users/current",
        complete: function(response) {
            var me = JSON.parse(response.responseText);
            linkedToFacebook = me["fb-link"];
            if(linkedToFacebook)
            {
              $("#revocateFBDiv").html('<a href="javascript: void(0);" data-placement="right" data-toggle="popover" title="Qu\'est ce que c\'est ?" data-content="En cliquant sur ce bouton vous allez supprimer la possibilité de vous connecter à VirtualID avec votre compte Facebook."><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> <button id="revocateFB" type="button" class="btn btn-danger">Révoquer le lien Facebook</button>');
            }
            var meHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option3" autocomplete="off" checked> Moi uniquement</label>';
            var frHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option2" autocomplete="off" checked> Mes amis</label>';
            var tlmHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde</label>';
            //Email settings
            switch(me.privacy_settings.email)
            {
                case 0:
                    meHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option3" autocomplete="off" checked> Moi uniquement</label>';
                    break;
                case 1:
                    frHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option2" autocomplete="off" checked> Mes amis</label>';
                    break;
                case 2:
                    tlmHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde</label>';
                    break;
            }
            $("#emailPrivacySettings").html(tlmHtml+frHtml+meHtml);
            //Friends list settings
            meHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option3" autocomplete="off" checked> Moi uniquement</label>';
            frHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option2" autocomplete="off" checked> Mes amis</label>';
            tlmHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde</label>';
            switch(me.privacy_settings.friends)
            {
                case 0:
                    meHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option3" autocomplete="off" checked> Moi uniquement</label>';
                    break;
                case 1:
                    frHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option2" autocomplete="off" checked> Mes amis</label>';
                    break;
                case 2:
                    tlmHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde</label>';
                    break;
            }
            $("#friendsPrivacySettings").html(tlmHtml+frHtml+meHtml);
            //Friends list settings
            meHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option3" autocomplete="off" checked> Moi uniquement</label>';
            frHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option2" autocomplete="off" checked> Mes amis</label>';
            tlmHtml = '<label class="btn btn-primary"><input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde</label>';
            switch(me.privacy_settings.displayname)
            {
                case 0:
                    meHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option3" autocomplete="off" checked> Moi uniquement</label>';
                    break;
                case 1:
                    frHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option2" autocomplete="off" checked> Mes amis</label>';
                    break;
                case 2:
                    tlmHtml = '<label class="btn btn-primary active"><input type="radio" name="options" id="option1" autocomplete="off" checked> Tout le monde</label>';
                    break;
            }
            $("#displaynamePrivacySettings").html(tlmHtml+frHtml+meHtml);

            $("#username").text(me.infos.username);
            $("#displayname").val(me.infos.displayname);
            $("#email").val(me.infos.email);
            $("#publicKey").val(me.public_key);
            if(linkedToFacebook)
            {
              $("#fblinked").html("Activé");
            }
            else
            {
              $("#fblinked").html("Désactivé");
            }
        }
    });
}

function saveInfosSettings()
{
    var displayname = $("#displayname").val();
    var email = $("#email").val();
    var jsonUser = { 'displayname':displayname, 'email':email };
    $.ajax({
        url: "webservice/users/update",
        type: "POST",
        contentType: 'application/json',
        data: JSON.stringify(jsonUser),
        dataType: "json",
        processData: false,
        success: function(response) {
            loadAcountSettings();
            $("#saveAccountInfosBtn").html('Valider <span style="color: green;" class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
        }
    });
}

var __urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
var __imgRegex = /\.(?:jpe?g|gif|png)$/i;

function findURL(text){

    var exp = __urlRegex;
    return text.replace(exp,function(match){
            __imgRegex.lastIndex=0;
            if(__imgRegex.test(match)){
                return '<img src="'+match+'" width="250" />';
            }
            else{
                return '<a href="'+match+'" target="_blank">'+match+'</a>';
            }
        }
    );
}

//This little script will draw two curved arrows into a canvas, the CSS3 will make it rotating.
//It's very verbose because of learning purpose. Feel free to share it!
//Author: Edoardo Odorico and some help from Baldarn (2013)  - Licensed Under Creative Commons CC-BY-SA


function showLoadingAnimation(distanceArrows, arrowStrength){

    //From here I will call two functions, for body and for triangle, twice.

    //But first of all let's calculate the angle of the arc (for arrow body)
    var lengthArrow = 1 - distanceArrows;
    var startAngle = 0;
    var endAngle = lengthArrow ;

    //draw it!
    drawArrowBody( startAngle * pi, endAngle * pi, arrowStrength );

    //and now draw the triangle:
    drawTriangle( endAngle * pi );

    //math for the other arrow and...
    startAngle = endAngle + distanceArrows;
    endAngle = startAngle + lengthArrow ;

    //...draw them!
    drawArrowBody( startAngle * pi, endAngle * pi, arrowStrength );
    drawTriangle( endAngle * pi);

}
function drawArrowBody( startAngle, endAngle, arrowStrength ){
    //In this function we draw the body of the arrow, which is just an arc

	var counterClockwise = false;

	context.beginPath();
    //draw it!
	context.arc( x, y, radius, startAngle, endAngle, counterClockwise );
    //stroke it!
	context.lineWidth = arrowStrength;
	context.strokeStyle = colorBody;
	context.stroke();
	context.closePath();


}
function drawTriangle(endAngle){

    //The bloody part: draw the triangle.
    //A lot of old trigos tricks:

    //First the center of the triangle base (where we start to draw the triangle)
	var canterBaseArrowX = x + radius * Math.cos( endAngle );
	var canterBaseArrowY = y + radius * Math.sin( endAngle );

	context.beginPath();

    //We move to the center of the base
	context.moveTo( canterBaseArrowX, canterBaseArrowY );

    //Let's calculate the first point, easy!
	var ax = canterBaseArrowX + (triangleSide / 2 ) * Math.cos( endAngle );
	var ay = canterBaseArrowY + (triangleSide / 2 ) * Math.sin( endAngle );
	context.lineTo ( ax, ay );

    //Now time to get mad: the farest triangle point from the arrow body
	var bx = canterBaseArrowX + ( Math.sqrt( 3 ) / 2 ) * triangleSide * ( Math.sin( -endAngle ));
	var by = canterBaseArrowY + ( Math.sqrt( 3 ) / 2 ) * triangleSide * ( Math.cos( -endAngle ));
	context.lineTo(bx,by);

    //Easy , like the a point
	var cx = canterBaseArrowX - ( triangleSide / 2 ) * Math.cos( endAngle );
	var cy = canterBaseArrowY - ( triangleSide / 2 ) * Math.sin( endAngle );
	context.lineTo( cx,cy );

    //and back to the origin, the center of the triangle base.
	context.lineTo( canterBaseArrowX, canterBaseArrowY );

    context.lineWidth = arrowStrength;

    //Stroke it with color!
    context.strokeStyle = colorTriangle;
	context.stroke();
	context.closePath();
}
