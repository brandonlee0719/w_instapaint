<?php
defined('PHPFOX') or exit('NO DICE');
/**
 * @author Neil J. <neil@phpfox.com>
 */
?>

<form action="#" onsubmit="$(this).ajaxCall('amazons3.processCreateBucket'); return false;" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="region">{_p var="Select region"}:</label>
        <select class="form-control" name="val[region]" id="region">
            {foreach from=$aAllRegions key=sKey value=sRegion}
            <option value="{$sKey}">{$sRegion}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label for="bucket">{_p var="Bucket Name"}:</label>
        <input type="text" class="form-control" id="bucket" name="val[bucket]">
    </div>
    <div class="form-group">
        <input type="submit" name="val[submit]" value="{_p var='Create'}" class="btn btn-primary">
    </div>
</form>
