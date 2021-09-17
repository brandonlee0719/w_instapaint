<?php

/**
 * Using the Event handler we add JS & CSS to the <head></head>
 */
new Core\Event([
    // event to attach to
    'lib_phpfox_template_getheader' => function (Phpfox_Template $Template) {
        if (!setting('m9_facebook_enabled')) {
            $Template->setHeader('<script>var Fb_Login_Disabled = true;</script>');
            $Template->setHeader('<style>.fb_login_go, #header_menu #js_block_border_user_login-block form > .table:first-of-type:before {display:none !important;} #header_menu #js_block_border_user_login-block .title { margin-bottom: 0px; }</style>');
        }
        if ($cached = storage()->get('fb_user_notice_' . user()->id)) {
            storage()->del('fb_user_notice_' . user()->id);
            $sHtml = '<div>' . _p('you_just_signed_up_successfully_with_email_email',
                    ['email' => $cached->value->email]) . '</div>';
            $sHtml .= '<div>' . _p('click_here_to_change_your_password', ['link' => url('user/setting')]) . '</div>';
            $Template->setHeader('<script>var Fb_show_notice = false; $Behavior.onReadyAfterLoginFB = function(){ setTimeout(function(){if(Fb_show_notice) return; Fb_show_notice = true; tb_show(\'' . _p('notice_uppercase') . '\',\'\',\'\',\'' . $sHtml . '\');$(\'#\'+$sCurrentId).find(\'.js_box_close:first\').show();},200);}</script>');
        }
    }
]);
// Make sure the app is enabled
if (!setting('m9_facebook_enabled')) {
    return;
}

if (auth()->isLoggedIn() && ($cached = storage()->get('fb_force_email_' . user()->id) || substr(user()->email,
            -3) == '@fb' || substr(user()->email, -13) == '@facebook.com')
    && request()->segment(1) != 'fb'
    && request()->segment(2) != 'email'
    && request()->segment(1) != 'logout'
    && request()->segment(1) != 'logout') {
    url()->send('/fb/email');
}

route('/fb/email', function () {
    auth()->membersOnly();

    if (request()->isPost()) {
        $val = request()->get('val');
        $validator = validator()->rule('email')->email();
        if (empty($val['email'])) {
            error(_p('provide_your_email'));
        }
        if ($validator->make()) {
            $users = db()->select('COUNT(*)')->from(':user')->where(['email' => $val['email']])->count();
            if ($users) {
                error(_p('Email is already in use.'));
            }

            db()->update(':user', ['email' => $val['email']], ['user_id' => user()->id]);
            storage()->del('fb_force_email_' . user()->id);

            //Set cached to show popup notify
            storage()->set('fb_user_notice_' . user()->id, ['email' => $val['email']]);

            url()->send('/', 'Thank you for adding your email!');
        }
    }

    section(_p('Active Email'), '/fb/email');

    $email = user()->email;
    if (substr($email, -3) == '@fb' || substr($email, -13) == '@facebook.com') {
        $email = '';
    }

    return view('email.html', [
        'email' => $email
    ]);
});

// Use the FB SDK to set the apps ID & Secret
Facebook\FacebookSession::setDefaultApplication(setting('m9_facebook_app_id'), setting('m9_facebook_app_secret'));

// We override the main settings page since their account is connected to FB
$Url = new Core\Url();
if (Phpfox::isUser() && $Url->uri() == '/user/setting/' && substr(Phpfox::getUserBy('email'), -3) == '@fb') {
    (new Core\Route('/user/setting'))->run(function (\Core\Controller $Controller) {
        return $Controller->render('setting.html');
    });
}

/**
 * Controller for the FB login routine
 */
(new Core\Route('/fb/login'))->run(function (\Core\Controller $Controller) {
    $helper = new Facebook\FacebookRedirectLoginHelper($Controller->url->make('/fb/auth'));
    $loginUrl = $helper->getLoginUrl(['public_profile', 'email']);

    header('Location: ' . $loginUrl);
    exit;
});

/**
 * Auth routine for FB Connect. This is where we either create the new user or log them in if they are already a user.
 */
(new Core\Route('/fb/auth'))->run(function (\Core\Controller $Controller) {
    $helper = new Facebook\FacebookRedirectLoginHelper($Controller->url->make('/fb/auth'));
    $session = $helper->getSessionFromRedirect();

    if ($session) {
        $request = new Facebook\FacebookRequest($session, 'GET', '/me', ['fields' => 'email,name,gender']);
        $response = $request->execute();

        $user = $response->getGraphObject(Facebook\GraphUser::className());

        if ($user instanceof Facebook\GraphUser) {
            $Service = new \Apps\PHPfox_Facebook\Model\Service();
            $Service->create($user);

            $sUrl = '';
            if (Phpfox::getParam('core.redirect_guest_on_same_page')) {
                $sUrl = Phpfox::getLib('session')->get('redirect');
                if (is_bool($sUrl)) {
                    $sUrl = '';
                }
                if (empty($sUrl) && !empty($sMainUrl)) {
                    $sUrl = $sMainUrl;
                }
                if (!filter_var($sUrl, FILTER_VALIDATE_URL) === false) {

                } elseif ($sUrl) {
                    $aParts = explode('/', trim($sUrl, '/'));
                    if (isset($aParts[0])) {
                        $aParts[0] = Phpfox_Url::instance()->reverseRewrite($aParts[0]);
                    }
                    if (isset($aParts[0]) && !Phpfox::isModule($aParts[0])) {
                        $aUserCheck = Phpfox::getService('user')->getByUserName($aParts[0]);
                        if (isset($aUserCheck['user_id'])) {
                            if (isset($aParts[1]) && !Phpfox::isModule($aParts[1])) {
                                $sUrl = '';
                            }
                        } else {
                            $sUrl = '';
                        }
                    }
                }
            }

            if (empty($sUrl) && Phpfox::getParam('user.redirect_after_login')) {
                $sUrl = Phpfox::getParam('user.redirect_after_login');
            }

            $Controller->url->send($sUrl);
        }
    }
});
