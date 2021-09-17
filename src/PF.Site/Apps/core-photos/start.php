<?php

namespace Apps\Core_Photos;
use Phpfox;
use Phpfox_Module;

Phpfox_Module::instance()
    ->addAliasNames('photo', 'Core_Photos')
    ->addServiceNames([
        'photo.album' => Service\Album\Album::class,
        'photo.album.browse'  => Service\Album\Browse::class,
        'photo.album.process' => Service\Album\Process::class,
        'photo.api' => Service\Api::class,
        'photo.browse' => Service\Browse::class,
        'photo.callback' => Service\Callback::class,
        'photo.category' => Service\Category\Category::class,
        'photo.category.process' => Service\Category\Process::class,
        'photo' => Service\Photo::class,
        'photo.process' => Service\Process::class,
        'photo.tag.process' => Service\Tag\Process::class,
        'photo.tag' => Service\Tag\Tag::class
    ])
    ->addTemplateDirs([
        'photo' => PHPFOX_DIR_SITE_APPS . 'core-photos' . PHPFOX_DS . 'views'
    ])
    ->addComponentNames('controller', [
        'photo.index' => Controller\IndexController::class,
        'photo.profile' => Controller\ProfileController::class,
        'photo.view' => Controller\ViewController::class,
        'photo.add' => Controller\AddController::class,
        'photo.upload' => Controller\UploadController::class,
        'photo.album' => Controller\AlbumController::class,
        'photo.albums' => Controller\AlbumsController::class,
        'photo.edit-album' => Controller\EditAlbumController::class,
        'photo.converting' => Controller\ConvertingController::class,
        'photo.download' => Controller\DownloadController::class,
        'photo.frame' => Controller\FrameController::class,
        'photo.public-album' => Controller\PublicAlbumController::class,
        'photo.tag' => Controller\TagController::class,
        'photo.frame-drag-drop' => Controller\FrameDragDropController::class,
    ])
    ->addComponentNames('controller', [
        'photo.admincp.add' => Controller\Admin\AddCategoryController::class,
        'photo.admincp.delete-category' => Controller\Admin\DeleteCategoryController::class,
        'photo.admincp.index' => Controller\Admin\CategoryController::class
    ])
    ->addComponentNames('ajax', [
        'photo.ajax' => Ajax\Ajax::class
    ])
    ->addComponentNames('block', [
        'photo.album' => Block\Album::class,
        'photo.album-tag' => Block\AlbumTag::class,
        'photo.attachment' => Block\Attachment::class,
        'photo.category' => Block\Category::class,
        'photo.detail' => Block\Detail::class,
        'photo.drop-down' => Block\DropDown::class,
        'photo.edit-photo' => Block\EditPhoto::class,
        'photo.featured' => Block\Featured::class,
        'photo.menu' => Block\Menu::class,
        'photo.menu-album' => Block\MenuAlbum::class,
        'photo.my-photo' => Block\MyPhoto::class,
        'photo.new' => Block\Newest::class,
        'photo.profile' => Block\Profile::class,
        'photo.share' => Block\Share::class,
        'photo.sponsored' => Block\Sponsored::class,
        'photo.stream' => Block\Stream::class,
        'photo.warning' => Block\Warning::class,
    ]);

group('/photo', function () {
    // BackEnd routes
    route('/admincp', function() {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('photo.admincp.index');
        return 'controller';
    });
    route('/admincp/category/order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'photo_category',
                'key' => 'category_id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->remove();
        return true;
    });

    // FrontEnd routes
    route('/', 'photo.index');
    route('/albums/*', 'photo.albums');
    route('/category/:id/:name/*', 'photo.index')->where([':id' => '([0-9]+)']);
    route('/:id/*', 'photo.view')->where([':id' => '([0-9]+)']);
    route('/add/*', 'photo.add');
    route('/upload/*', 'photo.add');
    route('/frame/', 'photo.frame');
    route('/frame-drag-drop/', 'photo.frame-drag-drop');
    route('/frame-feed-drag-drop/', 'photo.frame-feed-drag-drop');
    route('/album/:id/*', 'photo.album')->where([':id' => '([0-9]+)']);
    route('/album/profile/:id/*', 'photo.album')->where([':id' => '([0-9]+)']);
    route('/album/cover/:id/*', 'photo.album')->where([':id' => '([0-9]+)']);
    route('/converting/', 'photo.converting');
    route('/edit-album/*', 'photo.edit-album');
    route('/download/*', 'photo.download');
    route('/tag/*', 'photo.tag');
    route('/callback/', 'photo.callback');
    route('/public-album/', 'photo.public-album');
    route('/message', function () {
        $iTotal = isset($_REQUEST['valid']) ? $_REQUEST['valid'] : 0;
        if (isset($iTotal) && $iTotal > 0) {
            Phpfox::addMessage(_p('sharing_total_photo_s_successfully',['total' => $iTotal]));
        }
        else {
            Phpfox::addMessage(_p('no_photos_have_been_uploaded_successfully'));
        }
        $oUrl = Phpfox::getLib('url');
        $sRedirectUrl = $oUrl->makeUrl('photo');
        $bMassEdit = Phpfox::getParam('photo.photo_upload_process');
        $iAlbumId = $_REQUEST['album'];
        $oUploadIds = json_decode($_REQUEST['upload_ids']);
        $sModule = $_REQUEST['module'];
        $iItemId = $_REQUEST['item'];
        if ($bMassEdit && $iTotal > 0) {
            $sEditList = '&';
            for ($i = 0; $i < count($oUploadIds); $i++) {
                $sEditList .= 'photos[]=' . $oUploadIds[$i] . '&';
            }
            $sRedirectUrl = $oUrl->makeUrl('photo',['view' => 'my', 'mode' => 'edit']).$sEditList;
        } else {
            if ($iAlbumId > 0) {
                $sRedirectUrl = $oUrl->permalink('photo.album',$iAlbumId);
            } elseif (!empty($sModule) && Phpfox::isModule($sModule)) {
                if (Phpfox::hasCallback($sModule,'getPhotoDetails')) {
                    $aParams = Phpfox::callback($sModule.'.getPhotoDetails',['group_id' => $iItemId]);
                    if (isset($aParams['url_home_photo'])) {
                        $sRedirectUrl = $aParams['url_home_photo'];
                    } else {
                        $sRedirectUrl = $oUrl->permalink($sModule,$iItemId) . '/photo';
                    }
                } else {
                    $sRedirectUrl = $oUrl->permalink($sModule,$iItemId) . '/photo';
                }
            }
        }
        echo json_encode([
            'sUrl' => $sRedirectUrl
        ]);

        exit;
    });
});

$sDefaultAlbumPhoto = flavor()->active->default_photo('photo_default_album_photo', true);
if (!$sDefaultAlbumPhoto) {
    $sDefaultAlbumPhoto = setting('core.path_actual') . 'PF.Site/Apps/core-photos/assets/images/nocover.png';
}
Phpfox::getLib('setting')->setParam('photo.default_album_photo', $sDefaultAlbumPhoto);
//set cache for categories in 10 minutes.
Phpfox::getLib('setting')->setParam('event.categories_cache_time', 10);
