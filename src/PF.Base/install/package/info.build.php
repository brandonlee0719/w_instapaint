<?php defined("PHPFOX") or die("Access denied"); return array (
  'theme-material' => 
  array (
    'type' => 'theme',
    'target' => 'PF.Site/flavors/material',
    'filename' => 'theme-material.zip',
    'apps_id' => NULL,
    'apps_dir' => NULL,
    'apps_name' => NULL,
    'apps_version' => '4.6.0',
  ),
  'blogs' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-blogs',
    'filename' => 'core-blogs.zip',
    'apps_id' => 'Core_Blogs',
    'apps_dir' => 'core-blogs',
    'apps_name' => 'Blogs',
    'apps_version' => '4.6.0',
    'checking' => 'module_exists:blog,app_exists:Core_Blogs',
  ),
  'photos' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-photos',
    'filename' => 'core-photos.zip',
    'apps_id' => 'Core_Photos',
    'apps_dir' => 'core-photos',
    'apps_name' => 'Photos',
    'apps_version' => '4.6.0',
    'upgrade_if' => true,
  ),
  'videos' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-videos',
    'filename' => 'core-videos.zip',
    'apps_id' => 'PHPfox_Videos',
    'apps_dir' => 'core-videos',
    'apps_name' => 'Videos',
    'apps_version' => '4.6.0',
  ),
  'music' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-music',
    'filename' => 'core-music.zip',
    'apps_id' => 'Core_Music',
    'apps_dir' => 'core-music',
    'apps_name' => 'Music',
    'apps_version' => '4.6.0',
  ),
  'pages' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-pages',
    'filename' => 'core-pages.zip',
    'apps_id' => 'Core_Pages',
    'apps_dir' => 'core-pages',
    'apps_name' => 'Pages',
    'apps_version' => '4.6.0',
  ),
  'captcha' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-captcha',
    'filename' => 'core-captcha.zip',
    'apps_id' => 'Core_Captcha',
    'apps_dir' => 'core-captcha',
    'apps_name' => 'Captcha',
    'apps_version' => '4.6.0',
  ),
  'quizzes' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-quizzes',
    'filename' => 'core-quizzes.zip',
    'apps_id' => 'Core_Quizzes',
    'apps_dir' => 'core-quizzes',
    'apps_name' => 'Quizzes',
    'apps_version' => '4.6.0',
  ),
  'polls' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-polls',
    'filename' => 'core-polls.zip',
    'apps_id' => 'Core_Polls',
    'apps_dir' => 'core-polls',
    'apps_name' => 'Polls',
    'apps_version' => '4.6.0',
  ),
  'shoutbox' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-shoutbox',
    'filename' => 'core-shoutbox.zip',
    'apps_id' => 'phpFox_Shoutbox',
    'apps_dir' => 'core-shoutbox',
    'apps_name' => 'Shoutbox',
    'apps_version' => '4.6.0',
  ),
  'events' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-events',
    'filename' => 'core-events.zip',
    'apps_id' => 'Core_Events',
    'apps_dir' => 'core-events',
    'apps_name' => 'Events',
    'apps_version' => '4.6.0',
  ),
  'forum' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-forums',
    'filename' => 'core-forums.zip',
    'apps_id' => 'Core_Forums',
    'apps_dir' => 'core-forums',
    'apps_name' => 'Forums',
    'apps_version' => '4.6.0',
  ),
  'groups' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-groups',
    'filename' => 'core-groups.zip',
    'apps_id' => 'PHPfox_Groups',
    'apps_dir' => 'core-groups',
    'apps_name' => 'Groups',
    'apps_version' => '4.6.0',
  ),
  'marketplace' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-marketplace',
    'filename' => 'core-marketplace.zip',
    'apps_id' => 'Core_Marketplace',
    'apps_dir' => 'core-marketplace',
    'apps_name' => 'Marketplace',
    'apps_version' => '4.6.0',
  ),
  'egifts' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-egift',
    'filename' => 'core-egifts.zip',
    'apps_id' => 'Core_eGifts',
    'apps_dir' => 'core-egifts',
    'apps_name' => 'eGifts',
    'apps_version' => '4.6.0',
  ),
  'rss' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-rss',
    'filename' => 'core-rss.zip',
    'apps_id' => 'Core_RSS',
    'apps_dir' => 'core-rss',
    'apps_name' => 'Rss',
    'apps_version' => '4.6.0',
  ),
  'ckeditor' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-CKEditor',
    'filename' => 'core-ckeditor.zip',
    'apps_id' => 'phpFox_CKEditor',
    'apps_dir' => 'core-CKEditor',
    'apps_name' => 'CKEditor',
    'apps_version' => '4.6.0',
  ),
  'cdn' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-cdn',
    'filename' => 'core-cdn.zip',
    'apps_id' => 'PHPfox_CDN',
    'apps_dir' => 'core-cdn',
    'apps_name' => 'CDN Base',
    'apps_version' => '4.6.0',
  ),
  'announcement' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-announcement',
    'filename' => 'core-announcement.zip',
    'apps_id' => 'Core_Announcement',
    'apps_dir' => 'core-announcement',
    'apps_name' => 'Announcement',
    'apps_version' => '4.6.0',
  ),
  'newsletter' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-newsletter',
    'filename' => 'core-newsletter.zip',
    'apps_id' => 'Core_Newsletter',
    'apps_dir' => 'core-newsletter',
    'apps_name' => 'Newsletter',
    'apps_version' => '4.6.0',
  ),
  'poke' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-poke',
    'filename' => 'core-poke.zip',
    'apps_id' => 'Core_Poke',
    'apps_dir' => 'core-poke',
    'apps_name' => 'Poke',
    'apps_version' => '4.6.0',
  ),
  'im' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-im',
    'filename' => 'core-im.zip',
    'apps_id' => 'PHPfox_IM',
    'apps_dir' => 'core-im',
    'apps_name' => 'Instant Messages',
    'apps_version' => '4.6.0',
  ),
  'restful' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-restful-api',
    'filename' => 'core-restful.zip',
    'apps_id' => 'phpFox_RESTful_API',
    'apps_dir' => 'core-restful-api',
    'apps_name' => 'Restful Api',
    'apps_version' => '4.6.0',
  ),
  'facebook' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-facebook',
    'filename' => 'core-facebook.zip',
    'apps_id' => 'PHPfox_Facebook',
    'apps_dir' => 'core-facebook',
    'apps_name' => 'Facebook',
    'apps_version' => '4.6.0',
  ),
  'emoji' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-twemoji-awesome',
    'filename' => 'core-emoji.zip',
    'apps_id' => 'PHPfox_Twemoji_Awesome',
    'apps_dir' => 'core-twemoji-awesome',
    'apps_name' => 'Twemoji',
    'apps_version' => '4.6.0',
  ),
  'amazon-s3' => 
  array (
    'type' => 'app',
    'target' => 'PF.Site/Apps/core-amazon-s3',
    'filename' => 'core-amazon-s3.zip',
    'apps_id' => 'PHPfox_AmazonS3',
    'apps_dir' => 'core-amazon-s3',
    'apps_name' => 'Amazon CDN',
    'apps_version' => '4.5.4',
  ),
);