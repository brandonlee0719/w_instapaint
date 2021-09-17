<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($aForms.current_image) && !empty($aForms.song_id)}
    {module name='core.upload-form' type='music_image' params=$aForms.params current_photo=$aForms.current_image id=$aForms.song_id}
{else}
    {module name='core.upload-form' type='music_image' params=$aForms.params id=$aForms.song_id}
{/if}

