<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="form-group">
    <label>{$aItem.phrase}</label>
        {module name='privacy.form' privacy_name='privacy' privacy_info='' privacy_array=$sPrivacy privacy_name=$sPrivacy privacy_custom_id='js_custom_privacy_input_holder_'$aItem.custom_id'' privacy_no_custom=true}
</div>