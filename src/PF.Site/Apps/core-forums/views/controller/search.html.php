<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="main_break"></div>
<form class="form" method="post" action="{if $aCallback === false}{url link='forum.search'}{else}{url link='forum.search' module='pages' item=$aCallback.group_id}{/if}">
    {if $aCallback !== false}
    <div><input type="hidden" name="search[group_id]" value="{$aCallback.group_id}" /></div>
    {/if}
    <div class="form-group">
        <label for="">{_p var='search_for_keyword_s'}</label>
        {$aFilters.keyword}
    </div>
    <div class="form-group">
        <label for="">{_p var='search_for_author'}</label>
        {$aFilters.user}
    </div>
    <h3>{_p var='search_options'}</h3>
    {if $aCallback === false}
    <div class="form-group">
        <label for="find_in_forum">{_p var='find_in_forum'}</label>
        <select id="find_in_forum" name="search[forum][]" multiple="multiple" size="10" class="form-control">
            {$sForumList}
        </select>
    </div>
    <div class="separate"></div>
    {/if}
    <div class="form-group">
        <label for="">{_p var='display'}</label>
        {$aFilters.display}
    </div>
    <div class="form-group">
        <label for="">{_p var='sort'}</label>
        {$aFilters.sort} in {$aFilters.sort_by}
    </div>
    <div class="form-group">
        <label for="">{_p var='from'}</label>
        {$aFilters.days_prune}
    </div>
    <div class="form-group">
        <label for="">{_p var='display_results_as'}</label>
        {$aFilters.result}
    </div>
    <input type="submit" name="search[submit]" value="{_p var='search'}" class="btn btn-primary" />
</form>