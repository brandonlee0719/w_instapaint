<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="breadcrumbs_right_section" id="breadcrumbs_menu">
{if (!defined('PHPFOX_IS_PAGES_VIEW') || !PHPFOX_IS_PAGES_VIEW) && (!defined('PHPFOX_IS_USER_PROFILE') || !PHPFOX_IS_USER_PROFILE)}
    {template file='core.block.actions-buttons'}
{/if}
</div>