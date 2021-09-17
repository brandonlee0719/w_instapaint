# RESTful API :: Change Log

## Version 4.1.3

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Improvements

- Check compatible with phpFox core 4.6.0.
- Support renew refresh token.

### Changed Files
- M Install.php
- A README.md
- M Service/RestApiTransport.php
- M change-log.md
- M composer.lock
- A hooks/bundle__start.php
- M start.php
- M vendor/bshaffer/*
- M vendor/composer/LICENSE
- M vendor/composer/autoload_real.php
- M vendor/composer/installed.json
- M views/admincp.html
- M views/admincp_client.html

## Version 4.1.2

### Information

- **Release Date:** April 11, 2017
- **Best Compatibility:** phpFox >= 4.5.2

### Fixed Bugs

- Always requires param "state" when request Authorization Code
- Grant type "refresh_token" isn't supported

### Improvements

- Support get information of current logged user
- Support attach files/photos to blog content

### Changed Files

- PF.Site/Apps/core-restful-api/Install.php
- PF.Site/Apps/core-restful-api/Service/Storage.php
- PF.Site/Apps/core-restful-api/start.php
