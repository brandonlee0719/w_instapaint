<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aGenres)}
<div class="alert alert-danger">
    {_p var='no_genres_found'}
</div>
{else}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <a href="{url link='admincp.app' id='Core_Music'}">
                    {_p('Genres')}
                </a>
            </div>
        </div>
        <div class="table-responsive flex-sortable">
            <table class="table table-bordered" id="_sort" data-sort-url="{url link='music.admincp.genre.order'}">
                <thead>
                    <tr>
                        <th class="w30"></th>
                        <th class="w30"></th>
                        <th>{_p('Name')}</th>
                        <th class="t_center w140">{_p var='total_songs'}</th>
                        <th class="t_center" style="width:60px;">{_p var='Active'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$aGenres key=iKey item=aGenre}
                        <tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aGenre.genre_id}">
                            <td class="t_center">
                                <i class="fa fa-sort"></i>
                            </td>
                            <td class="t_center">
                                <a href="javascript:void(0)" class="js_drop_down_link" title="Manage"></a>
                                <div class="link_menu">
                                    <ul>
                                        <li><a class="popup" href="{url link='admincp.music.add' edit=$aGenre.genre_id}">{_p var='edit'}</a></li>
                                        <li><a class="popup" href="{url link='admincp.music.delete' delete=$aGenre.genre_id}" class="sJsConfirm">{_p var='delete'}</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td class="td-flex">
                                {$aGenre.name}
                            </td>
                            <td class="t_center" style="text-align: center">{if $aGenre.used > 0}<a href="{$aGenre.url}" id="js_category_link{$aGenre.genre_id}">{$aGenre.used}</a>{else}0{/if}</td>
                            <td class="on_off">
                                <div class="js_item_is_active"{if !$aGenre.is_active} style="display:none;"{/if}>
                                    <a href="#?call=music.toggleGenre&amp;id={$aGenre.genre_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                                </div>
                                <div class="js_item_is_not_active"{if $aGenre.is_active} style="display:none;"{/if}>
                                    <a href="#?call=music.toggleGenre&amp;id={$aGenre.genre_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}