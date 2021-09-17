<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="main_break">
    {$sCreateJs}
    <form method="post" action="{url link='video.edit'}" id="core_js_video_form" enctype="multipart/form-data"
          onsubmit="{$sGetJsForm}"
    >
        <div id="js_custom_privacy_input_holder">
            {module name='privacy.build' privacy_item_id=$aForms.video_id privacy_module_id='v'}
        </div>
        <div><input type="hidden" name="id" value="{$aForms.video_id}" /></div>
        {plugin call='video.template_controller_add_hidden_form'}

        <div class="table form-group">
            <div class="table_left">
                <label for="title">{required}{_p var='title'}:</label>
            </div>
            <div class="table_right">
                <input class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" />
            </div>
        </div>

        {plugin call='video.template_controller_add_textarea_start'}

        <div class="table form-group">
            <div class="table_left">
                <label for="text">{_p var='description'}:</label>
            </div>
            <div class="table_right">
                {editor id='text'}
            </div>
        </div>

        {if empty($aForms.image_path)}
        {module name='core.upload-form' type='v_edit_video'}
        {else}
        {module name='core.upload-form' type='v_edit_video' current_photo=$aForms.image_path photo_clickable='true' id=$aForms.video_id}
        {/if}

        <div class="table form-group-follow">
            <div class="table_left">
                <label>{_p var='categories'}:</label>
            </div>
            <div class="table_right">
                <div class="label_flow label_hover labelFlowContent" id="js_category_content">
                    {module name='v.add_category_list'}
                </div>
            </div>
        </div>
        {if $aForms.module_id != 'pages' && $aForms.module_id != 'groups'}
        <div class="table form-group-flow">
            <div class="table_left">
                <label>{_p var='privacy'}:</label>
            </div>
            <div class="table_right">
                {if Phpfox::isModule('privacy')}
                    {module name='privacy.form' privacy_name='privacy' privacy_info='video_control_who_can_see_this_video' default_privacy='v.default_privacy_setting'}
                {/if}
            </div>
        </div>
        {/if}
        <div class="table_clear">
            <ul class="table_clear_button">
                {plugin call='video.template_controller_add_submit_buttons'}
                <li><input type="submit" name="val[update]" value="{_p var='save'}" class="button btn-primary" /></li>
            </ul>
            <div class="clear"></div>
        </div>
    </form>
    {if Phpfox::getParam('core.display_required')}
    <div class="table_clear">
        {required} {_p var='required_fields'}
    </div>
    {/if}
</div>