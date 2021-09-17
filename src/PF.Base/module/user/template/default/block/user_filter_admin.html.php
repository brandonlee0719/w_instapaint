<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="get" class="form-search" action="{url link='admincp.user.browse'}">
<div class="panel panel-default">
    <div class="panel-body">
            <div class="clearfix row">
                <div class="form-group col-sm-3">
                    <label >{_p var='search'}</label>
                    {filter key='keyword' placeholder='search'}
                </div>
                <div class="form-group col-sm-3">
                    <label>{_p var='within'}</label>
                    {filter key='type'}
                </div>
                <div class="form-group col-sm-3">
                    <label >{_p var='group'}</label>
                    {filter key='group'}
                </div>
                <div class="form-group col-sm-3">
                    <label >{_p var='gender'}</label>
                    {filter key='gender'}
                </div>
                <div id="js_admincp_search_options" class="hide">
                    <div class="form-group col-sm-3">
                        <label >{_p var='location'}</label>
                        {filter key='country'}
                        {module name='core.country-child' admin_search=1 country_child_filter=true country_child_type='browse'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='city'}</label>
                        {filter key='city'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='zip_postal_code'}</label>
                        {filter key='zip'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='ip_address'}</label>
                        {filter key='ip'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='age_group'}</label>
                        {filter key='from'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label>&nbsp;</label>
                        {filter key='to'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='show_members'}</label>
                        {filter key='status'}
                    </div>
                    <div class="form-group col-sm-3">
                        <label >{_p var='sort_results_by'}</label>
                        {filter key='sort'}
                    </div>
                    {assign var='sFormGroupClass' value='col-sm-3'}
                    {foreach from=$aCustomFields item=aCustomField}
                    {template file='custom.block.foreachcustom'}
                    {/foreach}
                </div>
            </div>
            <div class="form-btn-group">
                <button type="submit" class="btn btn-primary" name="search[submit]">{_p var='search'}</button>
                <a class="btn btn-info" href="{url link='admincp.user.browse'}">{_p var='reset'}</a>
                <button type="button" class="btn btn-link" rel="{_p var='view_less_search_options'}" onclick="$('#js_admincp_search_options').toggleClass('hide'); var text = $(this).text(); $(this).text($(this).attr('rel')); $(this).attr('rel', text)">
                    {_p var='view_more_search_options'}
                </button>
            </div>
    </div>
</div>
</form>