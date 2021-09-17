<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if Phpfox::demoModeActive()}
<div class="message">
    {_p('AdminCP is set to "Demo Mode". Certain actions are limited when in this mode and acts as a Read Only control panel.')}
</div>
{/if}
<div class="dashboard">
    <div class="row">
        <div class="col-md-4">
        {module name='admincp.stat'}
        {module name='core.site-stat'}
    </div>
    <div class="col-md-4">
        {module name='core.news'}
        {module name='core.note'}
    </div>
    <div class="col-md-4">
        {template file='admincp.block.trial'}
        {template file='admincp.block.install'}
        {module name='core.active-admin'}
        {module name='core.latest-admin-login'}
    </div>
    </div>
</div>