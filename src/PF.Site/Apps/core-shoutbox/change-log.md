# Shoutbox :: Change Log

## Version 4.2.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bug

- Cannot use the app in some servers (do not allow to run php file with permission 777).
- Cannot use the app in sites that upgraded from v3.

### Improvements

- Update new layout for Shoutbox block.
- Check compatible with phpFox core and porting apps 4.6.0.

### Changed files

- PF.Site/Apps/core-shoutbox/Install.php

## Version 4.1.3

### Information

- **Release Date:** September 18, 2017
- **Best Compatibility:** phpFox >= 4.5.2

### Improvements

- Integrate with the new Pages app (4.5.3).

### Changed files

- PF.Site/Apps/core-shoutbox/Install.php
- PF.Site/Apps/core-shoutbox/Block/Chat.php
- PF.Site/Apps/core-shoutbox/Service/Process.php

## Version 4.1.2

### Information

- **Release Date:** April 11, 2017
- **Best Compatibility:** phpFox >= 4.5.2

### Bugs Fixed

- Can not use Shoutbox if enabled bundle JS
- Some issues on layout with right to left languages

### Improvement

- Apply site timestamp format for Shoutbox message time

### Changed files

- PF.Site/Apps/core-shoutbox/Install.php
- PF.Site/Apps/core-shoutbox/assets/autoload.css
- PF.Site/Apps/core-shoutbox/assets/autoload.js
- PF.Site/Apps/core-shoutbox/assets/autoload.less
- PF.Site/Apps/core-shoutbox/hooks/template_getheader_exclude_bundle_js.php
- PF.Site/Apps/core-shoutbox/polling.php
