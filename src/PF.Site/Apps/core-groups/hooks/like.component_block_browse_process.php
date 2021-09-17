<?php
if ($this->request()->get('type_id') == 'groups') {
    $aPage = Phpfox::getService('groups')->getPage($this->request()->getInt('item_id'));
    if (!count($aLikes)) {
        $sErrorMessage = _p('this_group_has_no_members');
    }
}

if ($this->request()->get('type_id') == 'groups' && Phpfox::getService('groups')->isAdmin($this->request()->getInt('item_id'))) {
    $bIsPageAdmin = true;
}