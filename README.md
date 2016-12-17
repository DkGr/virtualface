# Virtual iD

Virtual iD is a project prototype to create an open-source and encrypted social network with no personal data violations. Uploaded files (photos, videos,...) are stored into a private ownCloud space syncable with your computers, tablets and smartphones. Encryption/decryption features are powered by [OpenPGP.js](https://github.com/openpgpjs/openpgpjs). File storage and sync are powered by [ownCloud](https://owncloud.org).

## Requirements
- Node.js with npm
- A server certificate and private key to enable HTTPS (use easy and magical [let's encrypt](https://letsencrypt.org) !)
- A MongoDB server
- (not required for now) An ownCloud server

## Working features
- Basic account Creation
- Account creation with Facebook link
- Public stream post creation
- Photo/Video link embeded into posts
- OpenPGP keys generation at account creation
- User search
- Comment system
- Like system

## TODO
- Notification system
- Friendship system
- Instant Messaging
- Stream posts visibility system
- OpenPGP encryption/Decryption for posts
- Photo/Video upload into ownCloud server
- Account information page
- Privacy settings
- Encryption settings
- Email system
- OpenPGP encryption/Decryption for emails
- Localization system and translation
- Events system (creation, invites,...)
- Personal uploaded files sync (webdav) provided by ownCloud
- Personal Virtual iD Calendar sync (caldav) provided by ownCloud
- Virtual iD Contacts sync (carddav) provided by ownCloud
- Geolocation sharing

## Installation

### Required packages
```
git nodejs npm
```

### MongoDB installation
Run this command :
```
# npm install -g mongodb
```

### Get Virtual iD sources
Clone this repository where you want executing this command :
```
$ git clone https://github.com/DkGr/virtualid.git/
```

### Install dependencies
```
$ cd /your/virtualid/source/folder/
```
```
$ npm install
```
configure virtualid server by editing config/config.js :
```
$ nano config/config.js
```

### (Optional) Facebook connect
You need to create a Facebook application to get a Facebook API key linked to your domain name.
Insert your Facebook API key into the config/config.js file.
