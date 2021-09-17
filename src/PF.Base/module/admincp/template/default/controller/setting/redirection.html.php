<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.setting.redirection'}" class="ajax_post form">
    <div class="panel panel-default">
        <div class="panel-heading">
            {_p var='URL Match'}
        </div>
        <div class="panel-body">
            {$info}
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{if ($enabled)}{_p var='Disable'}{else}{_p var='Enable'}{/if}">
        </div>
    </div>
</form>