<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond Benc
 * @package          Module_Admincp
 * @version          $Id: admincp.class.php 6343 2013-07-19 19:42:10Z Raymond_Benc $
 */
class Admincp_Service_Admincp extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
    }

    /**
     * @deprecated from 4.6.0
     *
     * @param       $sCall
     * @param array $aPost
     */
    public function getHostingInfo($sCall, $aPost = [])
    {
    }

    /**
     * Get global settings + user group settings for search tools.
     * The result will be used by Fuse
     *
     * @param array $aSkipModules
     *
     * @return array
     */
    public function getForSearch($aSkipModules = [])
    {
        $sCacheId = $this->cache()->set('global_setting_all');

        if (!($aRows = $this->cache()->get($sCacheId))) {

            $aRows = Phpfox::getService('admincp.setting')->getForSearch($aSkipModules);

            if (Phpfox::getParam('core.search_group_settings')) {
                foreach (Phpfox::getService('user.group.setting')->getForSearch($aSkipModules) as $aRow) {
                    $aRows[] = $aRow;
                }
            }

            $this->cache()->save($sCacheId, $aRows);
            Phpfox::getLib('cache')->group('search', $sCacheId);
        }

        return $aRows;
    }

    /**
     * Get admincp main menus
     *
     * @return array
     */
    public function getMainMenus()
    {
        $oUrl = Phpfox::getLib('url');

        list($aGroups,) = Phpfox::getService('admincp.setting.group')->get();

        $aCache = $aGroups;
        $aGroups = [];

        foreach ($aCache as $key => $value) {

            $n = $key;
            switch ($value['group_id']) {
                case 'cookie':
                    $n = _p('browser_cookies');
                    break;
                case 'site_offline_online':
                    $n = _p('toggle_site');
                    break;
                case 'general':
                    $n = _p('site_settings');
                    break;
                case 'mail':
                    $n = _p('mail_server');
                    break;
                case 'spam':
                    $n = _p('spam_assistance');
                    break;
                case 'registration':
                    continue 2;
                    break;
            }

            $aGroups[$n] = $value;
        }
        ksort($aGroups);


        $aSettings = [];
        foreach ($aGroups as $sGroupName => $aGroupValues) {
            $aSettings[$sGroupName] = [
                'icon'  => '',
                'label' => $sGroupName,
                'link'  => $oUrl->makeUrl('admincp.setting.edit', ['group-id' => $aGroupValues['group_id']]),
            ];
        }

        $aSettings = array_merge($aSettings, [
            'short_urls'            => [
                'icon'  => '',
                'label' => _p('short_urls'),
                'link'  => $oUrl->makeUrl('admincp.setting.url'),
            ],
            'URL Match'             => [
                'icon'  => '',
                'label' => _p('URL Match'),
                'link'  => $oUrl->makeUrl('admincp.setting.redirection'),
            ],
            'seo'                   => [
                'icon'  => '',
                'label' => _p('seo'),
                'link'  => $oUrl->makeUrl('admincp.setting.edit', ['group-id' => 'seo']),
            ],
            'payment_gateways_menu' => [
                'icon'  => '',
                'label' => _p('payment_gateways_menu'),
                'link'  => $oUrl->makeUrl('admincp.api.gateway'),
            ],
            'Performance'           => [
                'icon'  => '',
                'label' => _p('Performance'),
                'link'  => $oUrl->makeUrl('/admincp/app/settings', ['id' => 'PHPfox_Core', 'group' => 'core_redis']),
            ],
            'Data Cache'            => [
                'icon'  => '',
                'label' => _p('Data Cache'),
                'link'  => $oUrl->makeUrl('/admincp/app/settings', ['id' => 'PHPfox_Core', 'group' => 'core_cache_driver']),
            ],
            'Cron'                  => [
                'icon'  => '',
                'label' => _p('Cron'),
                'link'  => $oUrl->makeUrl('admincp.cron'),
            ],
            'anti_spam_questions'   => [
                'icon'  => '',
                'label' => _p('anti_spam_questions'),
                'link'  => $oUrl->makeUrl('admincp.user.spam'),
            ],
            'cancellation_options'  => [
                'icon'  => '',
                'label' => _p('cancellation_options'),
                'link'  => $oUrl->makeUrl('admincp.user.cancellations.manage'),
            ],
            'license_key'           => [
                'icon'  => '',
                'label' => _p('license_key'),
                'link'  => $oUrl->makeUrl('admincp.setting.license'),
            ],
        ]);

        $badge = Phpfox::getService('admincp.alert')->getAdminMenuBadgeNumber();

        if ($badge) {
            $aMenus['alerts'] = [
                'icon'  => '',
                'label' => 'Alerts',
                'link'  => $oUrl->makeUrl('admincp.alert'),
                'badge' => $badge,
            ];
        }

        $aMenus['dashboard'] = [
            'icon'  => '',
            'label' => _p('dashboard'),
            'link'  => $oUrl->makeUrl('admincp'),
        ];

        $aMenus['apps'] = [
            'icon'  => '',
            'label' => _p('apps'),
            'link'  => $oUrl->makeUrl('admincp.apps'),
        ];

        $aMenus['appearance'] = [
            'icon'  => '',
            'label' => _p('appearance'),
            'link'  => '#',
            'items' => [
                'themes' => [
//                    'icon'=> 'fa fa-paint-brush',
'label' => _p('themes'),
'link'  => $oUrl->makeUrl('admincp.theme'),
                ],

                'pages' => [
//                    'icon'=> 'fa fa-file-text-o',
'label' => _p('pages'),
'link'  => $oUrl->makeUrl('admincp.page'),
                ],
                'menu'  => [
//                    'icon'=> 'fa fa-bars',
'label' => _p('menus'),
'link'  => $oUrl->makeUrl('admincp.menu'),
                ],
                'block' => [
//                    'icon'=> 'fa fa-th',
'label' => _p('blocks'),
'link'  => $oUrl->makeUrl('admincp.block'),
                ],
            ],
        ];

        $aMenus['maintenance'] = [
            'icon'  => '',
            'label' => _p('maintenance'),
            'link'  => '#',
            'items' => [
                'menu_cache_manager'           => [
                    'icon'  => '',
                    'label' => _p('menu_cache_manager'),
                    'link'  => $oUrl->makeUrl('admincp.maintain.cache'),
                ],
                'site_statistics'              => [
                    'icon'  => '',
                    'label' => _p('site_statistics'),
                    'link'  => $oUrl->makeUrl('admincp.core.stat'),
                ],
                'admincp_menu_system_overview' => [
                    'icon'  => '',
                    'label' => _p('admincp_menu_system_overview'),
                    'link'  => $oUrl->makeUrl('admincp.core.system'),
                ],
                'reported_items'               => [
                    'icon'  => '',
                    'label' => _p('reported_items'),
                    'link'  => $oUrl->makeUrl('admincp.report'),
                ],
                'admincp_menu_reparser'        => [
                    'icon'  => '',
                    'label' => _p('admincp_menu_reparser'),
                    'link'  => $oUrl->makeUrl('admincp.maintain.reparser'),
                ],
                'remove_duplicates'            => [
                    'icon'  => '',
                    'label' => _p('remove_duplicates'),
                    'link'  => $oUrl->makeUrl('admincp.maintain.duplicate'),
                ],
                'Remove files no longer used'  => [
                    'icon'  => '',
                    'label' => _p('Remove files no longer used'),
                    'link'  => $oUrl->makeUrl('admincp.maintain.removefile'),
                ],
                'counters'                     => [
                    'icon'  => '',
                    'label' => _p('counters'),
                    'link'  => $oUrl->makeUrl('admincp.maintain.counter'),
                ],
                'check_modified_files'         => [
                    'icon'  => '',
                    'label' => _p('check_modified_files'),
                    'link'  => $oUrl->makeUrl('admincp.checksum.modified'),
                ],
                'check_unknown_files'          => [
                    'icon'  => '',
                    'label' => _p('check_unknown_files'),
                    'link'  => $oUrl->makeUrl('admincp.checksum.unknown'),
                ],
                'find_missing_settings'        => [
                    'icon'  => '',
                    'label' => _p('find_missing_settings'),
                    'link'  => $oUrl->makeUrl('admincp.setting.missing'),
                ],
                'rebuild_core_theme'           => [
                    'icon'  => '',
                    'label' => _p('Rebuild Core Theme'),
                    'link'  => $oUrl->makeUrl('admincp.theme.bootstrap.rebuild'),
                ],
                'revert_core_theme'            => [
                    'icon'  => '',
                    'class' => 'sJsConfirm',
                    'label' => _p('Revert Core Theme'),
                    'link'  => $oUrl->makeUrl('flavors.manage', ['id' => 'bootstrap', 'type' => 'revert', 'process' => 'yes']),
                ],
                'ban_filters'                  => [
                    'icon'  => '',
                    'label' => _p('ban_filters'),
                    'link'  => $oUrl->makeUrl('admincp.ban.email'),
                ],
            ],
        ];

        $aMenus['globalize'] = [
            'icon'  => '',
            'label' => _p('Globalize'),
            'link'  => '#',
            'items' => [
                'languages'  => [
                    'icon'  => '',
                    'label' => _p('languages'),
                    'link'  => $oUrl->makeUrl('admincp.language'),
                ],
                'phrases'    => [
                    'icon'  => '',
                    'label' => _p('phrases'),
                    'link'  => $oUrl->makeUrl('admincp.language.phrase'),
                ],
                'countries'  => [
                    'icon'  => '',
                    'label' => _p('countries'),
                    'link'  => $oUrl->makeUrl('admincp.core.country'),
                ]
                ,
                'currencies' => [
                    'icon'  => '',
                    'label' => _p('currencies'),
                    'link'  => $oUrl->makeUrl('admincp.core.currency'),
                ],
            ],
        ];

        $aMenus['techie'] = [
            'icon'  => '',
            'label' => _p('techie'),
            'link'  => '#',
            'items' => [
                'techie_product'   => [
                    'icon'  => '',
                    'label' => _p('products'),
                    'link'  => $oUrl->makeUrl('admincp.product'),
                ],
                'techie_plugins'   => [
                    'icon'  => '',
                    'label' => _p('plugins'),
                    'link'  => $oUrl->makeUrl('admincp.plugin'),
                ],
                'techie_component' => [
                    'icon'  => '',
                    'label' => _p('components'),
                    'link'  => $oUrl->makeUrl('admincp.component'),
                ],
            ],
        ];

        $aMenus['members'] = [
            'icon'  => '',
            'label' => _p('members'),
            'link'  => '#',
            'items' => [
                'search'               => [
                    'icon'  => '',
                    'label' => _p('manage_users'),
                    'link'  => $oUrl->makeUrl('admincp.user.browse'),
                ],
                'group'                => [
                    'icon'  => '',
                    'label' => _p('manage_user_groups'),
                    'link'  => $oUrl->makeUrl('admincp.user.group'),
                ],
                'group_settings'       => [
                    'icon'  => '',
                    'label' => _p('user_group_settings'),
                    'link'  => $oUrl->makeUrl('admincp.user.group.add', ['group_id' => 2, 'setting' => true, 'module' => 'core']),
                ],
                'subscriptions'        => [
                    'icon'  => '',
                    'label' => _p('subscriptions'),
                    'link'  => $oUrl->makeUrl('admincp.subscribe'),
                ],
                'promotions'           => [
                    'icon'  => '',
                    'label' => _p('promotions'),
                    'link'  => $oUrl->makeUrl('admincp.user.promotion'),
                ],
                'custom'               => [
                    'icon'  => '',
                    'label' => _p('custom_fields'),
                    'link'  => $oUrl->makeUrl('admincp.custom'),
                ],
                'settings'             => [
                    'icon'  => '',
                    'label' => _p('manage_settings'),
                    'link'  => $oUrl->makeUrl('admincp.setting.edit', ['module-id' => 'user']),
                ],
                'registration'         => [
                    'icon'  => '',
                    'label' => _p('registration_settings'),
                    'link'  => $oUrl->makeUrl('admincp.setting.edit', ['group-id' => 'registration']),
                ],
                'relationship_statues' => [
                    'icon'  => '',
                    'label' => _p('relationship_statues'),
                    'link'  => $oUrl->makeUrl('admincp.custom.relationships'),
                ],
                'inactive_members'     => [
                    'icon'  => '',
                    'label' => _p('inactive_members'),
                    'link'  => $oUrl->makeUrl('admincp.user.inactivereminder'),
                ],
                'cancelled_members'    => [
                    'icon'  => '',
                    'label' => _p('cancelled_members'),
                    'link'  => $oUrl->makeUrl('admincp.user.cancellations.feedback'),
                ],
            ],
        ];

        $aMenus['settings'] = [
            'icon'  => 'fa fa-cog',
            'label' => _p('settings'),
            'link'  => '#',
            'items' => $aSettings,
        ];


        if (!defined('PHPFOX_IS_TECHIE') or !PHPFOX_IS_TECHIE) {
            unset($aMenus['techie']);
        }

        (($sPlugin = Phpfox_Plugin::get('admincp_get_main_menus')) ? eval($sPlugin) : false);

        return $aMenus;
    }

    /**
     * @return array
     */
    public function getHostingStats()
    {
        $sCacheId = $this->cache()->set('admincp_site_cache');

        if (!($aReturn = $this->cache()->get($sCacheId, 1 * 60 * 60))) // cache is in hours
        {
            $aParts = explode('|', Phpfox::getParam('core.phpfox_max_users_online'));
            $iTotalMemberCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->where('view_id = 0')
                ->execute('getSlaveField');

            $iOneGigIntoBytes = 1073741824;
            $iTotalSpace = ($aParts[0] * $iOneGigIntoBytes);
            $iTotalUsed = Phpfox::getLib('cdn')->getUsage();

            $iCount1 = $iTotalUsed / $iTotalSpace;
            $iCount2 = $iCount1 * 100;
            $iTotalUsage = number_format($iCount2, 0);
            if (!$iTotalUsage) {
                $iTotalUsage = '< 1';
            }

            if ($aParts[1] == '0') {
                $aParts[1] = 'Unlimited';
            } else {
                $iCount1 = $iTotalMemberCnt / $aParts[1];
                $iCount2 = $iCount1 * 100;
                $iTotalMemberUsage = number_format($iCount2, 0);
                if (!$iTotalMemberUsage) {
                    $iTotalMemberUsage = '< 1';
                }
            }

            $sPastHistory = Phpfox::getParam('core.phpfox_total_users_online_history');
            $aParts = explode('|', $sPastHistory);
            $iVideosUploaded = 0;
            $iTotalVideoUsage = 0;
            if (isset($aParts[1])) {
                $iVideosUploaded = (int)$aParts[0];
                $iCount1 = $iVideosUploaded / Phpfox::getParam('core.phpfox_total_users_online_mark');
                $iCount2 = $iCount1 * 100;
                $iTotalVideoUsage = number_format($iCount2, 0);
            }

            $aReturn = [
                'sTotalSpaceUsage'  => $iTotalUsage . '% (' . Phpfox_File::instance()->filesize($iTotalUsed) . ' out of ' . $aParts[0] . ' GB)',
                'sTotalMemberUsage' => (isset($iTotalMemberUsage) ? $iTotalMemberUsage . '% (' . $iTotalMemberCnt . ' out of ' . number_format($aParts[1]) . ')' : 'Unlimited'),
                'sTotalVideoUsage'  => $iTotalVideoUsage . '% (' . $iVideosUploaded . ' out of ' . Phpfox::getParam('core.phpfox_total_users_online_mark') . ')',
            ];

            $this->cache()->save($sCacheId, $aReturn);
            Phpfox::getLib('cache')->group('admincp', $sCacheId);
        }

        return $aReturn;
    }

    /**
     * @return array
     */
    public function getAdmincpRules()
    {
        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('admincp_privacy'))
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');

        $aUserGroupCache = [];
        $aUserGroups = $this->database()->select('*')
            ->from(Phpfox::getT('user_group'))
            ->execute('getSlaveRows');
        foreach ($aUserGroups as $aUserGroup) {
            $aUserGroupCache[$aUserGroup['user_group_id']] = $aUserGroup['title'];
        }

        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['user_groups'] = '';
            foreach ((array)json_decode($aRow['user_group'], true) as $iGroup) {
                if (!isset($aUserGroups[$iGroup])) {
                    continue;
                }

                $aRows[$iKey]['user_groups'] .= $aUserGroupCache[$iGroup] . ', ';
            }

            $aRows[$iKey]['user_groups'] = rtrim($aRows[$iKey]['user_groups'], ', ');
        }

        return $aRows;
    }

    /**
     * @param array $aMenus
     *
     * @return array
     */
    public function checkAdmincpPrivacy($aMenus)
    {
        $sCacheId = $this->cache()->set('admincp_url_' . Phpfox::getUserId());

        $aPrivacyCache = [];
        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('admincp_privacy'))
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');
        foreach ($aRows as $aRow) {
            foreach ((array)json_decode($aRow['user_group'], true) as $iGroup) {
                $aPrivacyCache[$iGroup][$aRow['url']] = ($aRow['wildcard'] ? true : false);
            }
        }

        $aCache = [];
        if (isset($aPrivacyCache[Phpfox::getUserBy('user_group_id')])) {
            $aCache = $aPrivacyCache[Phpfox::getUserBy('user_group_id')];
            $sUrl = Phpfox_Url::instance()->getFullUrl(true);
            $sUrl = str_replace('/', '.', $sUrl);
            $sUrl = trim($sUrl, '.');
            $sNewParts = '';
            $aParts = explode('.', $sUrl);
            foreach ($aParts as $sPart) {
                if (strpos($sPart, '_')) {
                    continue;
                }
                $sNewParts .= $sPart . '.';
            }
            $sNewParts = rtrim($sNewParts, '.');

            $bFailed = false;
            foreach ($aCache as $sUrlValue => $bWildcard) {
                if ($sUrlValue == $sNewParts) {
                    $bFailed = true;
                }

                if ($bWildcard && preg_match('/' . $sUrlValue . '(.*)/i', $sNewParts)) {
                    $bFailed = true;
                }
            }

            if ($bFailed) {
                Phpfox_Url::instance()->send('admincp');
            }
        }

        foreach ($aMenus as $sPhrase1 => $mValue1) {
            if (is_array($mValue1)) {
                foreach ($mValue1 as $sPhrase2 => $mValue2) {
                    if (is_array($mValue2)) {
                        foreach ($mValue2 as $sPhrase3 => $mValue3) {
                            if (isset($aCache[$mValue3])) {
                                unset($aMenus[$sPhrase1][$sPhrase2][$sPhrase3]);
                            }

                            foreach ($aCache as $sUrlValue => $bWildcard) {
                                if ($bWildcard && preg_match('/' . $sUrlValue . '(.*)/i', $mValue3)) {
                                    if (isset($aMenus[$sPhrase1][$sPhrase2][$sPhrase3])) {
                                        unset($aMenus[$sPhrase1][$sPhrase2][$sPhrase3]);
                                    }
                                }
                            }
                        }
                    } else {
                        if (isset($aCache[$mValue2])) {
                            unset($aMenus[$sPhrase1][$sPhrase2]);
                        }
                    }
                }
            }
        }

        $aMenuCache = $aMenus;

        foreach ($aMenuCache as $sP1 => $mV1) {
            if (is_array($mV1)) {
                foreach ($mV1 as $sP2 => $mV2) {
                    if (is_array($mV2) && empty($mV2)) {
                        unset($aMenuCache[$sP1][$sP2]);
                    }
                }
            }
        }

        $this->cache()->save($sCacheId, $aMenuCache);
        Phpfox::getLib('cache')->group('admincp', $sCacheId);

        return $aMenuCache;
    }

    public function checkLatestVersions()
    {

        if ((defined('PHPFOX_TRIAL_MODE') && PHPFOX_TRIAL_MODE)
            || (defined('PHPFOX_IS_TECHIE') && PHPFOX_IS_TECHIE)) {
            return [];
        }
        // fetch all apps data

        return [
            'apps'      => $this->checkLatestAppVersions(),
            'themes'    => $this->checkLatestThemeVersions(),
            'languages' => $this->checkLatestLanguageVersions(),
            'platform'  => $this->checkLatestPhpfoxVersions(),
            'timestamp' => time(),
        ];
    }

    public function checkLatestPhpfoxVersions()
    {
        $remote = json_decode(fox_get_contents('https://raw.githubusercontent.com/PHPfox-Official/phpfox-version/master/version.json'), true);
        $aReturn = [];

        if ($remote and isset($remote['stable'])) {
            $version = Phpfox::getVersion();
            if (version_compare($version, $remote['stable'],'<')) {
                $aReturn[] = [
                    'id'               => 'phpfox',
                    'name'             => 'phpFox',
                    'version'          => $version,
                    'latest_version'   => $remote['stable'],
                    'can_update'       => 1,
                    'have_new_version' => 1,
                    'link'=> isset($remote['blog'])?$remote['blog']:'https://www.phpfox.com/blog/category/releases/',
                ];
            }
        }
        return $aReturn;
    }

    public function checkLatestLanguageVersions()
    {
        $aReturn = [];
        $aRows = $this->database()
            ->select('*')
            ->from(':language')
            ->where('store_id>0')
            ->execute('getSlaveRows');

        foreach ($aRows as $v) {
            $remote = json_decode(fox_get_contents(sprintf('https://store.phpfox.com/product/%d/view.json', $v['store_id'])), true);

            if ($remote && isset($remote['version']) && version_compare($v['version'], $v['version'], '<')) {
                $aReturn[] = [
                    'id'               => $v['language_id'],
                    'store_id'         => $v['store_id'],
                    'version'          => $v['version'],
                    'name'             => $v['title'],
                    'latest_version'   => $v['version'],
                    'can_upgrade'      => 1,
                    'have_new_version' => 1,
                ];
            }
        }

        return $aReturn;
    }


    public function checkLatestAppVersions()
    {
        $aReturn = [];
        $Apps = new Core\App();
        $allApps = $Apps->getForManage();

        $appIdList = array_map(function ($item) {
            return ($item->is_phpfox_default) ? null : $item->id;
        }, $allApps);
        foreach ($appIdList as $keyApp => $value) {
            if (!isset($value) || empty($value)) {
                unset($appIdList[$keyApp]);
            }
        }

        $sendData = ['apps' => $appIdList];
        $Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
        $response = $Home->products(['products' => $sendData]);

        foreach ($allApps as $index => $app) {
            $id = $app->id;
            if (isset($response->products) && isset($response->products->apps) && isset($response->products->apps->$id) && isset($response->products->apps->$id->version)) {
                $app->latest_version = $response->products->apps->$id->version;
                if (version_compare($app->version, $app->latest_version, '<') && isset($response->products->apps->$id->link)) {
                    $aReturn[$id] = [
                        'id'               => $id,
                        'store_id'         => isset($app->store_id) ? $app->store_id : null,
                        'version'          => $app->version,
                        'name'             => $app->name,
                        'latest_version'   => $app->latest_version,
                        'can_upgrade'      => 1,
                        'have_new_version' => 1,
                    ];
                }
            }
            elseif (!empty($app->store_id)) {
                $store = json_decode(@fox_get_contents('https://store.phpfox.com/product/' . $app->store_id . '/view.json'), true);
                if (!empty($store['id']) && !empty($store['version']) && version_compare($app->version, $store['version'], '<')) {
                    $aReturn[$id] = [
                        'id'               => $id,
                        'store_id'         => isset($app->store_id) ? $app->store_id : null,
                        'version'          => $app->version,
                        'name'             => $app->name,
                        'latest_version'   => $store['version'],
                        'can_upgrade'      => 1,
                        'have_new_version' => 1,
                    ];

                }
            }
        }

        return $aReturn;
    }


    public function checkLatestThemeVersions()
    {
        $directory = realpath(PHPFOX_DIR_SITE . '/flavors');
        $aReturn = [];

        foreach (scandir($directory) as $name) {

            if (substr($name, -1) == '.') {
                continue;
            }

            if (!file_exists($filename = $directory . PHPFOX_DS . $name . PHPFOX_DS . 'theme.json')) {
                continue;
            }
            try {

                $info = json_decode(file_get_contents($filename), true);

                if ($info && isset($info['id']) && isset($info['name']) && isset($info['store_id']) && isset($info['version'])) {

                    $remote = json_decode(fox_get_contents(sprintf('https://store.phpfox.com/product/%d/view.json', $info['store_id'])), true);

                    if ($remote && isset($remote['version']) && version_compare($info['version'], $remote['version'], '<')) {
                        $aReturn[] = [
                            'id'               => $info['id'],
                            'name'             => $info['name'],
                            'store_id'         => $info['store_id'],
                            'version'          => $info['version'],
                            'latest_version'   => $remote['version'],
                            'can_upgrade'      => 1,
                            'have_new_version' => 1,
                        ];
                    }
                }
            } catch (Exception $exception) {

            }
        }
        return $aReturn;
    }

    /**
     * @deprecated from 4.6.0
     * @return int
     */
    public function check()
    {
        return 0;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod    is the name of the method
     * @param array  $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('admincp.service_admincp__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}