# Music :: Change Log

## Version 4.6.0

### Information

- **Release Date:** January 09, 2018
- **Best Compatibility:** phpFox >= 4.6.0

### Fixed Bugs

- Upload new songs: show unknown error when upload an over size file than the limit of server.
- Song privacy: user can play a privacy song in album.
- Some layout issues with right-to-left language.

### Improvements

- Support search albums in global search.
- Support CKEditor for description fields of songs/albums.
- Support drag/drop, preview, progress bar when users upload songs, photos.
- Allow admin can change default photos of songs and albums.
- Validate all settings, user group settings, and block settings.
- Improve layout for all pages and blocks.

### Changed Files

- Ajax/Ajax.php
- Block/AlbumRowsBlock.php
- Block/FeaturedAlbumBlock.php
- Block/FeaturedBlock.php
- Block/GenreBlock.php
- Block/ListBlock.php
- Block/NewAlbumBlock.php
- Block/RelatedAlbumBlock.php
- Block/SongBlock.php
- Block/SponsoredAlbumBlock.php
- Block/SponsoredSongBlock.php
- Block/SuggestionBlock.php
- Block/UploadBlock.php
- Controller/Admin/IndexController.php
- Controller/AlbumController.php
- Controller/Browse/AlbumController.php
- Controller/DownloadController.php
- Controller/FrameController.php
- Controller/IndexController.php
- Controller/UploadController.php
- Controller/ViewController.php
- Install.php
- Installation/Database/Music_Album.php
- Installation/Database/Music_Song.php
- Service/Album/Album.php
- Service/Album/Process.php
- Service/Callback.php
- Service/Genre/Genre.php
- Service/Genre/Process.php
- Service/Music.php
- Service/Process.php
- assets/autoload.css
- assets/autoload.js
- assets/image/music_v02.png
- assets/image/nophoto_song.png
- assets/image/song_detail_bg.png
- assets/jscript/mediaelementplayer/mediaelement-and-player.js
- assets/main.less
- hooks/bundle__start.php
- hooks/user.template_block_setting_form.php
- hooks/validator.admincp_user_settings_music.php
- phrase.json
- start.php
- views/block/album-rows.html.php
- views/block/featured-album.html.php
- views/block/featured.html.php
- views/block/genre.html.php
- views/block/list.html.php
- views/block/menu-album.html.php
- views/block/menu.html.php
- views/block/mini-album.html.php
- views/block/mini-entry.html.php
- views/block/mini-feed-entry.html.php
- views/block/mini.html.php
- views/block/new-album.html.php
- views/block/related-album.html.php
- views/block/rows.html.php
- views/block/song-genres.html.php
- views/block/song.html.php
- views/block/sponsored-album.html.php
- views/block/sponsored-song.html.php
- views/block/suggestion.html.php
- views/block/track-entry.html.php
- views/block/track.html.php
- views/block/upload-photo.html.php
- views/block/upload.html.php
- views/controller/admincp/add.html.php
- views/controller/album.html.php
- views/controller/browse/album.html.php
- views/controller/index.html
- views/controller/index.html.php
- views/controller/upload.html.php
- views/controller/view-album.html.php
- views/controller/view.html.php

### Removed Blocks

| ID | Block | Reason |
| --- | -------- | ---- |
| 1 | music.genre | Don't use anymore |

### New User Group Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | music.can_download_songs | Can download songs? | Admin can control which user groups have permission to down load songs |


## Version 4.5.3p1

### Information

- **Release Date:** September 29, 2017
- **Best Compatibility:** phpFox >= 4.5.3

### Fixed Bugs

- Process bar is not working when upload songs
- Doesn't show item actions in listing page if the viewer doesn't have mass permission

### Changed Files

- assets/autoload.js
- assets/main.less

## Version 4.5.3

### Information

- **Release Date:** September 19, 2017
- **Best Compatibility:** phpFox >= 4.5.3

### Removed Settings

| ID | Var name | Name | Reason |
| --- | -------- | ---- | --- |
| 1 | music_enable_mass_uploader | Enable mass uploader | Don't use anymore |
| 2 | sponsored_songs_to_show | How Many Sponsor Songs To Show | Don't use anymore |

### New Settings

| ID | Var name | Name | Description |
| --- | -------- | ---- | ---- |
| 1 | music_paging_mode | Pagination Style | Select Pagination Style at Search Page. |
| 2 | music_display_music_created_in_group | Display music which created in Group to the Music app | Enable to display all public music created in Group to the Music app. Disable to hide them. |
| 3 | music_display_music_created_in_page | Display music which created in Page to the Music app | Enable to display all public music created in Page to the Music app. Disable to hide them. |
| 4 | music_meta_description | Music Meta Description | Meta description added to pages related to the Music app. |
| 5 | music_meta_keywords | Music Meta Keywords | Meta keywords that will be displayed on sections related to the Music app. |
| 6 | max_songs_per_upload | Maximum number of songs per upload | Define the maximum number of songs a user can upload each time they use the upload form. Notice: This setting does not control how many songs a user can upload in total, just how many they can upload each time they use the upload form to upload new songs. |

### Deprecated Functions

| ID | Class Name | Function Name | Will Remove In | Reason |
| --- | -------- | ---- | ---- | ---- |
| 1 | Apps\Core_Music\Service\Callback | getFavoriteSong | 4.6.0 | Don't use anymore |
| 2 | Apps\Core_Music\Service\Callback | getFavoriteAlbum | 4.6.0 | Don't use anymore |
| 3 | Apps\Core_Music\Service\Album\Album | getLatestAlbums | 4.6.0 | Don't use anymore |
| 4 | Apps\Core_Music\Service\Genre\Genre | getUserGenre | 4.6.0 | Don't use anymore |

### Deprecated Blocks

| ID | Block | Will Remove In | Reason |
| --- | -------- | ---- | ---- |
| 1 | music.genre | 4.6.0 | Don't use anymore |

### New Blocks

| ID | Block | Name | Description |
| --- | -------- | ---- | ------------ |
| 1 | music.suggestion | Suggestion | Suggest songs have same genres with viewing song. |
| 2 | music.related-album | Related Albums | Show other albums have same owner with viewing album. |