<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Quizzes\Controller;

use Phpfox;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Quiz
 * @version        4.5.3
 */
class IndexController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle)) {
            Phpfox::getService('core')->getLegacyItem(array(
                    'field' => array('quiz_id', 'title'),
                    'table' => 'quiz',
                    'redirect' => 'quiz',
                    'title' => $sLegacyTitle
                )
            );
        }

        Phpfox::getUserParam('quiz.can_access_quiz', true);

        if (($iRedirect = $this->request()->getInt('redirect')) && ($sUrl = Phpfox::getService('quiz.callback')->getFeedRedirect($iRedirect))) {
            $this->url()->forward($sUrl);
        }

        if (defined('PHPFOX_IS_AJAX_CONTROLLER')) {
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        } else {
            $bIsProfile = $this->getParam('bIsProfile');
            if ($bIsProfile === true) {
                $aUser = $this->getParam('aUser');
            } else {
                //TODO $aUser use in many place. Check it.
                $aUser = [];
            }

        }

        if ($this->request()->getInt('req2') > 0) {
            return Phpfox_Module::instance()->setController('quiz.view');
        }
        $aParentModule = $this->getParam('aParentModule');

        if ($aParentModule === null && (!defined('PHPFOX_IS_USER_PROFILE') || (defined('PHPFOX_IS_USER_PROFILE') && Phpfox::getUserId() == $aUser['user_id']))) {
            if (Phpfox::getUserParam('quiz.can_create_quiz')) {
                sectionMenu(_p('add_new_quiz'), url('/quiz/add'));
            }
        }
        $sView = $this->request()->get('view');

        $this->search()->set(array(
                'type' => 'quiz',
                'field' => 'q.quiz_id',
                'ignore_blocked' => true,
                'search_tool' => array(
                    'table_alias' => 'q',
                    'search' => array(
                        'action' => (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'],
                            array('quiz', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('quiz',
                            array('view' => $this->request()->get('view')))),
                        'default_value' => _p('search_quizzes'),
                        'name' => 'search',
                        'field' => 'q.title'
                    ),
                    'sort' => array(
                        'latest' => array('q.time_stamp', _p('latest')),
                        'most-viewed' => array('q.total_view', _p('most_viewed')),
                        'most-liked' => array('q.total_like', _p('most_liked')),
                        'most-talked' => array('q.total_comment', _p('most_discussed'))
                    ),
                    'show' => array(10, 20, 30)
                )
            )
        );
        $aModerationMenu = array();
        if (Phpfox::getUserParam('quiz.can_delete_others_quizzes')) {
            $aModerationMenu[] = array(
                'phrase' => _p('delete'),
                'action' => 'delete'
            );
        }
        switch ($sView) {
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND q.user_id = ' . (int)Phpfox::getUserId());
                break;
            case 'pending':
                Phpfox::isUser(true);
                Phpfox::getUserParam('quiz.can_approve_quizzes', true);
                $this->search()->setCondition('AND q.view_id = 1');
                if (Phpfox::getUserParam('quiz.can_approve_quizzes')) {
                    $aModerationMenu[] = array(
                        'phrase' => _p('approve'),
                        'action' => 'approve'
                    );
                }
                break;
            default:
                if ($this->getParam('bIsProfile') === true) {
                    $this->search()->setCondition('AND q.view_id IN(' . ($aUser['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ') AND q.user_id = ' . (int)$aUser['user_id'] . ' AND  q.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($aUser)) . ')');
                } else {
                    $this->search()->setCondition('AND q.view_id = 0 AND q.privacy IN(%PRIVACY%)');
                }
                break;
        }
        if (Phpfox::getUserParam('quiz.can_feature_quiz') && $sView != 'pending') {
            $aModerationMenu[] = array(
                'phrase' => _p('feature'),
                'action' => 'feature'
            );
            $aModerationMenu[] = array(
                'phrase' => _p('un_feature'),
                'action' => 'un-feature'
            );
        }
        $aBrowseParams = array(
            'module_id' => 'quiz',
            'alias' => 'q',
            'field' => 'quiz_id',
            'table' => Phpfox::getT('quiz'),
            'hide_view' => array('pending', 'my')
        );

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->params($aBrowseParams)
            ->setPagingMode(Phpfox::getParam('quiz.quiz_paging_mode', 'loadmore'))
            ->execute();

        $iCnt = $this->search()->browse()->getCount();
        $aQuizzes = $this->search()->browse()->getRows();

        foreach ($aQuizzes as $aQuiz) {
            $this->template()->setMeta('keywords', $this->template()->getKeywords($aQuiz['title']));
        }

        Phpfox::getLib('pager')->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $iCnt,
            'paging_mode' => $this->search()->browse()->getPagingMode()
        ));

        Phpfox::getService('quiz')->buildSectionMenu();

        $bCanModerate = (boolean)count($aModerationMenu);
        $this->template()->setTitle((defined('PHPFOX_IS_USER_PROFILE') ? _p('full_name_s_quizzes',
            array('full_name' => $aUser['full_name'])) : _p('quizzes')))
            ->setBreadCrumb(_p('quizzes'),
                (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'],
                    'quiz') : $this->url()->makeUrl('quiz')))
            ->setMeta('keywords', Phpfox::getParam('quiz.quiz_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('quiz.quiz_meta_description'))
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                )
            )
            ->setPhrase(array(
                    'are_you_sure_you_want_to_delete_this_quiz'
                )
            )
            ->assign(array(
                    'aQuizzes' => $aQuizzes,
                    'bIsProfile' => (defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE) ? true : false,
                    'bCanModerate' => $bCanModerate,
                    'sView' => $sView
                )
            );
        if ($bCanModerate) {
            $this->setParam('global_moderation', array(
                    'name' => 'quiz',
                    'ajax' => 'quiz.moderation',
                    'menu' => $aModerationMenu
                )
            );
        }
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('quiz.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}