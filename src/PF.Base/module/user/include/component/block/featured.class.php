<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Featured
 */
class User_Component_Block_Featured extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        list($aUsers, $iTotal) = Phpfox::getService('user.featured')->get();

        if (empty($aUsers) || $aUsers === false) {
            return false;
        }
        if (count($aUsers) < $iTotal) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_all') => $this->url()->makeUrl('user.browse', array('view' => 'featured'))
                )
            ));
        }
        $this->template()->assign(array(
                'aFeaturedUsers' => $aUsers,
                'sHeader' => _p('featured_members'),
            )
        );

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('user.component_block_featured_clean')) ? eval($sPlugin) : false);
    }
}
