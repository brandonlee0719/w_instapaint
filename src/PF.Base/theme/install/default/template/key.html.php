<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="client_details">
    <form method="post" action="#key" id="js_form" class="form">
        <h1>
            License Information
        </h1>

        <div id="errors" class="hide"></div>
        <div><input type="hidden" id="license_trial" name="val[is_trial]" value="0"></div>
        <div class="form-group">
            <label class="control-label">License ID</label>
            <input autofocus autocomplete="off" type="text" name="val[license_id]" id="license_id" value="{value type='input' id='license_id'}" size="30" placeholder="Enter your license id" class="form-control"/>
        </div>
        <div class="form-group">
            <label class="control-label">License Key</label>
            <input autocomplete="off" type="text" name="val[license_key]" id="license_key" value="{value type='input' id='license_key'}" size="30" placeholder="Enter your license key" class="form-control"/>
        </div>
        <input type="submit" value="Continue" class="hide" name="val[submit]"/>
        <div class="help-block">
            If you encounter any problem, please follow our instruction in <a href="https://docs.phpfox.com/display/FOX4MAN/Installing+phpFox" target="_blank">this help topic</a> then try again.
        </div>
    </form>
</div>