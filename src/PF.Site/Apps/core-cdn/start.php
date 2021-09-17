<?php

// Check if CDN is enabled
if (setting('pf_cdn_enabled')) {

    // Attach an event to the CDN bootloader to our Model
    new Core\Event([
        'lib_phpfox_cdn' => 'Apps\PHPfox_CDN\Model\CDN'
    ]);
}

group('/pfcdn', function () {

    route('/acp/server/delete', function () {
        auth()->isAdmin(true);

        storage()->delById(request()->get('id'));

        Phpfox::getLib('cache')->remove('pf_cdn_servers');

        \Phpfox::addMessage(_p('Server successfully deleted.'));

        return url()->send('admincp.app', ['id' => 'PHPfox_CDN']);
    });

    route('/acp/server', function () {
        auth()->isAdmin(true);

        $server = null;
        $is_edit = false;
        if ($id = request()->get('id')) {
            $is_edit = true;

            $server = storage()->getById($id);
        }

        title(_p(($is_edit ? 'Edit Server' : 'New Server')));

        if (($val = request()->get('val'))) {
            if ($is_edit) {
                storage()->delById($server->id);
            }
            storage()->set('pf_cdn_servers', $val);
            Phpfox::getLib('cache')->remove('pf_cdn_servers');

            \Phpfox::addMessage(_p(($is_edit ? 'Server successfully updated.' : 'Server successfully added.')));

            return url()->send('admincp.app', ['id' => 'PHPfox_CDN']);
        }

        return view('admincp_server.html', [
            'is_edit' => $is_edit,
            'server' => $server
        ]);
    });

    route('/acp', function () {
        auth()->isAdmin(true);

        $servers = storage()->all('pf_cdn_servers');

        return view('admincp.html', [
            'servers' => $servers
        ]);
    });

});
