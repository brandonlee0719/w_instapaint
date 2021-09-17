<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Forums\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($sLegacyTitle = $this->request()->get('req2')) && !empty($sLegacyTitle)) {
            if (($sLegacyThread = $this->request()->get('req3')) && !empty($sLegacyThread) && !is_numeric($sLegacyTitle)) {
                Phpfox::getService('core')->getLegacyItem(array(
                        'field' => array('thread_id', 'title'),
                        'table' => 'forum_thread',
                        'redirect' => 'forum.thread',
                        'title' => $sLegacyThread
                    )
                );
            } else {
                $aForumParts = explode('-', $sLegacyTitle);
                if (isset($aForumParts[1])) {
                    Phpfox::getService('core')->getLegacyItem(array(
                            'field' => array('forum_id', 'name'),
                            'table' => 'forum',
                            'redirect' => 'forum',
                            'search' => 'forum_id',
                            'title' => $aForumParts[1]
                        )
                    );
                }
            }
        }

        Phpfox::getUserParam('forum.can_view_forum', true);

        $aParentModule = $this->getParam('aParentModule');

        if (Phpfox::getParam('core.phpfox_is_hosted') && empty($aParentModule)) {
            $this->url()->send('');
        } else {
            if (empty($aParentModule) && $this->request()->get('view') == 'new') {
                $aDo = explode('/', $this->request()->get('do'));
                if ($aDo[0] == 'mobile' || (isset($aDo[1]) && $aDo[1] == 'mobile')) {
                    Phpfox_Module::instance()->getComponent('forum.forum', array('bNoTemplate' => true), 'controller');
                    return null;
                }
            }
        }
        if ($this->request()->get('s')) {
            return Phpfox_Module::instance()->setController('forum.search');
        }
        if ($this->request()->get('req2') == 'topics' || $this->request()->get('req2') == 'posts') {
            return Phpfox_Module::instance()->setController('error.404');
        }

        $this->template()->setBreadCrumb(_p('forum'), $this->url()->makeUrl('forum'))
            ->setPhrase(array(
                    'provide_a_reply',
                    'adding_your_reply',
                    'are_you_sure',
                    'post_successfully_deleted',
                    'reply_multi_quoting'
                )
            );

        if ($aParentModule !== null) {
            Phpfox_Module::instance()->getComponent('forum.forum', array('bNoTemplate' => true), 'controller');

            return null;
        }

        if ($this->request()->getInt('req2') > 0) {
            return Phpfox_Module::instance()->setController('forum.forum');
        }

        if ($aParentModule === null) {
            Phpfox::getService('forum')->getSearchFilter();
        }

        $this->setParam('bIsForum', true);

        // $aIds = [];
        if (redis()->enabled() && redis()->exists('forums')) {
            $aForums = redis()->get_as_array('forums');
            foreach ($aForums as $key => $value) {
                $aForums[$key]['total_thread'] = redis()->get('threads/total_thread/' . $value['forum_id']);
                $aForums[$key]['total_post'] = redis()->get('threads/total_post/' . $value['forum_id']);

                if (isset($value['sub_forum'])) {
                    foreach ($value['sub_forum'] as $sub_key => $sub_value) {
                        $aForums[$key]['sub_forum'][$sub_key]['total_thread'] = redis()->get('threads/total_thread/' . $sub_value['forum_id']);
                        $aForums[$key]['sub_forum'][$sub_key]['total_post'] = redis()->get('threads/total_post/' . $sub_value['forum_id']);
                    }
                }
            }

        } else {
            $aForums = Phpfox::getService('forum')->live()->getForums();

            if (redis()->enabled()) {
                redis()->set('forums', $aForums);
            }
        }

        $this->template()->setTitle(_p('forum'))
            ->setMeta('keywords', Phpfox::getParam('forum.forum_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('forum.forum_meta_description'))
            ->assign(array(
                    'aForums' => $aForums,
                    'bHasCategory' => Phpfox::getService('forum')->hasCategory(),
                    'aCallback' => null,
                    'aSearchValues' => array(
                        'user' => '',
                        'adv_search' => 0
                    ),
                    'sForumList' => Phpfox::getService('forum')->getJumpTool(true, false, array(), true),
                    'bResult' => (Phpfox::getParam('forum.default_search_type', 'posts') == 'posts') ? true : false
                )
            );

        if ($aParentModule === null) {
            Phpfox::getService('forum')->buildMenu();
        }
        // set param for trending (tag) block
        $this->setParam('sTagType', 'forum');
        $this->setParam('bShowHashTag', false);
        $this->setParam('iTagDisplayLimit', Phpfox::getParam('forum.total_forum_tags_display'));
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}