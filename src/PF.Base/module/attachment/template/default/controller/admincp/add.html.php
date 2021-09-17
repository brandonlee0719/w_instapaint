<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: add.html.php 979 2009-09-14 14:05:38Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form" method="post" action="{url link='admincp.attachment.add'}">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='add_an_attachment_type'}</div>
    </div>
    <div class="panel-body">
        {if $bIsEdit}
        <div><input type="hidden" name="id" value="{$aForms.extension}" /></div>
        {/if}
        <div class="form-group">
            <label for="extension">{_p var='extension'}</label>
            <input type="text" id="extension" name="val[extension]" value="{value id='extension' type='input'}" size="30" class="form-control"/>
        </div>
        <div class="form-group">
            <label for="mime_type">{_p var='mime_type'}</label>
            <input type="text" id="mime_type" name="val[mime_type]" value="{value id='mime_type' type='input'}" size="30" class="form-control"/>
        </div>
        <div class="form-group">
            <label for="is_image">{required}{_p var='is_image'}</label>
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="val[is_image]" value="1" {value type='radio' id='is_image' default='1'}/> {_p var='yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_image]" value="0" {value type='radio' id='is_image' default='0' selected='true'}/> {_p var='no'}</span>
            </div>
        </div>
        <div class="form-group">
            <label for="is_active">{required}{_p var='active'}</label>
            <div class="item_is_active_holder">
                <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}</span>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
    </div>
</div>
</form>