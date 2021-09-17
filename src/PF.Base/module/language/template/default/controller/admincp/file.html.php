<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Language
 * @version 		$Id: file.html.php 225 2009-02-13 13:24:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form class="form" method="post" action="{url link="admincp.language.file"}">
<div class="panel panel-default">
    <div class="panel-body">
        {_p var='export'}
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <label for="language_id">{_p var='language_package'}</label>
            <select name="val[language_id]" id="language_id" class="form-control">
                {foreach from=$aLanguages item=aLanguage}
                <option value="{$aLanguage.language_id}">{$aLanguage.title}</option>
                {/foreach}
            </select>
            {help var='admincp.language_file_package'}
        </div>
        <div class="form-group">
            <label for="product_id">{_p var='product'}</label>
            <select name="val[product_id]" id="product_id" class="form-control">
                {foreach from=$aProducts item=aProduct}
                <option value="{$aProduct.product_id}">{$aProduct.title}</option>
                {/foreach}
            </select>
            {help var='admincp.language_file_product'}
        </div>
        <div class="form-group">
            <label for="file_extension">{_p var='download_file_format'}</label>
            <select name="val[file_extension]" id="file_extension" class="form-control">
                {foreach from=$aArchives item=aArchives}
                <option value="{$aArchives}">.{$aArchives}</option>
                {/foreach}
            </select>
            {help var='admincp.language_file_extension'}
        </div>
        <input type="submit" value="{_p var='download'}" class="btn" />
    </div>
</div>
</form>

<form class="form" method="post" action="{url link="admincp.language.file"}" enctype="multipart/form-data">
{token}
<div><input type="hidden" name="import" value="true" /></div>
<div class="panel panel-default">
    <div class="panel-body">
        {_p var='import'}
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <label for="download">{_p var='import_language_package'}</label>
            {_p var='either_select_language_package'}:
            <div class="p_4">
                <select id="download" name="download" size="10" style="width:400px;" class="form-control">
                    {foreach from=$aImports item=aImport}
                    <option value="{$aImport.title}">{$aImport.title}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="file">{_p var='upload_one_from_your_computer'}</label>
            <div class="p_4">
                <input type="file" name="file" id="file" class="form-control"/>
                <div class="p_4">
                    {_p var='valid_file_extensions'}: {$sSupported}
                </div>
            </div>
            {help var='admincp.language_file_import_package'}
        </div>
        <div class="form-group">
            <label for="missing_phrases">{_p var='import_missing_phrases_only'}</label>
            <div class="radio">
                <label><input type="radio" id="missing_phrases" name="missing_phrases" value="0" checked="checked" />{_p var='no'}</label>
                <label><input type="radio" id="missing_phrases" name="missing_phrases" value="1" />{_p var='yes'}</label>
                {help var='admincp.language_file_missing_phrases'}
            </div>
        </div>
        <input type="submit" value="{_p var='upload'}" class="btn" />
    </div>
</div>
</form>