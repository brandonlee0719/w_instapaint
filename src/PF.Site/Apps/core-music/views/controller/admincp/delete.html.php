<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.music.delete'}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div><input type="hidden" name="delete" value="{$iDeleteId}" /></div>
            <div class="alert alert-warning">
                {_p('are_you_sure_you_want_to_delete_this_genre')}
            </div>
            {if $iTotalItems}
                <div class="form-group">
                    <label>{_p('select_an_action_to_all_songs_of_this_genre')}</label>
                        <div class="radio">
                            <label><input type="radio" onchange="core_music_onchangeDeleteGenreType(1)" name="val[delete_type]" id="delete_type" value="1" checked>{_p('remove_all_songs_belonging_to_this_genre')}</label>
                        </div>
                        <div class="radio">
                            <label><input type="radio" onchange="core_music_onchangeDeleteGenreType(2)" name="val[delete_type]" id="delete_type" value="2">{_p('only_remove_genre_keep_all_songs_existing')}</label>
                        </div>
                        {if count($aGenres) > 0}
                            <div class="radio">
                                <label><input type="radio" onchange="core_music_onchangeDeleteGenreType(3)" name="val[delete_type]" id="delete_type" value="3">{_p('select_another_genre_for_all_songs_belonging_to_this_genre')}</label>
                            </div>
                            <select name="val[new_genre_id]" id="genre_select" class="form-control" style="display: none">
                                {foreach from=$aGenres item=aGenre}
                                    <option value="{$aGenre.genre_id}">
                                        {$aGenre.name|convert}
                                    </option>
                                {/foreach}
                            </select>
                        {/if}
                    </ul>
                </div>
            {else}
                <div><input type="hidden" name="val[delete_type]" value="0" /></div>
            {/if}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
            <input onclick="return js_box_remove(this);" type="submit" value="{_p('Cancel')}" class="btn btn-default" />
        </div>
    </div>
</form>