<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="form-group">
    <label for="name">{required}{_p var='name'}</label>
    {if isset($aForms.album_id) && $aForms.profile_id > 0}
        <input type="hidden" name="val[name]" id="name" value="{_p var='profile_pictures'}" size="30" maxlength="150" autofocus/>
        {_p var='profile_pictures'}
    {elseif isset($aForms.album_id) && $aForms.cover_id > 0}
        <input type="hidden" name="val[name]" id="name" value="{_p var='cover_photo'}" size="30" maxlength="150" autofocus/>
        {_p var='cover_photo'}
    {elseif isset($aForms.album_id) && $aForms.timeline_id > 0}
        <input type="hidden" name="val[name]" id="name" value="{_p var='timeline_photos'}" size="30" maxlength="150" autofocus/>
        {_p var='timeline_photos'}
    {else}
        <input class="form-control" required type="text" name="val[name]" id="name" value="{value type='input' id='name'}" size="30" maxlength="150" autofocus/>
    {/if}
</div>
<div class="form-group">
    <label for="description">{_p var='description'}</label>
    <textarea class="form-control" name="val[description]" id="description" cols="40" rows="5">{value type='textarea' id='description'}</textarea>
</div>
{if isset($sModule) && $sModule}
{else}
  {if Phpfox::isModule('privacy') && Phpfox::getUserParam('photo.can_use_privacy_settings')}
      <div class="form-group form-group-follow">
          <label for="privacy">{_p var='album_s_privacy'}</label>
          {module name='privacy.form' privacy_name='privacy' privacy_info='photo.control_who_can_see_this_photo_album_and_any_photos_associated_with_it' privacy_custom_id='js_custom_privacy_input_holder_album'}
      </div>
  {/if}
{/if}