<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package 		Phpfox_Component
 */
class Comment_Component_Controller_Admincp_Spam extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        //remove this feature
        Phpfox::getUserParam('comment.can_moderate_comments', true);

        $iPage = $this->request()->getInt('page');

        $aPages = array(20, 30, 40, 50);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt)
        {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }

        $aFilters = array(
            'search' => array(
                'type' => 'input:text',
                'search' => "AND ls.name LIKE '%[VALUE]%'"
            ),
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '20'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => array(
                    'time_stamp' => _p('last_activity'),
                    'rating ' => _p('rating')
                ),
                'default' => 'time_stamp',
                'alias' => 'cmt'
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => 'DESC'
            )
        );

        $oSearch = Phpfox_Search::instance()->set(array(
                'type' => 'comments',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );

        $oSearch->setCondition('AND cmt.view_id = 9');

        list($iCnt, $aComments) = Phpfox::getService('comment')->get('cmt.*', $oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $oSearch->getDisplay(), null, true);

        foreach ($aComments as $iKey => $aComment)
        {
            if (Phpfox::hasCallback($aComment['type_id'], 'getItemName'))
            {
                $aComments[$iKey]['item_name'] = Phpfox::callback($aComment['type_id'] . '.getItemName', $aComment['comment_id'], $aComment['owner_full_name']);
            }
        }

        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $oSearch->getDisplay(), 'count' => $oSearch->getSearchTotal($iCnt)));

        $this->template()
            ->setTitle(_p('comment_title'))
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('comment_title'), $this->url()->makeUrl('admincp.comment'))
            ->setBreadCrumb(_p('admin_menu_spam_comments'), null)
            ->setHeader('cache', array(
                    'comment.css' => 'style_css',
                    'pager.css' => 'style_css',
                )
            )
            ->assign(array(
                    'aComments' => $aComments,
                    'bIsCommentAdminPanel' => true
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('comment.component_controller_admincp_spam_clean')) ? eval($sPlugin) : false);
    }
}