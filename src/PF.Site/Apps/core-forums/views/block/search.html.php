<?php 
    defined('PHPFOX') or exit('NO DICE!'); 
 ?>

<div id="js_forum_search_wrapper">
    <input type="hidden" value="{$aSearchValues.adv_search}" id="js_adv_search_value" name="search[adv_search]"/>
    <div id="js_forum_search_result" class="item_is_active_holder item_selection_active advance_search_button">
        <a id="js_forum_enable_adv_search_btn" href="javascript:void(0)" onclick="forumEnableAdvSearch();return false;">
            <i class="ico ico-dottedmore-o"></i>
        </a>
    </div>
</div>

<div id="js_forum_adv_search_wrapper" class="advance_search_form" style="display: none;">
    <div class="form-group">
        <label>{_p var='search_for_author'}:</label>
        <div>
            {$aFilters.user}
        </div>
    </div>

    {if empty($aCallback)}
    <div class="form-group adv_search_forum">
        <label>{_p var='find_in_forum'}:</label>
        <div>
            <select name="search[forum][]" multiple="multiple" size="10">
                {$sForumList}
            </select>
        </div>
    </div>
    {/if}

    <div class="form-group">
        <label>{_p var='from'}: </label>
        <div>
            {$aFilters.days_prune}
        </div>
    </div>

    <div class="form-group clearfix advance_search_form_button">
        <div class="pull-left">
            <span class="advance_search_dismiss" onclick="forumEnableAdvSearch(); return false;">
                <i class="ico ico-close"></i>
            </span>
        </div>
        <div class="pull-right">
            <a class="btn btn-default btn-sm" href="{if isset($sResetUrl)}{$sResetUrl}{else}{if isset($sView) && $sView != ''}{url link='forum.search' view=$sView}{else}{url link='forum'}{/if}{/if}">{_p var='reset'}</a>
            <button class="btn btn-primary ml-1 btn-sm" type="submit" name="submit" id="adv_search_user"><i class="ico ico-search-o mr-1"></i>{_p var='search'}</button>
        </div>
    </div>
</div>

{literal}
    <script type="text/javascript">
        $Behavior.initForumSearch = function() {
            if ($('#form_main_search') && $('#js_forum_search_wrapper') && $('#form_main_search').find('#js_forum_search_wrapper').length == 0) {
                $("#js_forum_search_wrapper").detach().appendTo('#form_main_search');
                if ($('#js_adv_search_value').val() == '1') {
                    forumEnableAdvSearch('#js_forum_enable_adv_search_btn');
                }
                $('#form_main_search').find('div.hidden:first input[type="hidden"]:not(.not_remove)').remove();
            }
        }

        function forumEnableAdvSearch() {
            if ($('#form_main_search').find('#js_forum_adv_search_wrapper').length == 0) {
                $('#js_adv_search_value').val(1);
                $("#js_forum_adv_search_wrapper").detach().insertBefore('#js_search_input_holder');
                $('#js_forum_enable_adv_search_btn').addClass('active');
                $("#js_forum_adv_search_wrapper").slideDown();
            }
            else {
                $("#js_forum_adv_search_wrapper").slideUp();
                $('#js_adv_search_value').val(0);
                $('#js_forum_enable_adv_search_btn').removeClass('active');
                $("#js_forum_adv_search_wrapper").detach().insertAfter('#form_main_search');
            }
        }
    </script>
{/literal}
