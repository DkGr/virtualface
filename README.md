# VirtualID

VirtualID is a project prototype to create an open-source and encrypted social network with no personal datas violation. Encryption/decryption features are powered by OpenPGP.js.

## Requirements
- HTTP server with SSL/TLS
- Node.js with npm
- MongoDB server
- Openfire XMPP server with http-bind and CORS enabled for instant messaging

## Working features
- Basic account Creation
- Account creation with Facebook link
- Account information page
- Public stream post creation
- Stream posts visibility system
- Photo/Video link embeded into posts
- OpenPGP encryption/Decryption for posts
- Friendship system
- Notification system
- OpenPGP keys generation at account creation
- User search
- XMPP chat
- Comments system
- Likes system

## TODO
- Privacy settings
- Encryption settings
- Email system
- OpenPGP encryption/Decryption for emails
- Photo/Video upload
- Localization system and translation
- Events system (creation, invites,...)
- Geolocation sharing

## Installation

### Required packages
```
git apache2 nodejs npm openjdk-7-jre
```

### Get VirtualID sources
Clone this repository where you want executing this command :
```
$ git clone https://github.com/DkGr/virtualid.git/
```

### Install dependencies
cd /your/virtualid/source/folder/
npm install

configure virtualid server by editing config/config.js :
nano config/config.js

### Apache SSL configuration
Enable SSL on apache2 with a certificate for your domain (Use magic [letsencrypt](https://letsencrypt.org/) to automate this !)


### Openfire installation and configuration
Download the latest openfire deb package on the [official website](http://www.igniterealtime.org/downloads/index.jsp) then install it :
```
# dpkg -i openfire_4.0.3_all.deb
```

Now go to openfire setup page and follow the wizard http://youradresss.com:9090/

After setup finnished connect to the admin interface.

Go to plugin manager tab and install REST API plugin.

Now go to server settings->Rest API and enable it.

Switch to secret key auth and take note of the key.

Now go to SSL/TLS settings and import your apache certificate and private key into the keystore (Very easy with a [letsencrypt](https://letsencrypt.org/) generated)

### MongoDB installation
Run this command :
```
# npm install -g mongodb
```

### Facebook integration
You need to create a Facebook application to get a Facebook API key linked to your domain name

### Setup VirtualID
Now you can go to https://youradress.com/setup.php and follow the wizard to complete the VirtualID installation.
