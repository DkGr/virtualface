# Virtual iD

Virtual iD is a project prototype to create an open-source and encrypted social network with no personal datas violation. Encryption/decryption features are powered by OpenPGP.js.

## Requirements
- Node.js with npm
- MongoDB server

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
- Comments system
- Likes system

## TODO
- Instant Messaging
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
git nodejs npm
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


### MongoDB installation
Run this command :
```
# npm install -g mongodb
```

### Facebook integration
You need to create a Facebook application to get a Facebook API key linked to your domain name

### Setup VirtualID
Now you can go to https://youradress.com/setup and follow the wizard to complete the Virtual iD installation.
