<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Page
 * @version 		$Id: ajax.class.php 225 2009-02-13 13:24:59Z Raymond_Benc $
 */
class Page_Component_Ajax_Admincp_Ajax extends Phpfox_Ajax
{	
	public function addUrl()
	{
		$this->call("if ($('#title_url').val() == '') { $('#js_url_table').show(); $('#title_url').val('" . Phpfox::getService('page')->prepareTitle($this->get('title')) . "'); }");
	}
	public function checkUrl()
    {
        $this->call('$(\'#js_url_table\').find(\'.js_warning\').remove();');
        $oldUrl = $this->get('title_url');
        $formUrl = $this->get('old_url');
        $newUrl = Phpfox::getService('page')->prepareTitle($oldUrl);
        if ($oldUrl != $newUrl && $oldUrl != $formUrl) {
            $sText = _p('the_url_original_url_is_already_existed_we_recommend_you_change_this_url_as_following',['original_url' => $oldUrl]);
            $this->call('$(\'#js_url_table\').append(\'<div class="text-danger js_warning">'.$sText.'</div>\').find(\'#title_url\').val(\''.$newUrl.'\');');
        }
    }
}