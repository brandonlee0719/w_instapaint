<?php

namespace Apps\PHPfox_Groups\Controller;

use Core\Event;
use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_IS_PAGES_VIEW', true);
define('PHPFOX_PAGES_ITEM_TYPE', 'groups');

class ViewController extends Phpfox_Component
{
    public function process()
    {
        user('pf_group_browse', null, null, true);

        $mId = $this->request()->getInt('req2');
        defined('PHPFOX_PAGES_ITEM_ID') or define('PHPFOX_PAGES_ITEM_ID', $mId);

        if (!($aPage = \Phpfox::getService('groups')->getForView($mId))) {
            return Phpfox_Error::display(_p('The group you are looking for cannot be found.'));
        }

        if (($this->request()->get('req3')) != '') {
            $this->template()->assign([
                'bRefreshPhoto' => true,
            ]);
        }

        if ($aPage['view_id'] != '0' && !Phpfox::getService('groups')->canModerate() && (Phpfox::getUserId() != $aPage['user_id'])) {
            return Phpfox_Error::display(_p('The group you are looking for cannot be found.'));
        }

        if ($aPage['view_id'] == '2') {
            return Phpfox_Error::display(_p('The group you are looking for cannot be found.'));
        }

        if (Phpfox::getUserBy('profile_page_id') <= 0 && Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('groups', $aPage['page_id'], $aPage['user_id'],
                $aPage['privacy'], (isset($aPage['is_friend']) ? $aPage['is_friend'] : 0));
        }
        //Check group privacy
        if ($aPage['reg_method'] == 2 && !Phpfox::getService('groups')->isMember($aPage['page_id']) && !Phpfox::isAdmin() && !Phpfox::getService('groups')->isInvited($aPage['page_id'])) {
            Phpfox_Url::instance()->send('privacy.invalid');
        }
        $bCanViewPage = true;
        $sCurrentModule = Phpfox_Url::instance()->reverseRewrite($this->request()->get((($this->request()->get('req1') == 'groups') ? 'req3' : 'req2')));

        \Phpfox::getService('groups')->buildWidgets($aPage['page_id']);

        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_view_build')) ? eval($sPlugin) : false);


        $this->setParam([
            'aParentModule' => [
                'module_id' => 'groups',
                'item_id' => $aPage['page_id'],
                'url' => \Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']),
            ],
            'allowTagFriends' => false
        ]);

        if (isset($aPage['is_admin']) && $aPage['is_admin']) {
            defined('PHPFOX_IS_PAGE_ADMIN') or define('PHPFOX_IS_PAGE_ADMIN', true);
        }

        $sModule = $sCurrentModule;

        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_view_assign')) ? eval($sPlugin) : false);

        $this->setParam([
            'aPage' => $aPage,
            'aCallback' => array_merge($aPage, [
                'module_id' => 'groups',
                'item_id' => $aPage['page_id'],
                'url_home' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url'])
            ])
        ]);

        $this->template()
            ->assign([
                    'aPage' => $aPage,
                    'sCurrentModule' => $sCurrentModule,
                    'bCanViewPage' => $bCanViewPage,
                    'iViewCommentId' => $this->request()->getInt('comment-id'),
                    'bHasPermToViewPageFeed' => \Phpfox::getService('groups')->hasPerm($aPage['page_id'],
                        'groups.view_browse_updates'),
                ]
            );

        if ($bCanViewPage
            && $sModule
            && Phpfox::isModule($sModule)
            && Phpfox::hasCallback($sModule, 'getGroupSubMenu')
            && !$this->request()->getInt('comment-id')
        ) {
            if (Phpfox::hasCallback($sModule,
                    'canViewGroupSection') && !Phpfox::callback($sModule . '.canViewGroupSection', $aPage['page_id'])) {
                return Phpfox_Error::display(_p('Unable to view this section due to privacy settings.'));
            }

            $this->template()->assign('bIsPagesViewSection', true);
            $this->setParam('bIsPagesViewSection', true);
            $this->setParam('sCurrentPageModule', $sModule);

            Phpfox::getComponent($sModule . '.index', ['bNoTemplate' => true], 'controller');

            Phpfox_Module::instance()->resetBlocks();
        } elseif ($bCanViewPage
            && !\Phpfox::getService('groups')->isWidget($sModule)
            && !$this->request()->getInt('comment-id')
            && $sModule
            && Phpfox::isAppAlias($sModule)
        ) {
            if (Phpfox::hasCallback($sModule,
                    'canViewGroupSection') && !Phpfox::callback($sModule . '.canViewGroupSection', $aPage['page_id'])) {
                return Phpfox_Error::display(_p('Unable to view this section due to privacy settings.'));
            }

            $app_content = Event::trigger('groups_view_' . $sModule);

            Phpfox_Module::instance()->resetBlocks();

            event('lib_module_page_id', function ($obj) use ($sModule) {
                $obj->id = 'groups_' . $sModule;
            });

            $this->template()->assign([
                'app_content' => $app_content,
            ]);

        } elseif ($bCanViewPage && $sModule && \Phpfox::getService('groups')->isWidget($sModule) && !$this->request()->getInt('comment-id')) {
            define('PHPFOX_IS_PAGES_WIDGET', true);
            $aWidget = Phpfox::getService('groups')->getWidget($sModule);
            $this->template()->setTitle($aWidget['title'] . ' &raquo; ' . $aPage['title'])
                ->setBreadCrumb($aWidget['title'])
                ->assign([
                    'aWidget' => \Phpfox::getService('groups')->getWidget($sModule),
                ]
            );
        } elseif ($bCanViewPage && $sModule == 'members') {
            Phpfox::getComponent('groups.members', ['bNoTemplate' => true], 'controller');
            Phpfox_Module::instance()->resetBlocks();
            $this->template()->setTitle(_p('members') . ' &raquo; ' . $aPage['title'], true);
        } else {
            $bCanPostComment = true;
            if ($sCurrentModule == 'pending') {
                $aPendingUsers = Phpfox::getService('groups')->getPendingUsers($aPage['page_id']);
                if (!count($aPendingUsers)) {
                    $this->url()->send(\Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'],
                        $aPage['vanity_url']));
                }

                $this->template()->assign('aPendingUsers', $aPendingUsers);
                $this->setParam('global_moderation', [
                        'name' => 'groups',
                        'ajax' => 'PHPfox_Groups.moderation',
                        'menu' => [
                            [
                                'phrase' => _p('Delete'),
                                'action' => 'delete',
                            ],
                            [
                                'phrase' => _p('Approve'),
                                'action' => 'approve',
                            ],
                        ],
                    ]
                );
            }

            if (\Phpfox::getService('groups')->isAdmin($aPage)) {
                defined('PHPFOX_FEED_CAN_DELETE') or define('PHPFOX_FEED_CAN_DELETE', true);
            }

            if (Phpfox::getUserId()) {
                $bIsBlocked = Phpfox::getService('user.block')->isBlocked($aPage['user_id'], Phpfox::getUserId());
                if ($bIsBlocked) {
                    $bCanPostComment = false;
                }
            }

            if ($sCurrentModule != 'info') {
                defined('PHPFOX_IS_PAGES_IS_INDEX') or define('PHPFOX_IS_PAGES_IS_INDEX', true);
            }

            $this->setParam('aFeedCallback', [
                    'module' => 'groups',
                    'table_prefix' => 'pages_',
                    'ajax_request' => 'groups.addFeedComment',
                    'item_id' => $aPage['page_id'],
                    'disable_share' => ($bCanPostComment ? false : true),
                    'feed_comment' => 'groups_comment',
                ]
            );
            if (isset($aPage['text']) && !empty($aPage['text'])) {
                $this->template()->setMeta('description', $aPage['text']);
            }
            $this->template()->setTitle($aPage['title'])
                ->setEditor()
                ->setHeader('cache', [
                        'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                        'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                    ]
                )
                ->setMeta([
                    'keywords' => _p('groups_meta_keywords'),
                    'description' => _p('groups_meta_description')
                ]);

            if ($sModule == 'info') {
                $this->template()->setTitle(_p('info') . ' &raquo; ' . $aPage['title'], true);
            }

            if (in_array($sModule, ['', 'wall', 'home'])) {
                Phpfox_Module::instance()->appendPageClass('_is_groups_feed');
            }
        }

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}
