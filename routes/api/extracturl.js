var express = require('express');
var path = require('path');
var passport = require('passport');
var og = require('open-graph');
var router = express();

var config = require('../../config/config');

var extractDomain = function (url) {
    var domain;
    //find & remove protocol (http, ftp, etc.) and get domain
    if (url.indexOf("://") > -1) {
        domain = url.split('/')[2];
    }
    else {
        domain = url.split('/')[0];
    }

    //find & remove port number
    domain = domain.split(':')[0];

    return domain;
};

router.get('/', function(req, res) {
  var externalUrl = req.query.url;
  og(externalUrl, function(err, meta){
    var titleToSend = externalUrl;
    var descToSend = "Description unavailable."
    var domainToSend = extractDomain(externalUrl);
    if(meta.title){
      titleToSend = meta.title;
    }
    if(meta.description){
      descToSend = meta.description;
    }
    
    if(meta.site_name){
      domainToSend = meta.site_name;
    }
    
    var jsonResp = { 
      title: titleToSend, 
      desc: descToSend, 
      url: externalUrl, 
      domain: domainToSend
    };
    if(meta.image){
      jsonResp.image = meta.image.url;
    }
    
    res.json(jsonResp);   
  });
});

module.exports = router;
