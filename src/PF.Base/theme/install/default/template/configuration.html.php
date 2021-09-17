<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{$sCreateJs}
{if count($aTables)}
<div class="label_flow" style="height:200px;">
	<ul>
	{foreach from=$aTables item=sTable}
		<li>{$sTable}</li>
	{/foreach}
	</ul>
</div>
<div class="p_4">
	<b>Notice</b>: To resolve this problem you can change the current table prefix. This setting can be found in "Advanced Configuration". Another option is you can use another database.
	<p>
		If you do not need any of the data in the table(s) mentioned above we could drop each of the table(s) and continue with the installation. If you prefer this method click on the button "Drop Tables and Continue with Installation" below.
		Note, that this method will only drop the needed phpFox tables.
	</p>
</div>
{/if}
<form method="post" action="#configuration" id="js_form" class="form">
    <h1>Database Configuration</h1>
    <div id="errors" class="hide"></div>
    {if count($aTables)}
    {foreach from=$aTables item=sTable}
    <div><input type="hidden" name="table[]" value="{$sTable}" /></div>
    {/foreach}
    {/if}
    <div class="form-group {if count($aDrivers) == 1}hide{/if}">
        <label class="control-label">
            Database Driver:
        </label>
        <select {if count($aDrivers) > 1}autofocus{/if} class="form-control" name="val[driver]">
            {foreach from=$aDrivers item=aDriver}
            <option value="{$aDriver.driver}"{value type='select' id='driver' default='`$aDriver.driver`'}>{$aDriver.label}</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label for="host">Database Host</label>
        <input {if count($aDrivers) == 1}autofocus{/if} required type="text" class="form-control" name="val[host]" id="host" value="{value type='input' id='host' default='localhost'}" />
    </div>

    <div class="form-group">
        <label for="name">Database Name</label>
        <input required type="text" class="form-control" name="val[name]" id="name" value="{value type='input' id='name'}"/>
    </div>

    <div class="form-group">
        <label for="user_name">Database User Name</label>
        <input required class="form-control" type="text" name="val[user_name]" id="user_name" value="{value type='input' id='user_name'}"/>
    </div>

    <div class="form-group">
        <label for="password">Database Password</label>
        <input class="form-control" type="password" name="val[password]" id="password" value="{value type='input' id='password'}" autocomplete="off" />
    </div>

    <div class="form-group">
        <label for="port">Database Port</label>
        <input class="form-control" type="text" name="val[port]" id="port" value="{value type='input' id='port' default='3306'}" />
    </div>

    <div class="form-group">
        <label for="prefix">Prefix for Tables in Database</label>
        <input class="form-control" type="text" name="val[prefix]" id="prefix" value="{value type='input' id='prefix' default='phpfox_'}" />
    </div>

    <div class="form-group">
        <label for="sitename">Site name</label>
        <input class="form-control" type="text" name="val[sitename]" id="sitename" value="{value type='input' id='prefix' default='Site Name'}" />
    </div>
    <div class="hide">
        <div class="table" style="padding:10px;">
            <a href="#" onclick="$('#js_advanced').toggle('fast'); return false;">Display Advanced Configuration</a>
        </div>
        <div id="js_advanced" style="">

            <div class="table_sub_header">
                Database
            </div>


            <div class="table_sub_header">
                Modules
            </div>
            <div class="table">
                <div class="table_left">
                    Modules:
                </div>
                <div class="table_right">
                    Core:
                    <div class="moduleList">
                        {foreach from=$aModules.core item=aModule}
                        <div class="p_4">
                            <div><input type="hidden" name="val[module][]" value="{$aModule.name}" /></div>
                            <label><input type="checkbox" name="null" value="{$aModule.name}" checked="checked" disabled="disabled" /> {$aModule.name}</label>
                        </div>
                        {/foreach}
                    </div>
                    <br />
                    Extended:
                    <div class="moduleList">
                        {foreach from=$aModules.plugin item=aModule}
                        <div class="p_4">
                            <label><input type="checkbox" name="val[module][]" value="{$aModule.name}" checked="checked" /> {$aModule.name}</label>
                        </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {if count($aTables)}
    <div><input type="hidden" name="val[drop]" value="1" /></div>
    <input type="button" value="Clear Values and Retry" class="btn btn-primary" onclick="window.location.href='{url link=""$sUrl".configuration"}';" />
    <input type="submit" value="Drop Tables and Continue with Installation" class="btn btn-danger sJsConfirm" />
    {else}
    <input type="submit" value="Start the Install" class="hide" />
    {/if}
    <div class="help-block">
        If you encounter any problem, please follow our instruction in <a href="https://docs.phpfox.com/display/FOX4MAN/Installing+phpFox" target="_blank">this help topic</a> then try again.
    </div>
</form>