<?php

namespace Apps\Core_RSS\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Component;

class AddController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;
        if (($iId = $this->request()->getInt('id'))) {
            if (($aFeed = Phpfox::getService('rss')->getForEdit($iId))) {
                $bIsEdit = true;
                $this->template()->assign('aForms', $aFeed);
            }
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if (!Phpfox::isTechie()) {
                $aVals = array_merge($aVals, ['product_id' => 'phpfox', 'module_id' => 'core']);
            }

            if ($bIsEdit && isset($aFeed)) {
                if (Phpfox::getService('rss.process')->update($aFeed['feed_id'], $aVals)) {
                    $this->url()->send('admincp.rss.add', array('id' => $aFeed['feed_id']),
                        _p('feed_successfully_updated'));
                }
            } else {
                if (Phpfox::getService('rss.process')->add($aVals)) {
                    $this->url()->send('admincp.rss', null, _p('feed_successfully_added'));
                }
            }
        }

        if (Phpfox::getParam('core.enabled_edit_area')) {
            $this->template()->setHeader(array(
                    'editarea/edit_area_full.js' => 'static_script',
                    '<script type="text/javascript">				
						editAreaLoader.init({
							id: "php_view_code"	
							,start_highlight: true
							,allow_resize: "both"
							,allow_toggle: true
							,word_wrap: false
							,language: "en"
							,syntax: "php"
							,allow_resize: "y"
						});		

						editAreaLoader.init({
							id: "php_group_code"	
							,start_highlight: true
							,allow_resize: "both"
							,allow_toggle: true
							,word_wrap: false
							,language: "en"
							,syntax: "php"
							,allow_resize: "y"
						});		
					</script>'
                )
            );
        }

        $this->template()->setTitle((($bIsEdit && isset($aFeed)) ? _p('editing_feed') . ': #' . $aFeed['feed_id'] : _p('add_new_feed')))
            ->setBreadCrumb((($bIsEdit && isset($aFeed)) ? _p('editing_feed') . ': #' . $aFeed['feed_id'] : _p('add_new_feed')),
                null, true)
            ->assign(array(
                    'bIsEdit' => $bIsEdit,
                    'aGroups' => Phpfox::getService('rss.group')->getDropDown(),
                    'aLanguages' => Phpfox::getService('language')->getAll()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}
