<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Attachment_Component_Controller_Admincp_Index
 */
class Attachment_Component_Controller_Admincp_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (($sDeleteId = $this->request()->get('delete'))) {
            if (Phpfox::getService('attachment.process')->deleteType($sDeleteId)) {
                $this->url()->send('admincp.attachment', null, _p('attachment_successfully_deleted'));
            }
        }

        $this->template()->setTitle(_p('attachments_title'))
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('attachments_title'), $this->url()->makeUrl('admincp.attachment'))
            ->setBreadCrumb(_p('attachment_file_types'))
            ->setSectionTitle(_p('attachment_file_types'))
            ->assign(array(
                    'aRows' => Phpfox::getService('attachment.type')->get()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('attachment.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}
