# Instant Messaging :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs

- Cannot connect when client switch between http and https
- Setting "Node JS server" is hidden when upgrading IM app from 4.5.2 on phpFox 4.5.3.

### Improvements

- Check compatible with phpFox core 4.6.0.

### Changed Files
- M Controller/AdminManageSoundController.php
- M Install.php
- M README.md
- M assets/autoload.css
- M assets/autoload.js
- M assets/autoload.less
- D assets/dropzone.js
- M assets/im-libraries.min.js
- M change-log.md
- A hooks/bundle__start.php
- M hooks/set_editor_end.php
- M hooks/template_getheader_end.php
- A hooks/validator.admincp_settings_im.php
- M phrase.json
- M server/hooks/hosting.js
- D server/hooks/sample.js
- M server/index.js
- M start.php
- M views/controller/admincp/import-data-v3.html.php
- M views/controller/admincp/manage-sound.html.php

## Version 4.5.4

### Information

- **Release Date:** August 29, 2017
- **Best Compatibility:** phpFox >= 4.5.2

### Fixed Bugs
* Some layout issues.
* Issue when hosting expired.
* Show duplicate link when search message.
* Can reply banned user.

### Changed Files
1. assets/autoload.js
2. assets/autoload.less
3. assets/autoload.css
4. views/admincp.html
5. views/controller/admincp/manage-sound.html.php
6. Install.php
7. start.php

## Version 4.5.3

### Information

- **Release Date: June 26, 2017**
- **Best Compatibility:** phpFox >= 4.5.2

### Fixed Bugs
* Could not buy the IM hosting service at AdminCP.
* Cannot reply another conversation after a friend's account was deleted.

### New Files
1. Ajax/Ajax.php

### Changed Files
1. assets/autoload.js
2. assets/autoload.less
3. assets/autoload.css
4. hooks/mail.service_callback_getglobalnotifications.php
5. hooks/notification.component_ajax_update_1.php
6. hooks/template_getheader_end.php
7. server/hooks/hosting.js
8. server/config.js.new
9. server/index.js
10. views/admincp.html
11. Install.php
12. installer.php
13. phrase.json
14. start.php

## Version 4.5.2

### Information

- **Release Date: June 6, 2017** 
- **Best Compatibility:** phpFox >= 4.5.2