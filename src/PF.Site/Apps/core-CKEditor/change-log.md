# CKEditor :: Change Log

## Version 4.2.0

### Information

- **Release Date:** January 9th, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Bugs Fixed

- Bullets and numbers aren't aligned.
- Emoji's are always inserted at the beginning in CKEditor.

### Improvements

- Enable setting `Allow HTML` when install app.
- Allow admin can chose CKEditor package to use on their site.
- Check compatible with phpFox core and porting apps 4.6.0.

### Changed Files
- M	Install.php
- M	README.md
- M	assets/autoload.css
- M	assets/autoload.js
- A	assets/autoload.less
- M	assets/ckeditor/*
- A	assets/ckeditor_basic/*
- A	assets/ckeditor_full/*
- M	change-log.md
- A	hooks/bundle__start.php
- M	hooks/forum.component_ajax_reply.php
- M	hooks/get_editor_end.php
- M	hooks/set_editor_end.php
- A	installer.php
- A	uninstall.php
- M	views/admincp.html

## Version 4.1.4

### Information

- **Release Date:** August 29, 2017
- **Best Compatibility:** phpFox >= 4.5.2

### Bugs Fixed

- Does not load CKeditor when edit an event.
- Support other modules/apps.

### Changed files

- core-CKEditor/assets/autoload.js
- core-CKEditor/hooks/get_editor_end.php

## Version 4.1.3

### Information

- **Release Date:** April 17, 2017
- **Best Compatibility:** phpFox >= 4.5.2

### Bugs Fixed

- Cannot use CKEditor if bundle JS is enabled
- The cursor always focuses on top when users open Emoji cheat sheet

### Changed files

- PF.Site/Apps/core-CKEditor/Install.php
- PF.Site/Apps/core-CKEditor/hooks/get_editor_end.php
- PF.Site/Apps/core-CKEditor/hooks/template_getheader_exclude_bundle_js.php
