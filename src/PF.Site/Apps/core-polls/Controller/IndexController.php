<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Controller;

use Phpfox;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class IndexController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle)) {
            \Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('poll_id', 'question'),
                    'table' => 'poll',
                    'redirect' => 'poll',
                    'search' => 'question_url',
                    'title' => $sLegacyTitle
                )
            );
        }

        Phpfox::getUserParam('poll.can_access_polls', true);

        if (($iRedirect = $this->request()->getInt('redirect')) && ($sUrl = \Phpfox::getService('poll.callback')->getFeedRedirect($iRedirect))) {
            $this->url()->forward($sUrl);
        }

        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_index_process_start')) ? eval($sPlugin) : false);

        $sView = $this->request()->get('view');

        if ($iDeleteId = $this->request()->getInt('delete')) {
            if (\Phpfox::getService('user.auth')->hasAccess('poll', 'poll_id', $iDeleteId,
                    'poll.poll_can_delete_own_polls',
                    'poll.poll_can_delete_others_polls') && \Phpfox::getService('poll.process')->moderatePoll($iDeleteId,
                    2)
            ) {
                $this->url()->send('poll', null, _p('poll_successfully_deleted'));
            }
        }

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $bIsProfile = true;
            $aUser = \Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            }
        }

        if ($this->request()->getInt('req2') > 0) {
            return Phpfox_Module::instance()->setController('poll.view');
        }

        $this->search()->set(array(
                'type' => 'poll',
                'field' => 'poll.poll_id',
                'ignore_blocked' => true,
                'search_tool' => array(
                    'table_alias' => 'poll',
                    'search' => array(
                        'action' => (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'],
                            array('poll', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('poll',
                            array('view' => $this->request()->get('view')))),
                        'default_value' => _p('search_polls'),
                        'name' => 'search',
                        'field' => 'poll.question'
                    ),
                    'sort' => array(
                        'latest' => array('poll.time_stamp', _p('latest')),
                        'most-viewed' => array('poll.total_view', _p('most_viewed')),
                        'most-liked' => array('poll.total_like', _p('most_liked')),
                        'most-talked' => array('poll.total_comment', _p('most_discussed'))
                    ),
                    'show' => array(5, 10, 15)
                )
            )
        );
        $aParentModule = $this->getParam('aParentModule');

        if ($aParentModule === null && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            if (Phpfox::getUserParam('poll.can_create_poll')) {
                sectionMenu(_p('add_new_poll'), url('/poll/add'));
            }
        }
        $aBrowseParams = array(
            'module_id' => 'poll',
            'alias' => 'poll',
            'field' => 'poll_id',
            'table' => Phpfox::getT('poll'),
            'hide_view' => array('pending', 'my')
        );

        switch ($sView) {
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND poll.user_id = ' . (int)Phpfox::getUserId());
                break;
            case 'pending':
                Phpfox::isUser(true);
                Phpfox::getUserParam('poll.poll_can_moderate_polls', true);
                $this->search()->setCondition('AND poll.view_id = 1');
                break;
            default:
                if ($bIsProfile === true) {
                    $this->search()->setCondition('AND poll.item_id = 0 AND poll.user_id = ' . (int)$aUser['user_id'] . ' AND poll.view_id IN(' . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ') AND poll.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : \Phpfox::getService('core')->getForBrowse($aUser)) . ')');
                } else {
                    $this->search()->setCondition('AND poll.item_id = 0 AND poll.view_id = 0 AND poll.privacy IN(%PRIVACY%)');
                }
                $this->search()->setCondition('AND (poll.close_time = 0 OR poll.close_time > '.PHPFOX_TIME.')');
                break;
        }

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('poll.poll_paging_mode', 'loadmore'))
            ->execute();

        $iCnt = $this->search()->browse()->getCount();
        $aPolls = $this->search()->browse()->getRows();

        if (defined('PHPFOX_IS_USER_PROFILE')) {
            $this->template()->setMeta('keywords', _p('full_name_s_polls', array('full_name' => $aUser['full_name'])));
            $this->template()->setMeta('description',
                _p('full_name_s_polls_on_site_title_full_name_has_total_poll_s', array(
                        'full_name' => $aUser['full_name'],
                        'site_title' => Phpfox::getParam('core.site_title'),
                        'total' => $iCnt
                    )
                )
            );
        }

        Phpfox::getLib('pager')->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $iCnt,
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));

        // check if user has voted here already
        //$aVotedPollsByUser = \Phpfox::getService('poll')->getVotedAnswersByUser(Phpfox::getUserId());
        // check editing permissions
        foreach ($aPolls as $iKey => &$aPoll) {
            // is guest the owner?
            $aPoll['bCanEdit'] = \Phpfox::getService('poll')->bCanEdit($aPoll['user_id']);
            $aPoll['bCanDelete'] = \Phpfox::getService('poll')->bCanDelete($aPoll['user_id']);

            $this->template()->setMeta('keywords', $this->template()->getKeywords($aPoll['question']));
        }

        Phpfox::getService('poll')->buildMenu();

        $this->template()->setTitle((defined('PHPFOX_IS_USER_PROFILE') ? _p('full_name_s_polls_upper',
            array('full_name' => $aUser['full_name'])) : _p('polls')))
            ->setBreadCrumb(_p('all_polls'), (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'],
                'poll') : $this->url()->makeUrl('poll')))
            ->setMeta('description', Phpfox::getParam('poll.poll_meta_description'))
            ->setMeta('keywords', Phpfox::getParam('poll.poll_meta_keywords'))
            ->assign(array(
                    'aPolls' => $aPolls
                )
            )
            ->setPhrase([
                'are_you_sure_you_want_to_delete_this_poll'
            ]);
        $aModerationMenu = [];
        $bShowModerator = false;
        if ($sView == 'pending') {
            if (Phpfox::getUserParam('poll.poll_can_moderate_polls')) {
                $aModerationMenu[] = array(
                    'phrase' => _p('approve'),
                    'action' => 'approve'
                );
            }
        } elseif (Phpfox::getUserParam('poll.can_feature_poll')) {
            $aModerationMenu[] = array(
                'phrase' => _p('feature'),
                'action' => 'feature'
            );
            $aModerationMenu[] = array(
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            );
        }
        if (Phpfox::getUserParam('poll.poll_can_delete_others_polls')) {
            $aModerationMenu[] = array(
                'phrase' => _p('delete'),
                'action' => 'delete'
            );
        }
        if (count($aModerationMenu)) {
            $this->setParam('global_moderation', array(
                    'name' => 'poll',
                    'ajax' => 'poll.moderation',
                    'menu' => $aModerationMenu
                )
            );
            $bShowModerator = true;
        }
        $this->template()->assign([
            'bShowModerator' => $bShowModerator,
            'sView' => $sView,
        ]);

        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_index_process_end')) ? eval($sPlugin) : false);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}