<?php
defined('PHPFOX') or exit('NO DICE!');

if (defined('PHPFOX_IS_PAGES_ADD') && PHPFOX_IS_PAGES_ADD) {
    echo '<a role="button" class="text-uppercase fw-bold change_photo" onclick="tb_show(\'' . _p('edit_thumbnail') . '\', $.ajaxBox(\'pages.cropme\', \'height=400&width=500&id=' . $this->_aVars['aForms']['page_id'] . '\'))"><i class="ico ico-text-file-edit"></i>&nbsp;&nbsp;&nbsp;' . _p('edit_thumbnail') . '</a>';
}
