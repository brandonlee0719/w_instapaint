<?php
defined('PHPFOX') or exit('NO DICE!');
if (Phpfox::isModule('subscribe')) {
    $subcribeModule = Phpfox_Module::instance()->get('subscribe');
    if ($subcribeModule and $subcribeModule['is_active']) {
        echo '<li role="presentation">' .
                '<a href="'. Phpfox_Url::instance()->makeUrl('subscribe') .'">' .
                    '<i class="fa fa-address-card-o"></i>&nbsp;' .
                    _p('membership') .
                '</a>' .
            '</li>';
    }
}
?>