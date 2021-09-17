<?php
defined('PHPFOX') or exit('NO DICE!');
?>
  <div id="store_app_verify_status" style="max-height:150px; overflow:auto; background:#fff; margin-bottom:15px;">
    {if count($newFiles)}
    <div class="table new_file">
      <div class="table_left">
        {_p var='new_files'}:
      </div>
      <div class="table_right">
        {foreach from=$newFiles value=file}
        <div>{$file}</div>
        {/foreach}
      </div>
    </div>
    {/if}

    {if count($removeFiles)}
    <div class="table remove_file">
      <div class="table_left">
        {_p var='remove_files'}:
      </div>
      <div class="table_right">
        {foreach from=$removeFiles value=file}
        <div>{$file}</div>
        {/foreach}
      </div>
    </div>
    {/if}

    {if count($overrideFiles)}
    <div class="table override_file">
      <div class="table_left">
        {_p var='override_files'}:
      </div>
      <div class="table_right">
        {foreach from=$overrideFiles value=file}
        <div>{$file}</div>
        {/foreach}
      </div>
    </div>
    {/if}
</div>
<div class="table_clear">
  <input class="button" type="button" value="Continue" onclick="window.location.href='{url link='admincp.store.ftp' productName=$productName type=$type productId=$productId extra_info=$extra_info targetDirectory=$targetDirectory}'">
</div>
