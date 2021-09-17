# Instant Messaging

## App Info

- `App name`: Instant Messaging
- `Version`: 4.6.0
- `Store link`: https://store.phpfox.com/product/1837/instant-messaging
- `Demo site`: https://v4.phpfox.com
- `Owner`: phpFox

## Installation Guide

Please follow below steps to install new phpfox IM app:

1. Give 777 permission for folder **PF.Site/Apps/core-im/**.

2. Install the IM app from the store.

3. Give 775 permission for files in folder **PF.Site/Apps/core-imassets/sounds/**.

```bash
sudo chmod -R 775 core-im/assets/sounds/
```

4. Follow the instruction [here](https://docs.phpfox.com/display/FOX4MAN/Server+Setup+for+IM+Module) to set up chat server.

5. Clear cache on your site.

Congratulation! You have completed the installation process.

## Import data from v3
After installing new app, you
 can import your data from v3 to our new version with 2 following ways:

First, go to AdminCP > Apps > Instant Messaging > Import Data From v3. In this view, you can see 2 ways to import.

### Import data directly

> To use this function, PhpRedis extension is required, you can find the installation guide [here](https://github.com/phpredis/phpredis).

1. Enter your redis info.
2. Click **Import**. Done.

### Import data manual

Following the instruction:

1. Export data to json file by clicking **Export JSON** button.
2. Upload exported json file to your chat server (nodejs server, the same folder with file import.js).
3. Run the following command in command line:

```bash
node import.js
```