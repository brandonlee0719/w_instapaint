<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form class="form" method="post" action="{url link='admincp.ad.placement.add'}">
<div class="panel panel-default">
    <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.plan_id}" /></div>
            {/if}
            <div class="form-group">
                <label for="title">{_p var='title'}</label>
                <input type="text" name="val[title]" id="title" value="{value id='title' type='input'}" size="40" class="form-control"/>
            </div>

            {if Phpfox::getParam('ad.multi_ad') != true}
            <div class="form-group">
                <label for="location">{_p var='placement'}</label>
                <select name="val[block_id]" id="location" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aPlanBlocks item=i}
                    <option value="{$i}"{value type='select' id='block_id' default=$i}>{_p var='block_location_x' x=$i}</option>
                    {/foreach}
                </select>
                <a href="#?call=ad.sample&amp;width=scan&amp;click=1" class="inlinePopup" title="{_p var='sample_layout'}">{_p var='view_site_layout'}</a>
                <p class="help-block">{_p var='notice_the_ad_sizes_provided_is_a_recommendation'}</p>
            </div>
            <div class="form-group">
                <label for="">{_p var='dimensions'}</label>
                {_p var='width'}: <input type="text" name="val[d_width]" value="{value id='d_width' type='input'}" size="5" class="form-control" /> {_p var='height'}: <input type="text" name="val[d_height]" value="{value id='d_height' type='input'}" size="5" class="form-control"/>
                <p class="help-block">{_p var='ad_dimensions_are_in_pixels'}</p>
            </div>
            {else}
            <div>
                <input type="hidden" name="val[block_id]" value="50" />
                <input type="hidden" name="val[d_width]" value="245" />
                <input type="hidden" name="val[d_height]" value="200" />
            </div>
            {/if}
            <div class="form-group">
                <label for="">{_p var='price'}</label>
                {module name='core.currency' currency_field_name='val[cost]'}
            </div>

            <div class="form-group">
                <label for="is_cpm">{_p var='placement_type'}</label>
                <select name="val[is_cpm]" id="is_cpm" class="form-control">
                    <option value="0">{_p var='select'}:</option>
                    <option value="1"{value type='select' id='is_cpm' default='1'}>{_p var='cpm_cost_per_mille'}</option>
                    <option value="0"{value type='select' id='is_cpm' default='0'}>{_p var='ppc_pay_per_click'}</option>
                </select>
            </div>

            <div class="form-group-follow form-group">
                <label for="is_active">{_p var='is_active'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}</span>
                </div>
            </div>
    </div>
    <div class="panel-footer">
        <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
    </div>
</div>
</form>