# Photos  :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs

* Feed shows scale photos when sharing feed on friend's wall.
* Cannot view photo in case uploaded file has more than one extension.
* Sponsor in feed works wrong.
* Issue when load more photos many times.

### Improvements

* Support search photo albums in global search.
* Improve layout of all pages and blocks.
* Add Block Setting to limit the photos in Recent block.
* Support drag/drop, preview, progress bar when users upload photos.
* Support admin can change default photo for photo albums.
* Validate all settings, user group settings, and block settings.

### Changed Files

- Ajax/Ajax.php
- Block/Album.php
- Block/AlbumTag.php
- Block/Category.php
- Block/Detail.php
- Block/Featured.php
- Block/MyPhoto.php
- Block/Profile.php
- Block/Share.php
- Block/Sponsored.php
- Controller/AddController.php
- Controller/Admin/CategoryController.php
- Controller/AlbumController.php
- Controller/AlbumsController.php
- Controller/FrameController.php
- Controller/FrameDragDropController.php
- Controller/FrameFeedDragDropController.php
- Controller/IndexController.php
- Controller/ViewController.php
- Install.php
- Installation/Version/v453.php
- Installation/Version/v460.php
- README.md
- Service/Album/Album.php
- Service/Album/Browse.php
- Service/Album/Process.php
- Service/Api.php
- Service/Browse.php
- Service/Callback.php
- Service/Category/Category.php
- Service/Category/Process.php
- Service/Photo.php
- Service/Process.php
- Service/Tag/Process.php
- Service/Tag/Tag.php
- assets/autoload.css
- assets/autoload.js
- assets/autoload.less
- assets/dropzone/dropzone.css
- assets/dropzone/dropzone.js
- assets/images/nocover.jpg
- assets/images/nocover.png
- assets/main.less
- change-log.md
- hooks/bundle__start.php
- hooks/validator.admincp_user_settings_photo.php
- phrase.json
- start.php
- views/block/album-tag.html.php
- views/block/album_entry.html.php
- views/block/featured.html.php
- views/block/form-album.html.php
- views/block/form.html.php
- views/block/mass-edit-item.html.php
- views/block/menu-album.html.php
- views/block/menu.html.php
- views/block/my-photo.html.php
- views/block/photo_entry.html.php
- views/block/share.html.php
- views/block/sponsored.html.php
- views/block/stream.html.php
- views/controller/add.html.php
- views/controller/admincp/add.html.php
- views/controller/album.html.php
- views/controller/albums.html.php
- views/controller/edit-album.html.php
- views/controller/index.html.php
- views/controller/view.html.php


### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | --- |
| 1 | display_timeline_photo_within_gallery | Display User Timeline Photos within Gallery | Allow admin to decide show/hide uploaded photos from feed in Photos listing page |

### Deprecated Settings

| ID | Var name | Name | Reason | Removed In |
| --- | -------- | ---- | --- | ---- |
| 1 | photo_pic_sizes | Photo Pic Sizes | Don't use anymore | 4.7.0 |

## Version 4.5.3

### Information

- **Release Date:** September 22th, 2017
- **Best Compatibility:** phpFox >= 4.5.3

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | photo_image_details_time_stamp | Image Details Time Stamps | Don't use anymore |
| 2 | html5_upload_photo | HTML5 Mass Upload | Don't use anymore |
| 3 | can_add_tags_on_photos | Can add tags on photos? | Don't use anymore |
| 4 | can_edit_photo_categories | Can edit public photo categories? | Don't use anymore |
| 5 | can_add_public_categories | Can add public photo categories? | Don't use anymore |
| 6 | total_photo_display_profile | Define how many photos to display within an album on a users profile. | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | photo_paging_mode | Pagination Style | Select Pagination Style at Search Page. |
| 2 | display_cover_photo_within_gallery | Display User Cover Photos within Gallery | Disable this feature if you do not want to display user cover photos within the photo gallery. |
| 3 | display_photo_album_created_in_group | Display photos/albums which created in Group to the Photo app | Enable to display all public photos/albums to the both Photos/Albums page in group detail and in Photo app. Disable to display photos/albums created by an users to the both Photos/Albums page in group detail and My Photos/Albums page of this user in Photo app and nobody can see these photos/albums in Photo app but owner. Notice: This setting will be applied for all types of groups, include secret groups. |
| 3 | display_photo_album_created_in_page | Display photos/albums which created in Page to the Photo app | Enable to display all public photos/albums to the both Photos/Albums page in page detail and in Photo app. Disable to display photos/albums created by an users to the both Photos/Albums page in page detail and My Photos/Albums page of this user in Photo app and nobody can see these photos/albums in Photo app but owner. |
| 4 | can_post_on_albums | Can post comments on albums? | Can post comments on albums? |

### Deprecated Functions

| ID | Class Name | Function Name | Will Remove In | Reason |
| --- | -------- | ---- | ---- | ---- |
| 1 | Apps\Core_Photos\Ajax\Ajax | browse | 4.6.0 | Don't use anymore |
| 2 | Apps\Core_Photos\Ajax\Ajax | browseUserAlbum | 4.6.0 | Don't use anymore |
| 3 | Apps\Core_Photos\Ajax\Ajax | browseAlbum | 4.6.0 | Don't use anymore |
| 4 | Apps\Core_Photos\Ajax\Ajax | browseUserPhotos | 4.6.0 | Don't use anymore |
| 5 | Apps\Core_Photos\Ajax\Ajax | categoryOrdering | 4.6.0 | Don't use anymore |
| 6 | Apps\Core_Photos\Service\Callback | deleteGroup | 4.6.0 | Don't use anymore |
| 7 | Apps\Core_Photos\Service\Photo | _getPhoto | 4.6.0 | Don't use anymore |
| 8 | Apps\Core_Photos\Service\Photo | getPreviousPhotos | 4.6.0 | Don't use anymore |
| 9 | Apps\Core_Photos\Service\Photo | getNextPhotos | 4.6.0 | Don't use anymore |
| 10 | Apps\Core_Photos\Service\Photo | getPhotoStream | 4.6.0 | Don't use anymore |
| 11 | Apps\Core_Photos\Service\Photo | getInfoForAction | 4.6.0 | Don't use anymore |




