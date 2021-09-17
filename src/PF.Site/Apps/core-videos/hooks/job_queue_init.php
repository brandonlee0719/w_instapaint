<?php
\Core\Queue\Manager::instance()->addHandler('videos_ffmpeg_encode', '\Apps\PHPfox_Videos\Job\Encode');
\Core\Queue\Manager::instance()->addHandler('videos_convert_old_videos', '\Apps\PHPfox_Videos\Job\ConvertOldVideos');
