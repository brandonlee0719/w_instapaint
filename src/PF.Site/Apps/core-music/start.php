<?php
namespace Apps\Core_Music;
use Phpfox;

\Phpfox_Module::instance()
    ->addAliasNames('music', 'Core_Music')
    ->addServiceNames([
        'music.album'               => Service\Album\Album::class,
        'music.album.browse'        => Service\Album\Browse::class,
        'music.album.process'       => Service\Album\Process::class,
        'music.browse'              => Service\Browse::class,
        'music.callback'            => Service\Callback::class,
        'music.genre'               => Service\Genre\Genre::class,
        'music.genre.process'       => Service\Genre\Process::class,
        'music'                     => Service\Music::class,
        'music.process'             => Service\Process::class,
        'music.song.browse'         => Service\Song\Browse::class,
        'music.song.process'        => Service\Song\Process::class,
    ])
    ->addTemplateDirs([
        'music'                     => PHPFOX_DIR_SITE_APPS . 'core-music' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'music.album' => Controller\AlbumController::class,
        'music.frame' => Controller\FrameController::class,
        'music.download' => Controller\DownloadController::class,
        'music.index' => Controller\IndexController::class,
        'music.profile' => Controller\ProfileController::class,
        'music.upload' => Controller\UploadController::class,
        'music.view-album' => Controller\ViewAlbumController::class,
        'music.view' => Controller\ViewController::class,
        'music.browse.album' => Controller\Browse\AlbumController::class,
        'music.admincp.index' => Controller\Admin\IndexController::class,
        'music.admincp.add' => Controller\Admin\AddController::class,
        'music.admincp.delete' => Controller\Admin\DeleteController::class
    ])
    ->addComponentNames('ajax', [
        'music.ajax'                => Ajax\Ajax::class,
    ])
    ->addComponentNames('block', [
        'music.featured-album'      => Block\FeaturedAlbumBlock::class,
        'music.featured'            => Block\FeaturedBlock::class,
        'music.suggestion'          => Block\SuggestionBlock::class,
        'music.related-album'       => Block\RelatedAlbumBlock::class,
        'music.list'                => Block\ListBlock::class,
        'music.menu-album'          => Block\MenuAlbumBlock::class,
        'music.menu'                => Block\MenuBlock::class,
        'music.new-album'           => Block\NewAlbumBlock::class,
        'music.rows'                => Block\RowsBlock::class,
        'music.album-rows'          => Block\AlbumRowsBlock::class,
        'music.song'                => Block\SongBlock::class,
        'music.sponsored-album'     => Block\SponsoredAlbumBlock::class,
        'music.sponsored-song'      => Block\SponsoredSongBlock::class,
        'music.track'               => Block\TrackBlock::class,
        'music.upload'              => Block\UploadBlock::class,
    ]);
group('/music', function () {
    // BackEnd routes
    route('/admincp', function() {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('music.admincp.index');
        return 'controller';
    });
    route('/admincp/genre/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'music_genre',
                'key' => 'genre_id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->remove();
        return true;
    });
    route('/', 'music.index');
    route('/delete', 'music.index')->where([':id' => '([0-9]+)']);
    route('/upload/*', 'music.upload');
    route('/frame/*', 'music.frame');
    route('/download/*', 'music.download')->where([':id' => '([0-9]+)']);
    route('/:id/*', 'music.view')->where([':id' => '([0-9]+)']);
    route('/view/*', 'music.view');
    route('/genre/:id/*', 'music.index')->where([':id' => '([0-9]+)']);
    route('/browse/album/*', 'music.browse.album');
    route('/browse/song/*', 'music.index');

    // upload temporary album photo
    route('/add-album-photo', function () {
        $oFile = \Phpfox_File::instance();
        // max file size allow to upload
        $iMaxUploadFileSize = Phpfox::getUserParam('photo.photo_max_upload_size') === 0 ? null : (Phpfox::getUserParam('photo.photo_max_upload_size') / 1024);
        $oImage = \Phpfox_Image::instance();

        // upload file to server successfully
        if ($_FILES['file']['error'][0] == UPLOAD_ERR_OK) {
            $aImage = $oFile->load("file[0]", ['jpg', 'gif', 'png'], $iMaxUploadFileSize);
            if (!$aImage) {
                $sErrorMessage = implode(', ', \Phpfox_Error::get());
            }
        } else {
            // check file error
            switch ($_FILES['file']['error'][0]) {
                case UPLOAD_ERR_INI_SIZE:
                    $sErrorMessage = _p('the_uploaded_file_exceeds_the_upload_max_filesize_max_file_size_directive_in_php_ini',
                        ['upload_max_filesize' => ini_get('upload_max_filesize')]);
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $sErrorMessage = _p("the_uploaded_file_exceeds_the_MAX_FILE_SIZE_directive_that_was_specified_in_the_HTML_form");
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $sErrorMessage = _p("the_uploaded_file_was_only_partially_uploaded");
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $sErrorMessage = _p("no_file_was_uploaded");
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $sErrorMessage = _p("missing_a_temporary_folder");
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $sErrorMessage = _p("failed_to_write_file_to_disk");
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $sErrorMessage = _p("file_upload_stopped_by_extension");
                    break;
                default:
                    $sErrorMessage = _p("unknown_upload_error");
                    break;
            }
        }

        if (isset($sErrorMessage)) {
            echo json_encode([
                'error' => $sErrorMessage
            ]);
            exit;
        }

        $sFileName = $oFile->upload('image', Phpfox::getParam('music.dir_image'), uniqid());
        $iFileSizes = filesize(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''));
        $aPicSizes = array(50, 120, 200, 500);
        foreach ($aPicSizes as $iSize) {
            if (Phpfox::getParam('core.keep_non_square_images')) {
                $oImage->createThumbnail(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''),
                    Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
            }
            $oImage->createThumbnail(Phpfox::getParam('music.dir_image') . sprintf($sFileName, ''),
                Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize,
                false);

            $iFileSizes += filesize(Phpfox::getParam('music.dir_image') . sprintf($sFileName, '_' . $iSize));
        }
        // Update user space usage
        Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'music_image', $iFileSizes);

        echo json_encode([
            'file' => $sFileName,
            'server_id' => \Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID'),
        ]);
        exit;
    });

    route('/album/add/*', 'music.album');
    route('/album/track/*', 'music.album');
    route('/album/:id/*', 'music.view-album')->where([':id' => '([0-9]+)']);
    route('/album/*', 'music.album');
    route('/message', function () {
        if (isset($_REQUEST['valid']) && $_REQUEST['valid'] > 0) {
            Phpfox::addMessage(_p('sharing_total_song_s_successfully',['total' => $_REQUEST['valid']]));
        }
        else {
            Phpfox::addMessage(_p('no_songs_have_been_uploaded_successfully'));
        }
        $sModule = $_REQUEST['module'];
        $iItemId = (int)$_REQUEST['item'];
        $iAlbumId = (int)$_REQUEST['album'];
        $oUrl = Phpfox::getLib('url');
        if ($iAlbumId > 0) {
            $sRedirectUrl = $oUrl->permalink('music.album',$iAlbumId);
        } else {
            $sRedirectUrl = $oUrl->makeUrl('music');
        }
        if (!empty($sModule) && Phpfox::isModule($sModule) && !$iAlbumId) {
            if (Phpfox::hasCallback($sModule,'getMusicDetails')) {
                $aParams = Phpfox::callback($sModule.'.getMusicDetails',['item_id' => $iItemId]);
                if (isset($aParams['url_home_photo'])) {
                    $sRedirectUrl = $aParams['url_home_photo'];
                } else {
                    $sRedirectUrl = $oUrl->permalink($sModule,$iItemId) . '/music';
                }
            } else {
                $sRedirectUrl = $oUrl->permalink($sModule,$iItemId) . '/music';
            }
        }
        echo json_encode([
            'sUrl' => $sRedirectUrl
        ]);

        exit;
    });

});
$sDefaultSongPhoto = flavor()->active->default_photo('music_default_photo', true);
if (!$sDefaultSongPhoto) {
    $sDefaultSongPhoto = setting('core.path_actual') . 'PF.Site/Apps/core-music/assets/image/nophoto_song.png';
}
$sDefaultAlbumPhoto = flavor()->active->default_photo('music_default_album_photo', true);
if (!$sDefaultAlbumPhoto) {
    $sDefaultAlbumPhoto = setting('core.path_actual') . 'PF.Site/Apps/core-music/assets/image/music_v02.png';
}

Phpfox::getLib('setting')->setParam('music.default_song_photo', $sDefaultSongPhoto);
Phpfox::getLib('setting')->setParam('music.default_album_photo', $sDefaultAlbumPhoto);

//set cache for genres in 10 minutes.
Phpfox::getLib('setting')->setParam('music.genres_cache_time', 10);

Phpfox::getLib('setting')->setParam('music.url_photo', Phpfox::getParam('core.url_pic') . 'music/');

Phpfox::getLib('setting')->setParam('music.thumbnail_sizes', [50, 120, 200, 500]);


