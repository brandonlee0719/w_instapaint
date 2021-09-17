<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.egift.add-category'}" onsubmit="$Core.onSubmitForm(this, true);">
    <div class="panel panel-default">

        <div class="panel-body">
            {if $bIsEdit}
            <input type="hidden" name="edit" value="{$iEditId}" />
            <input type="hidden" name="val[name]" value="{$aForms.name}" />
            {/if}

            {field_language phrase='phrase' class='form-control' label='Name' field='name' format='val[name_' size=30 maxlength=100}

            <div class="form-group">
                <label>{_p var='schedule_availability'}:</label>
                <div class="item_is_active_holder">
                <span class="js_item_active item_is_active" onclick="$('#availableSince, #availableUntil').show();">
                    <input class='form-control' type="radio" name="val[do_schedule]" value="1" {value type='radio' id='do_schedule' default='1'}/> {_p var='yes'}
                </span>
                    <span class="js_item_active item_is_not_active" onclick="$('#availableSince, #availableUntil').hide();">
                    <input class='form-control' type="radio" name="val[do_schedule]" value="0" {value type='radio' id='do_schedule' default='0'  selected='true'}/> {_p var='no'}
                </span>
                </div>
                <p class="help-block">{_p var='when_disabled_this_category_will_only_show_up_on_birthdays'}</p>
            </div>
            <div class="form-group" id="availableSince" style="{if empty($aForms.do_schedule)}display: none;{/if}">
                <div class="form-inline">
                    <div class="form-group">
                        <label>{_p var='available_since'}:</label>
                        {select_date prefix='start_' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true time_separator='event.time_separator'}
                    </div>
                    <div class="form-group">
                        <label>{_p var='available_until'}:</label>
                        {select_date prefix='end_' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true time_separator='event.time_separator'}
                    </div>
                </div>
            </div>

            <input type="hidden" name="val[date_order]" value="MDY">
        </div>

        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            <input onclick="return js_box_remove(this);" type="submit" value="{_p var='cancel'}" class="btn btn-default" />
        </div>
    </div>
</form>
