<?php

event('app_settings', function ($settings) {
    if (!isset($settings['pf_core_cache_driver'])) {
        return;
    }
    $redis_file = PHPFOX_DIR_SETTINGS . 'redis.sett.php';
    if (isset($settings['pf_core_redis']) && $settings['pf_core_redis'] == '1' && !empty($settings['pf_core_redis_host'])) {
        file_put_contents($redis_file,
            "<?php\nreturn ['host' => '{$settings['pf_core_redis_host']}', 'enabled' => 1];\n");
    } else {
        if (isset($settings['pf_core_redis']) && !$settings['pf_core_redis'] && isset($settings['pf_core_redis_host'])) {
            file_put_contents($redis_file,
                "<?php\nreturn ['host' => '{$settings['pf_core_redis_host']}', 'enabled' => 0];\n");
        }
    }

    $cache_file = PHPFOX_DIR_SETTINGS . 'cache.sett.php';
    $cache_file_data = [];
    if (isset($settings['pf_core_cache_driver'])) {
        $cache_file_data['driver'] = $settings['pf_core_cache_driver'];
        switch ($cache_file_data['driver']) {
            case 'redis':
                $cache_file_data['redis'] = [
                    'host' => $settings['pf_core_cache_redis_host'],
                    'port' => $settings['pf_core_cache_redis_port']
                ];
                file_put_contents($cache_file, "<?php\n return " . var_export($cache_file_data, true) . ";\n");
                break;
            case 'memcached':
                $cache_file_data['memcached'] = [
                    [$settings['pf_core_cache_memcached_host'], $settings['pf_core_cache_memcached_port'], 1]
                ];
                file_put_contents($cache_file, "<?php\n return " . var_export($cache_file_data, true) . ";\n");
                break;
            default:
                if (file_exists($cache_file)) {
                    @unlink($cache_file);
                }

        }
    }
});

function materialParseIcon($sKey, $sDefault = null) {
    static $aIconParseList = [
        'attachment' => 'paperplane-alt-o',
        'blog' => 'compose-alt',
        'groups' => 'user-man-three-o',
        'marketplace' => 'store-o',
        'music' => 'music-note-o',
        'pages' => 'flag-waving-o',
        'photo' => 'photos-alt-o',
        'poll' => 'bar-chart2',
        'quiz' => 'question-circle-o',
        'todo' => 'paragraph-plus',
        'activity-points' => 'star-circle-o',
        'event' => 'calendar-check-o',
        'forum' => 'comments-o',
        'rss' => 'rss-o',
        'default' => 'box-o',
        'user' => 'user1-three-o',
        'home' => 'alignleft',
        'members' => 'user1-three-o',
        'info' => 'info-circle-alt-o',
        'video' => 'video',
        'all_results' => 'search-o'
    ];

    if (empty($aIconParseList[$sKey])) {
        return $sDefault ? $sDefault : 'ico ico-' . $aIconParseList['default'];
    }

    return 'ico ico-' . $aIconParseList[$sKey];
}