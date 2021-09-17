# Core Pages  :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 9th, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Improvements

- Allow users can re-claim after admin deny their claim.
- Support drag/drop, preview, progress bar when users upload photos.
- Validate all settings, user group settings, and block settings.
- Improve layout of all pages and blocks.

### New Features

- Add `Members` tab in pages profile page.

### New User Group Settings

| ID | Var name  | Description |
| --- | -------- | ----------- |
| 1 | pages.flood_control | Define how many minutes this user group should wait before they can add new group. Note: Setting it to "0" (without quotes) is default and users will not have to wait. |

### Changed files
- M Ajax/Ajax.php
- M Block/AddPage.php
- M Block/Admin.php
- M Block/Category.php
- M Block/DeleteCategory.php
- M Block/Like.php
- M Block/Menu.php
- M Block/Pending.php
- M Block/PeopleAlsoLike.php
- M Block/Photo.php
- M Block/Profile.php
- D Block/ProfilePhoto.php
- A Block/SearchMember.php
- M Controller/AddController.php
- M Controller/Admin/AddCategoryController.php
- M Controller/Admin/IndexController.php
- M Controller/Admin/IntegrateController.php
- M Controller/FrameController.php
- M Controller/IndexController.php
- A Controller/MembersController.php
- M Controller/PhotoController.php
- M Controller/ViewController.php
- M Install.php
- M Installation/Version/v453.php
- A Installation/Version/v460.php
- A Job/GenerateMissingThumbnails.php
- M README.md
- M Service/Browse.php
- M Service/Callback.php
- M Service/Category.php
- M Service/Pages.php
- M Service/Process.php
- M Service/Type.php
- M assets/autoload.css
- M assets/autoload.js
- M assets/autoload.less
- A assets/img/default-category/default_category.png
- A assets/img/default_pagecover.png
- M assets/main.less
- M change-log.md
- A hooks/bundle__start.php
- M hooks/comment.service_comment_massmail__1.php
- A hooks/core.template_block_upload_form_action_1.php
- M hooks/friend.component_block_search_get.php
- M hooks/get_module_blocks.php
- A hooks/job_queue_init.php
- A hooks/mail.component_ajax_compose_process_success.php
- M hooks/photo.component_ajax_process_done.php
- A hooks/photo.service_process_make_profile_picture__end.php
- M hooks/run.php
- A hooks/validator.admincp_settings_pages.php
- A hooks/validator.admincp_user_settings_pages.php
- M installer.php
- M phrase.json
- M start.php
- M views/block/add-page.html.php
- M views/block/admin.html.php
- M views/block/cropme.html.php
- M views/block/delete-category.html.php
- M views/block/joinpage.html.php
- M views/block/like.html.php
- M views/block/link-listing.html.php
- M views/block/link.html.php
- M views/block/login.html.php
- M views/block/menu.html.php
- M views/block/pending.html.php
- M views/block/people-also-like.html.php
- M views/block/photo.html.php
- D views/block/profile-photo.html.php
- M views/block/profile.html.php
- A views/block/search-member.html.php
- M views/controller/add.html.php
- M views/controller/admincp/add.html.php
- M views/controller/admincp/claim.html.php
- M views/controller/admincp/index.html.php
- M views/controller/all.html.php
- M views/controller/index.html.php
- A views/controller/members.html.php
- M views/controller/view.html.php
- M views/controller/widget.html.php

## Version 4.5.3

### Information

- **Release Date:** September 21st, 2017
- **Best Compatibility:** phpFox >= 4.5.3

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | pages.show_page_admins | Show Page Admins | Move to setting of block Admin |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | pages.pages_limit_per_category | Pages Limit Per Category | Define the limit of how many pages per category can be displayed when viewing All Pages page. |
| 2 | pages.pagination_at_search_page | Paging Type |  |
| 3 | pages.display_pages_profile_photo_within_gallery | Display pages profile photo within gallery | Disable this feature if you do not want to display pages profile photos within the photo gallery. |
| 4 | pages.display_pages_profile_photo_within_gallery | Display pages profile photo within gallery | Disable this feature if you do not want to display pages profile photos within the photo gallery. |
| 5 | pages.display_pages_cover_photo_within_gallery | Display pages cover photo within gallery | Disable this feature if you do not want to display pages cover photos within the photo gallery. |
| 6 | pages_meta_description | Pages Meta Description | Meta description added to pages related to the Pages app. |
| 7 | pages_meta_keywords | Pages Meta Keywords | Meta keywords that will be displayed on sections related to the Pages app. |

### Removed User Group Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | pages.can_moderate_pages | Can moderate pages? This will allow a user to edit/delete/approve pages added by other users. | Don't use anymore, split to 2 new user group settings "Can edit all pages?", "Can delete all pages?" |

### New User Group Settings

| ID | Var name | Name |
| --- | -------- | ---- |
| 1 | pages.can_edit_all_pages | Can edit all pages? |
| 2 | pages.can_delete_all_pages | Can delete all pages? |

### Deprecated Functions

| ID | Class Name | Function Name | Will Remove In | Reason |
| --- | -------- | ---- | ---- | ---- |
| 1 | Ajax | signup | 4.6.0 | Don't use anymore |
| 2 | Callback | getNotificationJoined | 4.6.0 | Don't use anymore |
| 3 | Callback | getNotificationRegister | 4.6.0 | Don't use anymore |
