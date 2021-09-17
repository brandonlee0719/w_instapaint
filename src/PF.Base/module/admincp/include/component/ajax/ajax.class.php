<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Ajax
 */
class Admincp_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function deleteMeta()
	{
		Phpfox::isAdmin(true);
		
		foreach ((array) $this->get('id') as $iId)  {
            Phpfox::getService('admincp.seo.process')->deleteMeta($iId);
			$this->remove('#js_id_row_' . $iId);			
		}
		$this->call('$(\'#js_check_box_all\').attr(\'checked\', false);');
	}	
	
	public function addMeta()
	{
		Phpfox::isAdmin(true);
		
		if (($iId = Phpfox::getService('admincp.seo.process')->addMeta($this->get('val'))))
		{
			$aVals = $this->get('val');	
			
			$sHtml = '<tr class="js_nofollow_row is_new_row" id="js_id_row_' . $iId. '">';
			$sHtml .= '<td><input type="checkbox" name="id[]" class="checkbox" value="' . $iId. '" id="js_id_row' . $iId. '" /></td>';
			$sHtml .= '<td>' . ($aVals['type_id'] == '1' ? _p('description') : ($aVals['type_id'] == '2' ? _p('title') : _p('keyword'))) . '</td>';
			$sHtml .= '<td>' . Phpfox::getService('admincp.seo')->getUrl($aVals['url']) . '</td>';
			$sHtml .= '<td><textarea name="val[' . $iId. '][content]" cols="30" rows="4" style="height:30px;">' . $aVals['content'] . '</textarea></td>';
			$sHtml .= '<td>' . Phpfox::getLib('date')->convertTime(PHPFOX_TIME) . '</td>';
			$sHtml .= '</tr>';
			
			$this->call('$(\'#js_meta_form\')[0].reset();');
			$this->show('#js_meta_holder');
			$this->append('#js_meta_holder_table', $sHtml);
			$this->call('var bHasTrClass = false; $(\'.js_nofollow_row\').each(function(){ if ($(this).hasClass(\'is_new_row\')) { $(this).removeClass(\'is_new_row\'); return false; } if ($(this).hasClass(\'tr\')) { bHasTrClass = true; } else { bHasTrClass = false; } }); if (!bHasTrClass) { $(\'#js_id_row_' . $iId. '\').addClass(\'tr\'); }');
			
			$this->alert(_p('successfully_added_a_new_custom_element_dot'));
		}		
	}
	
	public function nofollow()
	{
		Phpfox::isAdmin(true);
		
		if (($iId = Phpfox::getService('admincp.seo.process')->addNoFollow($this->get('val'))))
		{
			$aVals = $this->get('val');
			
			$sHtml = '<tr class="js_nofollow_row is_new_row" id="js_id_row_' . $iId. '">';
			$sHtml .= '<td><input type="checkbox" name="id[]" class="checkbox" value="' . $iId. '" id="js_id_row' . $iId. '" /></td>';
			$sHtml .= '<td>' . Phpfox::getService('admincp.seo')->getUrl($aVals['url']) . '</td>';
			$sHtml .= '<td>' . Phpfox::getLib('date')->convertTime(PHPFOX_TIME) . '</td>';
			$sHtml .= '</tr>';
			
			$this->val('#js_nofollow_url', '');
			$this->show('#js_nofollow_holder');
			$this->append('#js_nofollow_holder_table', $sHtml);
			$this->call('var bHasTrClass = false; $(\'.js_nofollow_row\').each(function(){ if ($(this).hasClass(\'is_new_row\')) { $(this).removeClass(\'is_new_row\'); return false; } if ($(this).hasClass(\'tr\')) { bHasTrClass = true; } else { bHasTrClass = false; } }); if (!bHasTrClass) { $(\'#js_id_row_' . $iId. '\').addClass(\'tr\'); }');
			
			$this->alert(_p('successfully_added_a_new_url'));
		}
	}
	
	public function deleteNoFollow()
	{
		Phpfox::isAdmin(true);
		
		foreach ((array) $this->get('id') as $iId)
		{
            Phpfox::getService('admincp.seo.process')->deleteNoFollow($iId);
			$this->remove('#js_id_row_' . $iId);			
		}
		$this->call('$(\'#js_check_box_all\').attr(\'checked\', false);');
	}
	
	public function buildSearchValues()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);		
		
		$this->call('aAdminCPSearchValues = ' . json_encode(Phpfox::getService('admincp.setting')->getForSearch()) . ';');
		$this->call('$("#admincp_search_input").keyup();');
	}
	
	public function updateBlockActivity()
	{		
		if (Phpfox::getService('admincp.block.process')->updateActivity($this->get('id'), $this->get('active')))
		{
			
		}
	}	
	
	public function blockOrdering()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		if ($aVals = $this->get('val'))
		{
			if (Phpfox::getService('admincp.block.process')->updateOrder($aVals['ordering'], (isset($aVals['style_id']) ? (int) $aVals['style_id'] : null)))
			{

			}			
		}		
	}
	
	public function getBlocks()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
		Phpfox::getBlock('admincp.block.setting');		

		$this->html('#js_setting_block', $this->getContent(false));
		$this->show('#content_editor_text');
		$this->show('#js_editing_block');
		$this->html('#js_editing_block_text', ($this->get('m_connection') == '' ? _p('site_wide') : $this->get('m_connection')));
		$this->call('$.scrollTo(0);');		
		$this->call('$Core.loadInit();');
		$this->call('Core_drag.init({table: \'.js_drag_drop\', ajax: \'admincp.blockOrdering\'});');
	}
	
	public function removeSettingFromArray()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('admincp.has_admin_access', true);
        Phpfox::getService('admincp.setting.process')->removeSettingFromArray($this->get('setting'), $this->get('value'));
	}
	
	public function checkProductVersions()
	{
		Phpfox::getService('admincp.product.process')->checkProductVersions();
	}
	
	public function updateModuleActivity()
	{
		Phpfox::getService('admincp.module.process')->updateActivity($this->get('id'), $this->get('active'));
	}

	public function updateMenuActivity()
	{
		Phpfox::getService('admincp.menu.process')->updateActivity($this->get('id'), $this->get('active'));
	}
	
	public function componentFeedActivity()
	{
		Admincp_Service_Component_Process::instance()->updateActivity($this->get('id'), $this->get('active'));
	}

    /**
     * @deprecated 4.6.0
     */
	public function updateAutoSaveSettings()
    {
        storage()->del('admincp/settings/autosave');
        storage()->set('admincp/settings/autosave', (int) $this->get('active'));
        $this->call('$Core.closeAjaxMessage();');
    }
}