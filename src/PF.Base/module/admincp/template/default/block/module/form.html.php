<?php 
/*might not use*/
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="form-group"{if !PHPFOX_IS_TECHIE} style="display:none;"{/if}>
	<label for="{if !$bUseClass}{$sModuleFormId}{/if}">{required}
	{if $bModuleFormRequired}{/if}{$sModuleFormTitle}
    </label>
    <select name="val[{$sModuleFormId}]" {if $bUseClass}class{else}id{/if}="{$sModuleFormId}" class="form-control">
        <option value="">{$sModuleFormValue}</option>
        {foreach from=$aModules key=sModule item=iModuleId}
            <option value="{$sModule}"{value type='select' id=''$sModuleFormId'' default=$sModule}>{translate var=$sModule prefix='module'}</option>
        {/foreach}
    </select>
</div>