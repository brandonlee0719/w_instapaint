<?php
namespace Apps\Core_Blogs\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class ProfileController
 * @package Apps\Core_Blogs\Controller
 */
class ProfileController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->setParam('bIsProfile', true);

        $aUser = $this->getParam('aUser');

        $this->template()->setMeta('keywords', _p('full_name_s_blogs', array('full_name' => $aUser['full_name'])));
        $this->template()->setMeta('description', _p('full_name_s_blogs_on_site_title',
            array('full_name' => $aUser['full_name'], 'site_title' => Phpfox::getParam('core.site_title'))));

        Phpfox::getComponent('blog.index', array('bNoTemplate' => true), 'controller');
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}
