<?php

namespace Apps\phpFox_RESTful_API;

use Core\Registry;

\Phpfox_Module::instance()->addComponentNames('ajax', [
    'restful_api.ajax' => '\Apps\phpFox_RESTful_API\Ajax\Ajax'
]);

\Phpfox_Module::instance()->addServiceNames([
    'restful_api.storage' => '\Apps\phpFox_RESTful_API\Service\Storage',
    'restful' => '\Apps\phpFox_RESTful_API\Service\RestApiTransport',
]);

defined('phpFox_RESTful_API_PREFIX') or define('phpFox_RESTful_API_PREFIX', 'restful_api');

Registry::set('PHPFOX_EXTERNAL_API_TRANSPORT', 'restful');
Registry::set('PHPFOX_EXTERNAL_API_PREFIX', phpFox_RESTful_API_PREFIX);

group(phpFox_RESTful_API_PREFIX, function () {

    \OAuth2\Autoloader::register();

    $storage = \Phpfox::getService('restful_api.storage');

    $server = new \OAuth2\Server($storage, [
        'allow_implicit' => true,
        'access_lifetime' => (int)setting('pf_restful_api_access_lifetime', 3600),
        'enforce_state' => false
    ]);

    $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
    $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
    $server->addGrantType(new \OAuth2\GrantType\UserCredentials($storage));
    $server->addGrantType(new \OAuth2\GrantType\RefreshToken($storage, [
        'always_issue_new_refresh_token' => true
    ]));

    route('/token', function () use ($server) {
        $server->handleTokenRequest(\OAuth2\Request::createFromGlobals())->send();
    });

    route('/authorize', function () use ($server, $storage) {
        auth()->isLoggedIn(true);

        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();

        // validate the authorize request
        if (!$server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            die;
        }

        if (empty($_POST)) {
            $client_id = $request->query('client_id');
            $client_info = $storage->getClientDetails($client_id);

            return view('authorize.html', [
                'aClientInfo' => $client_info
            ]);
        } else {
            // print the authorization code if the user has authorized your client
            $is_authorized = ($_POST['authorized'] === 'yes');
            $server->handleAuthorizeRequest($request, $response, $is_authorized, user()->id);
            $response->send();
        }
    });

    route('/resource', function () use ($server) {
        if (!$server->verifyResourceRequest(\OAuth2\Request::createFromGlobals())) {
            $server->getResponse()->send();
            die;
        }
        $token = $server->getAccessTokenData(\OAuth2\Request::createFromGlobals());
        $user_id = $token['user_id'];

        echo "User ID associated with this token is {$token['user_id']}";
    });
});

group('restful_api', function () {
    $storage = \Phpfox::getService('restful_api.storage');
    route('/admincp/client/delete', function () use ($storage) {
        auth()->isAdmin(true);

        if ($storage->unsetClient(request()->get('id'))) {
            \Phpfox::addMessage(_p('Client successfully deleted.'));

            return url()->send('admincp.app', ['id' => 'phpFox_RESTful_API']);
        }
    });

    route('/admincp/client', function () use ($storage) {
        auth()->isAdmin(true);

        $id = request()->get('id');
        $client = [
            'client_id' => '',
            'client_name' => '',
            'redirect_uri' => ''
        ];
        if ($id) {
            $client = $storage->getClientDetails($id, false);
        }

        if (request()->isPost() && ($aParams = request()->getArray('val'))) {
            validator()->rule('client_id')->required()->make();
            validator()->rule('client_name')->required()->make();
            validator()->rule('redirect_uri')->required()->make();
            validator()->rule('redirect_uri')->url()->make();
            if (!preg_match('/^[\w\-]+$/', $aParams['client_id'])) {
                return error(_p('The Client ID is invalid. The Client ID can only contain alphanumeric characters and _ or -.'));
            }

            if (!empty($storage->getClientDetails($aParams['client_id'],
                    false)) && $aParams['client_id'] != $client['client_id']) {
                return error(_p('The Client ID is already used.'));
            }

            $secretKey = sha1($aParams['client_id'] . PHPFOX_TIME);

            if ($storage->setClientDetails(['id' => $aParams['client_id'], 'name' => $aParams['client_name']],
                $secretKey, $aParams['redirect_uri'])) {
                return url()->send('admincp.app', ['id' => 'phpFox_RESTful_API']);
            }

            return error(_p('Cannot add new client.'));
        }

        if ($id) {
            title(_p('Edit Client'));
        } else {
            title(_p('New Client'));
        }

        return view('admincp_client.html', [
            'client_id' => $id,
            'client' => $client
        ]);
    });

    route('/admincp', function () use ($storage) {
        auth()->isAdmin(true);

        $clients = $storage->getAllClients();

        return view('admincp.html', [
            'clients' => $clients
        ]);
    });
});
