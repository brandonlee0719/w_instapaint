<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Forums\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        if (($aOrder = $this->request()->getArray('order')) && Phpfox::getService('forum.process')->updateOrder($aOrder)) {
            $this->url()->send('admincp.app', ['id' => 'Core_Forums'], _p('forum_order_successfully_updated'));
        }

        if ($iId = $this->request()->getInt('view')) {
            $sUrl = Phpfox::getService('forum')->getForumUrl($iId);
            $this->url()->send("forum/$iId/$sUrl");
        }

        $this->template()->setTitle(_p('manage_forums'))
            ->setBreadCrumb(_p('manage_forums'), $this->url()->makeUrl('admincp.forum'))
            ->setPhrase(array(
                    'global_moderator_permissions',
                    'moderator_permissions',
                    'cancel'
                )
            )
            ->setHeader(array(
                    'jquery/ui.js' => 'static_script',
                )
            )
            ->assign(array(
                    'sForumList' => Phpfox::getService('forum')->getAdminCpList(),
                    'sPath' => $this->url()->makeUrl('admincp.forum'),
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}