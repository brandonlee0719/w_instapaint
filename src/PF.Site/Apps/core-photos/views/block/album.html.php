<?php
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bAllowedAlbums}
    <form class="form" method="post" action="{url link='current'}" id="js_create_new_album" onsubmit="return addNewAlbum();">
        {if $sModule}
            <div><input type="hidden" name="val[module_id]" value="{$sModule}" /></div>
        {/if}
        {if $iItem}
            <div><input type="hidden" name="val[group_id]" value="{$iItem}" /></div>
        {/if}
        <div id="js_custom_privacy_input_holder_album"></div>
        {template file='photo.block.form-album'}
        <input type="submit" value="{_p('submit')}" class="btn btn-primary" />
        {if Phpfox::getParam('core.display_required')}
            <div class="help-block">
                {required} {_p('required_fields')}
            </div>
        {/if}
    </form>
    <script type="text/javascript">
        {literal}
        function addNewAlbum()
        {
            {/literal}
            if ({$sGetJsForm})
            {literal}
            {
                $('#js_create_new_album').ajaxCall('photo.addAlbum');
                js_box_remove(this);
            }
            return false;
        }
        {/literal}
    </script>
    {$sCreateJs}
{else}
    {_p('you_have_reached_your_limit_you_are_currently_unable_to_create_new_photo_albums')}
{/if}