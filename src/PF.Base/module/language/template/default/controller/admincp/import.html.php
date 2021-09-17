<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: import.html.php 4961 2012-10-29 07:11:34Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bImportPhrases}
	<div class="message">
		{_p var='importing_phrases_please_hold'}
	</div>
{else}
	{if Phpfox::getParam('core.is_auto_hosted')}		
	<form class="form" method="post" action="{url link='admincp.language.import'}" enctype="multipart/form-data">
        <div class="panel panel-default">
            <div class="panel-body">
                {_p var='import'}
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <label for="import" class="form-control">{_p var='file'}</label>
                    <input type="file" id="import" name="import" size="40" />
                </div>
                <input type="submit" value="Import" class="btn" />
            </div>
        </div>

	</form>
	{else}
    <div class="panel panel-default">
        <div class="panel-body">
            {_p var='manual_install'}
        </div>
        <div class="panel-footer">
            {if count($aNewLanguages)}
            <div class="table-responsive">
                <table class="table" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{_p var='title'}</th>
                            <th>{_p var='created_by'}</th>
                            <th style="width:100px;">{_p var='action'}</th>
                        </tr>
                    </thead>
                    <tbody>
                    {foreach from=$aNewLanguages key=iKey item=aLanguage}
                        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                            <td>
                                {if !empty($aLanguage.site)}<a href="{$aLanguage.site}" target="_blank">{/if}{$aLanguage.title|clean}</a>{if !empty($aLanguage.site)}</a>{/if}
                            </td>
                            <td>{if !empty($aLanguage.site)}<a href="{$aLanguage.site}" target="_blank">{/if}{if empty($aLanguage.created)}N/A{else}{$aLanguage.created}{/if}{if !empty($aLanguage.site)}</a>{/if}</td>
                            <td class="t_center"><a href="{url link='admincp.language.import' install=$aLanguage.language_id}" title="{_p var='click_to_install_this_language'}">{_p var='install'}</a></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            {else}
            <div class="table form-group">
                <div class="message">
                    {_p var='nothing_new_to_install'}
                </div>
            </div>
            {/if}
        </div>
    </div>
	{/if}
{/if}