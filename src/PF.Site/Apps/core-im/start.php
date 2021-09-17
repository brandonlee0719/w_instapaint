<?php
/**
 * IM namespace : /im/*
 */

$package_id = 0;
$cache = cache('im/hosted');
if (request()->get('im-reset-cache')) {
    $cache->del();
}

if (!($host = $cache->get()) || defined('PF_IM_DEBUG_URL')) {
    if (!defined('PHPFOX_TRIAL_MODE') and defined('PHPFOX_LICENSE_ID') and PHPFOX_LICENSE_ID) {
        $home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
        $hosted = $home->im();
        if (isset($hosted->license_id)) {
            $package_id = $hosted->package_id;
        }
    }

    $cache->set('im/hosted', [
        'package_id' => $package_id
    ]);

    $host = cache('im/hosted')->get();
}

if (!empty($host['package_id']) && request()->segment(2) != 'hosting') {
    define('PF_IM_PACKAGE_ID', $host['package_id']);

    if (empty(storage()->get('im/host/status')->value) || storage()->get('im/host/status')->value == 'on') {
        if (PF_IM_PACKAGE_ID) {
            $url = (defined('PF_IM_DEBUG_URL') ? PF_IM_DEBUG_URL : 'https://im-node.phpfox.com/');
            setting()->set('pf_im_node_server', $url);

            $token = cache('im/host/token')->get(null, 1440);
            if (!$token) {
                $token = (new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY))->im_token();
                storage()->update('im/host/expired', !isset($token->token));
                cache()->set('im/host/token', $token);
                $cache->del();
            }
            $token = (object)$token;

            define('PHPFOX_IM_TOKEN', isset($token->token) ? $token->token : 'failed');
            if (isset($token->token)) {
                storage()->del('im/host/status');
                storage()->set('im/host/status', 'on');
            }
        }
    }
}

group('/im', function() {
    // No host
    route('/no-hosting', function() {
        auth()->isAdmin(true);

        storage()->del('im/no/host');
        storage()->set('im/no/host', 1);

        return j('.pf_im_hosting')->remove();
    });

    // AdminCP
    route('/admincp', function() {
        auth()->isAdmin(true);

        // storage()->del('im/no/host');

        $url = url()->make('/admincp/app', ['id' => 'PHPfox_IM', 'im-reset-cache' => '1']);

        return view('admincp.html', [
            'callback' => Core\Home::store() . 'pay/im_hosting?auth=' . PHPFOX_LICENSE_ID . ':' . PHPFOX_LICENSE_KEY . '&return_url=' . urlencode($url),
            'package_id' => (defined('PF_IM_PACKAGE_ID') ? PF_IM_PACKAGE_ID : 0),
            'no_hosting' => storage()->get('im/no/host'),
            'status' => ($status = storage()->get('im/host/status')) ? $status->value : '',
            'expired' => ($expired = storage()->get('im/host/expired')) ? !!($expired->value) : false
        ]);
    });

    route('/link', function() {
        $link = db()->select('*')->from(':link')->where(['link' => request()->get('url')])->executeRow();
        if (!$link) {
            $link = Phpfox::getService('link')->getLink(request()->get('url'));
            $link_id = Phpfox::getService('link.process')->add(
                ['link' => ['image' => $link['default_image'],
                    'url' => $link['link'],
                    'title' => $link['title'],
                    'description' => $link['description'],
                    'embed_code' => $link['embed_code']
                ]], true);
            $link = db()->select('*')->from(':link')->where(['link_id' => $link_id])->executeRow();
        }

        return [
            'link' => $link,
            'time_stamp' => request()->get('time_stamp')
        ];
    });

    // IM in popup mode
    route('/popup', function() {
        Core\View::$template = 'blank';

        $image = Phpfox_Image_Helper::instance()->display([
            'user' => Phpfox::getUserBy(),
            'suffix' => '_50_square'
        ]);

        $imageUrl = Phpfox_Image_Helper::instance()->display([
            'user' => Phpfox::getUserBy(),
            'suffix' => '_50_square',
            'return_url' => true
        ]);

        $image = htmlspecialchars($image);
        $image = str_replace(['<', '>'], ['&lt;', '&gt;'], $image);

        $sticky_bar = '<div id="auth-user" data-image-url="' . str_replace("\"", '\'', $imageUrl) . '" data-user-name="' . Phpfox::getUserBy('user_name') . '" data-id="' . Phpfox::getUserId() . '" data-name="' . Phpfox::getUserBy('full_name') . '" data-image="' . $image . '"></div>';

        return render('popup.html', [
            'sticky_bar' => $sticky_bar
        ]);
    });

    route('/failed', function() {
        h1('Messenger', '#');

        return render('failed.html');
    });

    // Load friends
    route('/friends', function() {
        $iUserIds = array_unique(explode(',', request()->get('threads')));
        $sCond = '';
        foreach ($iUserIds as $iUserId) {
            if ($iUserId && Phpfox::getUserId() !== $iUserId) {
                $sCond .= " AND friend.friend_user_id != $iUserId";
            }
        }
        $friends = Phpfox::getService('friend')->get("friend.user_id=" . Phpfox::getUserId() . $sCond,
            'u.full_name ASC', '', request()->get('limit'), false);
        $str = '';
        foreach ($friends as $friend) {
            $imageLink = \Phpfox_Image_Helper::instance()->display([
                'user' => $friend,
                'suffix' => '_120_square',
                'no_link' => true
            ]);
            $thread_id = (Phpfox::getUserId() < $friend['friend_user_id']) ? Phpfox::getUserId().':'. $friend['friend_user_id'] : $friend['friend_user_id'] . ':' . Phpfox::getUserId();
            $users = (Phpfox::getUserId() < $friend['friend_user_id']) ? Phpfox::getUserId().','. $friend['friend_user_id'] : $friend['friend_user_id'] . ',' . Phpfox::getUserId();

            $str .= '<div class="pf-im-panel" data-thread-id="'. $thread_id .'">'
                .'<div class="pf-im-panel-image"><a class="no_ajax_link" href="'. Phpfox_Url::instance()->makeUrl($friend['user_name']) .'" target="_blank">'. $imageLink .'</a></div><div class="pf-im-panel-content"><span class="__thread-name" data-users="'. $users .'">'. $friend['full_name']
                .'</span><div class="pf-im-panel-preview twa_built"></div></div></div>';
        }
        echo $str;
    });

    route('/panel', function() {
        $cache = [];
        $users = request()->get('users');
        foreach (explode(',', $users) as $user) {
            if (empty($user)) {
                continue;
            }
            $cache[$user] = true;
        }

        $threads = [];
        foreach ($cache as $id => $value) {
            $u = (new \Api\User())->get($id);

            // check banned user
            if (!empty($u)) {
                $u->is_banned = Phpfox::getService('ban')->isUserBanned(['user_id' => $id])['is_banned'];
            }
            $threads[$id] = $u;
        }

        return $threads;
    });

    route('/conversation', function() {
        $user = null;
        $listing = null;

        if (!request()->get('listing_id') && Phpfox::isModule('friend') && !Phpfox::getService('friend')->isFriend(user()->id, request()->get('user_id'))) {
            return [
                'error' => 'not_friends'
            ];
        }

        if (request()->get('listing_id')) {
            $listing = Phpfox::getService('marketplace')->getListing(request()->get('listing_id'));
        }

        return [
            'user' => (new \Api\User())->get(request()->get('user_id')),
            'listing' => $listing
        ];
    });

    route('/search-friends', function () {
        $friends =  Phpfox::getService('friend')->get("u.full_name like '%" . request()->get('search') . "%' AND friend.user_id=" . Phpfox::getUserId(),
            'u.full_name ASC', '', setting('pf_total_conversations', 20), false);
        $str = '';
        foreach ($friends as $friend) {
            $imageLink = \Phpfox_Image_Helper::instance()->display([
                'user' => $friend,
                'suffix' => '_120_square',
                'no_link' => true
            ]);

            $str .= '<div class="pf-im-panel" onclick="$Core.composeMessage({user_id: '. $friend['user_id'] .'});" data-friend-id="'. $friend['user_id'] .'">'
                .'<div class="pf-im-panel-image"><a class="no_ajax_link" href="'. Phpfox_Url::instance()->makeUrl($friend['user_name']) .'" target="_blank">'. $imageLink .'</a></div><div class="pf-im-panel-content"><span class="__thread-name" data-users="">' . $friend['full_name'] . '</span>'
                .'<div class="pf-im-panel-preview"></div></div></div>';
        }
        echo $str;
    });

    route('/attachment', function () {
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return;
        }
        $aValid = Phpfox::getService('attachment.type')->getTypes();

        $iMaxSize = null;
        if (Phpfox::getUserParam('attachment.item_max_upload_size') !== 0) {
            $iMaxSize = (Phpfox::getUserParam('attachment.item_max_upload_size') / 1024);
        }

        $oFile = Phpfox_File::instance();
        $aImage = $oFile->load('file', $aValid, $iMaxSize);
        if ($aImage === false) {
            return;
        }

        $bIsImage = in_array($aImage['ext'], Phpfox::getParam('attachment.attachment_valid_images'));

        $oAttachment = Phpfox::getService('attachment.process');
        $iId = $oAttachment->add(array(
                'category' => '',
                'file_name' => $_FILES['file']['name'],
                'extension' => $aImage['ext'],
                'is_image' => $bIsImage
            )
        );
        $sFileName = $oFile->upload('file', Phpfox::getParam('core.dir_attachment'), $iId);
        $sFileSize = filesize(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''));

        $oAttachment->update(array(
            'file_size' => $sFileSize,
            'destination' => $sFileName,
            'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ), $iId);

        if ($bIsImage) {
            $oImage = Phpfox_Image::instance();
            $sThumbnail = Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, '_thumb');
            $sViewImage = Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, '_view');

            $oImage->createThumbnail(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''), $sThumbnail, Phpfox::getParam('attachment.attachment_max_thumbnail'), Phpfox::getParam('attachment.attachment_max_thumbnail'));
            $oImage->createThumbnail(Phpfox::getParam('core.dir_attachment') . sprintf($sFileName, ''), $sViewImage, Phpfox::getParam('attachment.attachment_max_medium'), Phpfox::getParam('attachment.attachment_max_medium'));

            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'attachment', $sFileSize);
            $sPath = Phpfox::getLib('image.helper')->display(array('server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID'), 'path' => 'core.url_attachment', 'file' => $sFileName, 'suffix' => '_view', 'max_width' => 'attachment.attachment_max_medium', 'max_height' =>'attachment.attachment_max_medium', 'return_url' => true));
        } else {
            $sPath = Phpfox_Url::instance()->makeUrl('attachment/download', ['id' => $iId]);
        }

        return [
            'id' => $iId,
            'isImage' => $bIsImage,
            'path' => $sPath
        ];
    });

    route('/get-attachment', function () {
        $iId = request()->get('id');
        list($iCnt, $aAttachment) = Phpfox::getService('attachment')->get(['attachment_id' => $iId]);
        if (!$iCnt) {
            return false;
        }
        $aAttachment = $aAttachment[0];
        if ($aAttachment['is_image']) {
            $bIsImage = true;
            $sPath = Phpfox::getLib('image.helper')->display(array('server_id' => $aAttachment['server_id'], 'path' => 'core.url_attachment', 'file' => $aAttachment['destination'], 'suffix' => '', 'max_width' => 'attachment.attachment_max_medium', 'max_height' =>'attachment.attachment_max_medium', 'return_url' => true));
            $sThumb = Phpfox::getLib('image.helper')->display(array('server_id' => $aAttachment['server_id'], 'path' => 'core.url_attachment', 'file' => $aAttachment['destination'], 'suffix' => '_thumb', 'max_width' => 'attachment.attachment_max_medium', 'max_height' =>'attachment.attachment_max_medium', 'return_url' => true));
        } else {
            $bIsImage = false;
            $sPath = Phpfox_Url::instance()->makeUrl('attachment/download', ['id' => $iId]);
            $sThumb = '';
        }
        return [
            'id' => $iId,
            'is_image' => $bIsImage,
            'path' => $sPath,
            'thumb' => $sThumb,
            'file_name' => $aAttachment['file_name']
        ];
    });

    route('/ban-user', function () {
        Phpfox::getService('ban.process')->banUser(Phpfox::getUserId(), 0, 2);
    });

    route('/get-token', function () {
        $timestamp = \Phpfox_Request::instance()->get('timestamp');
        if (empty($timestamp)) {
            exit;
        }
        // return token
        echo md5($timestamp . setting('pf_im_node_server_key'));
        exit;
    });
});

group('im/admincp/', function () {
    // AdminCP - Manage sound
    route('manage-sound/', function() {
        auth()->isAdmin(true);
        \Phpfox_Module::instance()->dispatch('im.admincp.sound');
        return 'controller';
    });

    // AdminCP - Import data from v3
    route('import-data-v3/', function() {
        auth()->isAdmin(true);
        \Phpfox_Module::instance()->dispatch('im.admincp.import');
        return 'controller';
    });
});

\Phpfox_Module::instance()->addComponentNames('controller', [
    'im.admincp.manage-sound' => '\Apps\PHPfox_IM\Controller\AdminManageSoundController',
    'im.admincp.import-data-v3'  => '\Apps\PHPfox_IM\Controller\AdminImportDataController',
])->addTemplateDirs([
    'im' => PHPFOX_DIR_SITE_APPS . 'core-im' . PHPFOX_DS . 'views',
])->addComponentNames('ajax', [
    'im.ajax' => \Apps\PHPfox_IM\Ajax\Ajax::class
])->addAliasNames('im', 'PHPfox_IM');

\Phpfox_Template::instance()->setPhrase([
    "messenger",
    "conversations",
    "friends",
    "no_conversations",
    "no_friends_found",
    "send",
    "open_in_new_tab",
    "close_chat_box",
    "this_message_has_been_deleted",
    "messaged_you",
    "unable_to_load_im",
    "hide_thread",
    "search_thread",
    "noti_thread",
    "no_message",
    "search_message",
    "enter_search_text",
    "play",
    "close",
    "loading_conversation",
    "loading_messages",
    "error",
    "deleted_user",
    "invalid_user",
    "you_cannot_reply_this_conversation",
    "uploading",
    "im_failed",
    "add_attachment",
    "im_file",
    "just_now",
    "a_minute_ago",
    "minutes_ago",
    "a_hour_ago",
    "hours_ago"
]);
