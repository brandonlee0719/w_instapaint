<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="form-group">
    <label for="country_iso_custom">{_p var='location'}</label>
    {if isset($aAllCountries)}
        <select multiple="multiple" name="val[country_iso_custom][]" id="country_iso_custom" class="form-control">
            <option value="">{_p var='any'}
            {foreach from=$aAllCountries key=sIso item=aCountry}
                <option value="{$sIso}" {if isset($aForms) && isset($aForms.countries_list)}{foreach from=$aForms.countries_list item=sChosen} {if $sChosen == $sIso} selected="selected" {/if}{/foreach}{/if}> {$aCountry.name}
            {/foreach}
        </select>
    {else}
        {select_location value_title='phrase var=core.any' name='country_iso_custom'}
    {/if}
</div>

{if Phpfox::getParam('ad.advanced_ad_filters')}
	<div class="form-group tbl_province">
		<label for="sct_country_{$aCountry.country_iso}">{_p var='state_province'}</label>
        {foreach from=$aAllCountries item=aCountry}
            {if is_array($aCountry.children) && !empty($aCountry.children)}
                <div id="country_{$aCountry.country_iso}" class="select_child_country">
                    <div>{$aCountry.name}</div>
                    <select class="sct_child_country form-control" id="sct_country_{$aCountry.country_iso}" name="val[child_country][{$aCountry.country_iso}][]" multiple="multiple">
                        {foreach from=$aCountry.children item=aChild}
                            <option value="{$aChild.child_id}">{$aChild.name_decoded}</option>
                        {/foreach}
                    </select>
                </div>
            {/if}
        {/foreach}
	</div>
	
	<div class="form-group">
        <label for="postal_code">{_p var='postal_code'}</label>
        <input type="text" name="val[postal_code]" id='postal_code' value="{value type='input' id='postal_code'}">
        <p class="help-block">{_p var='separate_multiple_postal_codes_by_a_comma'}</p>
    </div>
		
    <div class="form-group">
        <label for="city_location">{_p var='city'}</label>
        <input type="text" name="val[city_location]" id='city_location' value="{value type='input' id='city_location'}">
        <p class="help-block">{_p var='separate_multiple_cities_by_a_comma'}.</p>
    </div>
{/if}
    <div class="form-group">
        <label for="gender">{_p var='gender'}</label>
        {select_gender value_title='phrase var=core.any'}
    </div>
    <div class="form-group">
        <label for="age_from">{_p var='age_group_between'}</label>
        <select name="val[age_from]" id="age_from" class="form-control">
            <option value="">{_p var='any'}</option>
            {foreach from=$aAge item=iAge}
                <option value="{$iAge}"{value type='select' id='age_from' default=$iAge}>{$iAge}</option>
            {/foreach}
        </select>
        <span id="js_age_to">
            {_p var='and'}
            <select name="val[age_to]" id="age_to" class="form-control">
            <option value="">{_p var='any'}</option>
            {foreach from=$aAge item=iAge}
                <option value="{$iAge}"{value type='select' id='age_to' default=$iAge}>{$iAge}</option>
            {/foreach}
            </select>
        </span>
    </div>