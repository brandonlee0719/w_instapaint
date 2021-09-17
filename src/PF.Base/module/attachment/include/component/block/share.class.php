<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Attachment_Component_Block_Share extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $mAttachmentShare = $this->getParam('attachment_share', null);
        $id = $this->getParam('id');

        if ($mAttachmentShare === null) {
            return false;
        }

        if (!is_array($mAttachmentShare)) {
            $mAttachmentShare = array('type' => $mAttachmentShare);
        }

        if (!isset($mAttachmentShare['inline'])) {
            $mAttachmentShare['inline'] = false;
        }

        $this->template()->assign(array(
                'aAttachmentShare' => $mAttachmentShare,
                'id' => $id,
                'holderId' => uniqid('attachment_holder_')
            )
        );

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('attachment.component_block_share_clean')) ? eval($sPlugin) : false);
//
//		$this->clearParam('attachment_share');
    }
}
