<?php

namespace Apps\PHPfox_Groups\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_IS_GROUPS_ADD', true);

class AddController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);
        user('pf_group_add', null, null, true);

        \Phpfox::getService('groups')->setIsInPage();

        $bIsEdit = false;
        $bIsNewPage = false;
        $sStep = $this->request()->get('req3');
        $aPage = [];
        $aDetailErrorsMessages = [_p('group_name_is_empty')];

        if (($iEditId = $this->request()->getInt('id')) && ($aPage = \Phpfox::getService('groups')->getForEdit($iEditId))) {
            $bIsEdit = true;

            if ($aPage['image_path']) {
                $aPage['image_path'] = Phpfox::getLib('image.helper')->display([
                        'server_id' => $aPage['image_server_id'],
                        'path' => 'pages.url_image',
                        'file' => $aPage['image_path'],
                        'suffix' => '_120',
                        'return_url' => true,
                        'time_stamp' => true
                    ]
                );
            }

            $aMenus = [
                'detail' => _p('Details'),
                'info' => _p('Info'),
            ];

            if (!$aPage['is_app']) {
                $aMenus['photo'] = _p('Photo');
            }
            $aMenus['permissions'] = _p('Permissions');
            if (Phpfox::isModule('friend') && Phpfox::getUserBy('profile_page_id') == 0) {
                $aMenus['invite'] = _p('Invite');
            }
            if (!$bIsNewPage) {
                $aMenus['url'] = _p('Url');
                $aMenus['admins'] = _p('Admins');
                $aMenus['widget'] = _p('Widgets');
            }

            if ($bIsNewPage) {
                $iCnt = 0;
                foreach ($aMenus as $sMenuName => $sMenuValue) {
                    $iCnt++;
                    $aMenus[$sMenuName] = _p('Step count', ['count' => $iCnt]) . ': ' . $sMenuValue;
                }
            }

            $this->template()->buildPageMenu('js_groups_block',
                $aMenus,
                [
                    'link' => \Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'],
                        $aPage['vanity_url']),
                    'phrase' => ($bIsNewPage ? _p('Skip view this page') : _p('View this page')),
                ]
            );

            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('groups.process')->update($aPage['page_id'], $aVals, $aPage)) {
                    if ($bIsNewPage && $this->request()->getInt('action') == '1') {
                        switch ($sStep) {
                            case 'invite':
                                if (Phpfox::isModule('friend')) {
                                    $this->url()->send('groups.add.url', ['id' => $aPage['page_id'], 'new' => '1']);
                                }
                                break;
                            case 'permissions':
                                $this->url()->send('groups.add.invite', ['id' => $aPage['page_id'], 'new' => '1']);
                                break;
                            case 'photo':
                                $this->url()->send('groups.add.permissions', ['id' => $aPage['page_id'], 'new' => '1']);
                                break;
                            case 'info':
                                $this->url()->send('groups.add.photo', ['id' => $aPage['page_id'], 'new' => '1']);
                                break;
                            default:
                                $this->url()->send('groups.add.info', ['id' => $aPage['page_id'], 'new' => '1']);
                                break;
                        }
                    }

                    // update old group
                    $this->url()->send('groups.add', ['id' => $aPage['page_id'], 'tab' => empty($aVals['current_tab']) ? '' : $aVals['current_tab']], _p('group_successfully_updated'));
                } else {
                    \Phpfox_Error::setDisplay(false);
                    foreach (\Phpfox_Error::get() as $sError) {
                        if (in_array($sError, $aDetailErrorsMessages)) {
                            $aDetailErrors[] = $sError;
                        } else {
                            $aPhotoErrors[] = $sError;
                        }
                    }
                    if (isset($aDetailErrors)) {
                        $this->template()->assign('aDetailErrors', $aDetailErrors);
                    }
                    if (isset($aPhotoErrors) && !isset($aDetailErrors)) {
                        $this->template()->assign([
                            'aPhotoErrors' => $aPhotoErrors,
                            'sActiveTab' => 'photo'
                        ]);
                    }
                }
            }
            if (Phpfox::isAdmin() && Phpfox::getUserId() != $aPage['user_id']) {
                $aViewer = Phpfox::getService('user')->getUser(Phpfox::getUserId());
                $this->template()->assign([
                    'aViewer' => json_encode([
                        'user_id' => Phpfox::getUserId(),
                        'full_name' => $aViewer['full_name'],
                        'user_image' => Phpfox::getLib('image.helper')->display([
                            'user' => $aViewer,
                            'suffix' => '_50_square',
                            'max_height' => 32,
                            'max_width' => 32,
                            'return_url' => true
                        ])
                    ])
                ]);
            }

            // build widgets
            $this->template()->assign([
                'aBlockWidgets' => Phpfox::getService('groups')->getWidgetsOrdering($iEditId),
                'aMenuWidgets' => Phpfox::getService('groups')->getWidgetsOrdering($iEditId, false),
                'aForms' => \Phpfox::getService('groups')->getForEdit($iEditId)
            ]);
        } elseif (isset($iEditId) && !\Phpfox_Error::isPassed()) {
            // when user enter an edit link but don't have permission to edit that group
            return false;
        }

        $this->template()->setTitle(($bIsEdit ? '' . _p('Editing Group') . ': ' . $aPage['title'] : _p('add_new_group')))
            ->setBreadCrumb(_p('Groups'), $this->url()->makeUrl('groups'))
            ->setBreadCrumb(($bIsEdit ? '' . _p('Editing Group') . ': ' . $aPage['title'] : _p('add_new_group')),
                $this->url()->makeUrl('groups.add', ['id' => $iEditId]), true)
            ->setPhrase([
                    'select_a_file_to_upload',
                    'add_new_group'
                ]
            )
            ->setHeader([
                    'privacy.css' => 'module_user',
                    'progress.js' => 'static_script',
                    'jquery/plugin/jquery.tablednd.js' => 'static_script',
                    'drag.js' => 'app_core-groups',
                    'jquery.cropit.js' => 'module_user',
                ]
            )
            ->setHeader(['<script type="text/javascript">$Behavior.groupsProgressBarSettings = function(){ if ($Core.exists(\'#js_groups_block_customize_holder\')) { oProgressBar = {holder: \'#js_groups_block_customize_holder\', progress_id: \'#js_progress_bar\', uploader: \'#js_progress_uploader\', add_more: false, max_upload: 1, total: 1, frame_id: \'js_upload_frame\', file_id: \'image\'}; $Core.progressBarInit(); } }</script>'])
            ->assign([
                    'aPermissions' => (isset($aPage) && isset($aPage['page_id']) ? \Phpfox::getService('groups')->getPerms($aPage['page_id']) : []),
                    'aTypes' => Phpfox::getService('groups.type')->get(),
                    'bIsEdit' => $bIsEdit,
                    'iMaxFileSize' => user('pf_group_max_upload_size',
                        500) ? Phpfox::getLib('phpfox.file')->filesize((user('pf_group_max_upload_size',
                                500) / 1024) * 1048576) : null,
                    'aWidgetEdits' => \Phpfox::getService('groups')->getWidgetsForEdit(),
                    'bIsNewPage' => $bIsNewPage,
                    'sStep' => $sStep
                ]
            )
            ->setMeta([
                'keywords' => _p('groups_meta_keywords'),
                'description' => _p('groups_meta_description')
            ]);

        return 'controller';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}
