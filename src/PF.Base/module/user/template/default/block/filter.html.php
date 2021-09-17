<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="GET" action="{if isset($aCallback.url_home)}{url link=$aCallback.url_home view=$sView}{else}{url link='user.browse' view=$sView}{/if}" class="form">

    {if isset($aCallback.url_home)}
    <input type="hidden" name="url_home" value="{$aCallback.url_home}" />
    {/if}

    {if Phpfox::getUserParam('user.can_search_user_gender')}
    <div class="form-group">
        <label>{_p var='browse_for'}</label>
        {filter key='gender'}
    </div>
    {/if}

    {if Phpfox::getUserParam('user.can_search_user_age')}
    <div class="form-group">
        <label>{_p var='between_ages'}</label>
        {filter key='from'}&nbsp;{filter key='to'}
    </div>
    {/if}

    <div class="form-group">
        <label>{_p var='located_within'}</label>
        {filter key='country'}
        {module name='core.country-child' country_child_filter=true country_child_type='browse'}
    </div>

    <div class="form-group">
        <label>{_p var='city'}</label>
        {filter key='city'}
    </div>
	
	{if Phpfox::getUserParam('user.can_search_by_zip')}
    <div class="form-group">
        <label>{_p var='zip_postal_code'}</label>
        {filter key='zip'}
    </div>
	{/if}

    <div class="form-group">
        <label>{_p var='keywords'}</label>
        {filter key='keyword'}
        <p class="help-block" style="display:none;">
            {_p var='within'}: {filter key='type'}
        </p>
    </div>

	<ul id="js_user_browse_advanced_link" class="form-group">
        {if isset($bShowAdvSearch) && $bShowAdvSearch}
		<li><a class="btn btn-default" href="#" onclick="$('.main_search_browse_button').toggle(); $('#js_user_browse_advanced').toggleClass('active'); return false;" id="user_browse_advanced_link">
                <span class="main_search_browse_button">{_p var='view_advanced_filters'}</span>
                <span class="main_search_browse_button" style="display: none">{_p var='close_advanced_filters'}</span>
            </a></li>
        {/if}
		{if isset($bIsInSearchMode) && $bIsInSearchMode}
		<li><a href="#"><a href="{url link='user.browse'}">{_p var='reset_browse_criteria'}</a></a></li>
		{/if}
	</ul>
		
	<div class="main_search_browse_button">
		<input type="submit" value="{_p var='search'}" class="btn btn-primary" name="search[submit]" />
	</div>
	
	<div id="js_user_browse_advanced">
		<div class="user_browse_content">
			<div id="browse_custom_fields_popup_holder">
			    {foreach from=$aCustomFields name=customfield item=aCustomField}
				{if isset($aCustomField.fields)}
					{template file='custom.block.foreachcustom'}
				{/if}
			    {/foreach}
			</div>
			{if count($aForms)}
			{literal}
			<script type="text/javascript">
				$Behavior.user_filter_1 = function()
				{
					var iBrowseCnt = 0;
					$('#js_block_border_user_filter .menu li').each(function()
					{
						iBrowseCnt++;
						if (iBrowseCnt == 1)
						{
							$(this).removeClass('active');
						}
						else
						{
							$(this).addClass('active');
						}
					});
				};
			</script>
			{/literal}
			{/if}

            <div class="form-group" style="display:none;">
                <label>{_p var='sort_results_by'}</label>
                {filter key='sort'} {filter key='sort_by'}
            </div>
			<div class="form-group">
			    <input type="submit" value="{_p var='search'}" class="btn btn-primary" name="search[submit]" />
			</div>
		</div>
	</div>
	
	{if isset($sCountryISO)}
		<script type="text/javascript">
			$Behavior.loadStatesAfterBrowse = function()
			{l}
				sCountryISO = "{$sCountryISO}";
				if(sCountryISO != "")
				{l}
					sCountryChildId = "{$sCountryChildId}";
					$.ajaxCall('core.getChildren', 'country_child_filter=true&country_child_type=browse&country_iso=' + sCountryISO + '&country_child_id=' + sCountryChildId);
				{r}
			{r}
		</script>
	{/if}
</form>