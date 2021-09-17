<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Language
 * @version 		$Id: add.html.php 1161 2009-10-09 07:42:41Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $sCachePhrase}
<div class="p_4">
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php'}</b>:</div>
		<div><input type="text" name="php" value="_p('{$sCachePhrase}')" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php_single_quoted'}</b>:</div>
		<div><input type="text" name="php" value="' . _p('{$sCachePhrase}') . '" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>	
	<div class="p_4">	
		<div class="go_left t_right" style="width:150px;"><b>{_p var='php_double_quoted'}</b>:</div>
		<div><input type="text" name="php" value="&quot; . _p('{$sCachePhrase}') . &quot;" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>		
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='html'}</b>:</div>
		<div><input type="text" name="html" value="{literal}{{/literal}phrase var='{$sCachePhrase}'{literal}}{/literal}" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='js'}</b>:</div>
		<div><input type="text" name="html" value="oTranslations['{$sCachePhrase}']" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>	
	<div class="p_4">
		<div class="go_left t_right" style="width:150px;"><b>{_p var='text'}</b>:</div>
		<div><input type="text" name="html" value="{$sCachePhrase}" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>		
</div>
{/if}
{$sCreateJs}
<form class="form" method="post" action="{url link='admincp.language.phrase.add' last-module=$sLastModuleId}" id="js_phrase_form" onsubmit="{$sGetJsForm}">
    {token}
    {if $sReturn}
    <div><input type="hidden" name="return" value="{$sReturn}" /></div>
    {/if}
    {if $sVar}
    <div><input type="hidden" name="val[is_help]" value="true" /></div>
    {/if}
<!--begin-->
    <div class="panel panel-default panel-group" id="select_database">
            <div class="panel-heading">
                <div class="panel-title">
                    {_p var='add_phrase'}
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="">
                        <label for="quick_add_input" class="radio-inline" onclick="$('#detail_add').removeClass('in');$('#quick_add').addClass('in');">
                            <input data-toggle="pf-panel" data-cmd="check_box_hide" data-show="class_quick_add" href="#quick_add" type="radio" id="quick_add_input" value="1" name="val[type]" checked="checked"/>
                            {_p var='Quick'}
                        </label>
                        <label class="radio-inline" onclick="$('#detail_add').addClass('in');$('#quick_add').removeClass('in');" for="detail_add_input">
                            <input data-toggle="pf-panel" data-cmd="check_box_hide" href="#detail_add" data-show="class_detail_add" type="radio" id="detail_add_input" value="2" name="val[type]">
                            {_p var="Detail"}</label>
                    </div>
                </div>
                <div id="quick_add" class="panel-collapse collapse in check_box_hide class_quick_add">
                    <div class="form-group">
                        <label for="var_name">{_p var='Phrase'}</label>
                        <input type="text" name="val[phrase]" value="" size="40" id="var_name" class="form-control" autofocus/>
                    </div>
                </div>
                <div id="detail_add" class="panel-collapse collapse check_box_hide class_detail_add">
                    <div class="form-group">
                        <label for="var_name">{_p var='varname'}</label>
                        <input type="text" name="val[var_name]" value="{$sVar}" size="40" id="var_name" maxlength="100" class="form-control" autofocus/>
                        {help var='admincp.language_add_phrase_varname'}
                    </div>
                    {field_language phrase='var_name' label='text' field='text' help='admincp.language_add_phrase_text' format='val[text][' rows=4 type='textarea'}
                </div>
            </div>
            <div class="panel-footer">
                <button class="btn btn-primary" type="submit">{_p var='submit'}</button>
            </div>
        </div>
</form>