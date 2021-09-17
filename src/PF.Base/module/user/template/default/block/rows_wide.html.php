<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="pages_item user_item">
    {img user=$aUser suffix='_200_square' max_width=200 max_height=200}
    <div class="pages_info">
        <div>
            {$aUser|user}
            {module name='user.friendship' friend_user_id=$aUser.user_id type='icon' extra_info=true}
        </div>
    </div>
</div>