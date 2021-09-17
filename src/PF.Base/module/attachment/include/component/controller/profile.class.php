<?php
defined('PHPFOX') or exit('NO DICE!');

class Attachment_Component_Controller_Profile extends Phpfox_Component
{
    public function process()
    {
        $this->setParam('bIsProfile', true);
        $aUser = $this->getParam('aUser');
        if ($aUser['user_id'] != Phpfox::getUserId()){
            $this->url()->send('profile.attachment');
        }
        $this->search()->set(array(
                'type' => 'attachment',
                'field' => 'attachment.attachment_id',
                'search_tool' => array(
                    'table_alias' => 'attachment',
                    'search' => array(
                        'action' => $this->url()->makeUrl($aUser['user_name'], array('attachment', 'view' => $this->request()->get('view'))),
                        'default_value' => _p('Search attachment...'),
                        'name' => 'search',
                        'field' => array('attachment.file_name')
                    ),
                    'sort' => array(
                        'latest' => array('attachment.time_stamp', _p('Latest')),
                        'category' => array('attachment.category_id', _p('Category')),
                    ),
                    'show' => array(10, 20, 30)
                )
            )
        );
        $aConds = $this->search()->getConditions();
        $sSort = $this->search()->getSort();
        $aConds[] = ' AND attachment.user_id=' . (int) $aUser['user_id'];
        list($iCnt, $aRows) = Phpfox::getService('attachment')->get($aConds, $sSort);
        $iPage = $this->request()->get('page');
        Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => 12, 'count' => $iCnt));
        $time = '';
        foreach ($aRows as $iKey => $aRow){
            if (!$aRow['item_id']){
                $using = _p('Inline');
            } elseif (Phpfox::hasCallback($aRow['category_id'], 'getItemLink'))
            {
                $data = Phpfox::callback($aRow['category_id'] . '.getItemLink',$aRow['item_id'], true);
                $using = "<a href='". $data['url']. "' > " . $data['title'] . "</a>";
            } else {
                $using = _p('attachment_using_in', ['module' => $aRow['category_id'], 'item_id' => $aRow['item_id']]);
            }
            $aRows[$iKey]['using'] = "<span><b>". _p("Using: ") ."</b>" . $using . ' . <span class="attachment-file-size">' . Phpfox::getLib('phpfox.file')->filesize($aRow['file_size'], 3) . "</span></span>";
            $old_time = $time;
            $time = Phpfox_Date::instance()->convertTime($aRow['time_stamp']);
            if ($old_time != $time){
                $aRows[$iKey]['time_name'] = $time;
            }
        }
        $this->template()->setTitle(_p('attachments_title'))
            ->setBreadCrumb(_p('attachments_title'), $this->url()->makeUrl('profile.attachment'))
            ->setSectionTitle(_p('attachment_file_types'))
            ->assign(array(
                    'aRows' => $aRows
                )
            );
        return null;
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}