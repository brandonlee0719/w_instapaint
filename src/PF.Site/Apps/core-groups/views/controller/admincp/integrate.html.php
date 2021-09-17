<form class="form" method="post" id="pages_integrate_form" onsubmit="location.reload();">
    <div class="panel panel-default global-settings">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='manage_integrated_items'}
            </div>
        </div>
        <div class="panel-body">
            {foreach from=$aModules item=aModule key=sModuleId}
            <div class="form-group lines">
                <label class="">
                    {_p var='integrate_module_with_group' module=$aModule.title}
                </label>
                <div class="item_is_active_holder {if $aModule.value == 1}item_selection_active{else}item_selection_not_active{/if}">
                    <span class="js_item_active item_is_active">
                        <input type="radio" value="1" name="val[{$sModuleId}]" {if $aModule.value == 1}checked{/if} class="pages-integrate-radio""> {_p var='yes'}
                    </span>
                    <span class="js_item_active item_is_not_active">
                        <input type="radio" value="0" name="val[{$sModuleId}]" {if $aModule.value == 0}checked{/if} class="pages-integrate-radio""> {_p var='no'}
                    </span>
                </div>
                <p class="help-block">
                    {_p var='integrate_module_description_with_group' module=$aModule.title}
                </p>
            </div>
            {/foreach}
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{_p var='save_changes'}">
        </div>
    </div>
</form>