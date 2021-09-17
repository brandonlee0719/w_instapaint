{if empty($aUploadCallback.component_only)}
<div class="form-group js_upload_form_wrapper {if !empty($sCurrentPhoto)}show-current{/if}" id="js_upload_form_{$sType}_wrapper" data-type="{$sType}">
    {if !empty($aUploadCallback.label)}
    <label>{if !empty($aUploadCallback.is_required)}{required}{/if}{$aUploadCallback.label}:</label>
    {/if}
    {if !empty($sCurrentPhoto) && !empty($iId)}
    <div class="change_photo_block js_upload_form_current" id="js_current_image_wrapper">
        {if $bImageClickable}
        <a style="background-image: url('{$sCurrentPhoto}');" href="{$sCurrentPhoto}" class="thickbox"></a>
        {else}
        <span style="background-image: url('{$sCurrentPhoto}');"></span>
        {/if}
        <div class="extra_info">
            {plugin call='core.template_block_upload_form_action_1'}
            <a class="text-uppercase fw-bold change_photo" href="javascript:void(0);" onclick="$Core.uploadForm.toggleForm(this);return false;">
                <i class="ico ico-photo-plus"></i>&nbsp;
                {_p var='change_photo'}
            </a>
            {plugin call='core.template_block_upload_form_action_2'}
            {if empty($aUploadCallback.is_required)}
            <a href="javascript:void(0);" class="remove" onclick="$Core.uploadForm.deleteImage(this,'{$sType}','{$sRemoveField}'); return false;">
                <i class="ico ico-trash-o"></i>&nbsp;
                {_p var='delete'}
            </a>
            {/if}
            {plugin call='core.template_block_upload_form_action_3'}
        </div>
    </div>
    {/if}

    <div class="table_right js_upload_form" id="js_upload_form">
    {/if}

        <div class="{if !empty($aUploadCallback.style)}{$aUploadCallback.style}-{/if}dropzone-component dont-unbind" id="{$sType}-dropzone"
             data-component="dropzone"
             data-dropzone-id = "{$sType}"
             data-url="{$aUploadCallback.upload_url}"
             data-param-name="{$aUploadCallback.param_name}"
             data-max-files="{$aUploadCallback.max_file}"
             data-clickable=".dropzone-button-{$sType}"
             data-preview-template="#dropzone-preview-template-{$sType}"
             data-auto-process-queue="{$aUploadCallback.upload_now}"
             data-upload-multiple="false"
             {if !empty($aUploadCallback.max_size)}
             data-max-size="{$aUploadCallback.max_size}"
             {/if}
            {if !empty($aUploadCallback.style)}
            data-upload-style="{$aUploadCallback.style}"
            {/if}
            {if !empty($aUploadCallback.submit_button)}
            data-submit-button="{$aUploadCallback.submit_button}"
            {/if}
             {if !empty($aUploadCallback.type_list_string)}
                data-accepted-files="{$aUploadCallback.type_list_string}"
             {/if}
             {if !empty($aUploadCallback.on_remove)}
                data-on-remove="{$aUploadCallback.on_remove}"
             {/if}
            {if empty($aUploadCallback.js_events)}
                data-on-success="$Core.uploadForm.onSuccessUpload"
            {else}
                {foreach from=$aUploadCallback.js_events key=event item=function}
                    data-on-{$event}="{$function}"
                {/foreach}
            {/if}
            {if !empty($aUploadCallback.extra_data)}
                {foreach from=$aUploadCallback.extra_data key=name item=value}
                    data-{$name}="{$value}"
                {/foreach}
            {/if}
        >
            {if !empty($sCurrentPhoto) && !empty($iId)}
            <a href="#" class="dismiss_upload js_hide_upload_form" onclick="$Core.uploadForm.dismissUpload(this, '{$sType}');return false;">
                <i class="ico ico-close-circle"></i>
            </a>
            {/if}

            {if !empty($aUploadCallback.style) && $aUploadCallback.style == 'mini'}

            <div class="dz-default dz-message dropzone-inner">
                {if !empty($aUploadCallback.first_description)}
                <p><b>{$aUploadCallback.first_description}</b></p>
                {/if}
                <div class="btn btn-primary dropzone-button dropzone-button-{$sType}">{_p var='browse_three_dot'}</div>
            </div>
            {else}

            <div class="dz-default {if empty($aUploadCallback.keep_form)}dz-message{/if}">
                {if !empty($aUploadCallback.use_browse_button)}
                <div class="dropzone-video-icon"><i class="{$aUploadCallback.upload_icon}"></i></div>
                {else}
                <div class="dropzone-button-outer">
                    <div class="dropzone-button dropzone-button-{$sType}"><i class="{$aUploadCallback.upload_icon}"></i></div>
                </div>
                {/if}
                <div class="dropzone-content-outer">
                    <div class="dropzone-content-info">
                        {if !empty($aUploadCallback.first_description)}
                        <h4>{$aUploadCallback.first_description}</h4>
                        {/if}
                        {if !empty($aUploadCallback.type_description)}
                        <p>{$aUploadCallback.type_description}</p>
                        {/if}
                        {if !empty($aUploadCallback.max_size_description)}
                        <p>{$aUploadCallback.max_size_description}</p>
                        {/if}
                        {if !empty($aUploadCallback.extra_description) && is_array($aUploadCallback.extra_description)}
                            {foreach from=$aUploadCallback.extra_description item=sDescription}
                                <p>{$sDescription}</p>
                            {/foreach}
                        {/if}
                    </div>
                    {if !empty($aUploadCallback.use_browse_button)}
                    <div class="btn btn-primary btn-gradient dropzone-button dropzone-button-{$sType}">{_p var='browse_three_dot'}</div>
                    {/if}
                </div>
                <a href="javascript:void(0)" id="dropzone-cancel-upload" class="dont-unbind" style="display: none">{_p var='stop_uploading'}</a>
            </div>
            {if empty($aUploadCallback.keep_form)}
                <div class="dropzone-button outer dropzone-button-{$sType}">
                    <div class="inner">
                        <i class="{$aUploadCallback.upload_icon}"></i>
                    </div>
                </div>
            {/if}
        {/if}

        </div>
        
        {if !empty($aUploadCallback.style) && $aUploadCallback.style == 'mini'}
        <div class="extra_info">
            {if !empty($aUploadCallback.type_description)}
            <p class="help-block">{$aUploadCallback.type_description}</p>
            {/if}

            {if !empty($aUploadCallback.max_size_description)}
            <p class="help-block">
                {$aUploadCallback.max_size_description}
            </p>
            {/if}
        </div>
        {/if}
        <!-- Dropzone template -->
        <div id="dropzone-preview-template-{$sType}" style="display: none;">
            {if !empty($aUploadCallback.preview_template)}
                {$aUploadCallback.preview_template}
            {else}
                <div class="dz-preview dz-file-preview">
                    <div class="dz-image"><img data-dz-thumbnail /></div>
                    <div class="dz-filename"><span data-dz-name ></span></div>
                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    <span class="dz-error-icon hide"><i class="ico ico-info-circle-alt"></i></span>
                    <input class="dz-form-file-id" type="hidden" id="js_upload_form_file_{$sType}" />
                </div>
            {/if}
        </div>

        {if empty($aUploadCallback.component_only)}
    </div>
</div>
{/if}