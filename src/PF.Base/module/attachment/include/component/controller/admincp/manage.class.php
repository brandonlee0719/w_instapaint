<?php
/**
 * User: Neil
 * Date: 5/12/16
 * Time: 15:59
 */
defined('PHPFOX') or exit('NO DICE!');

class Attachment_Component_Controller_Admincp_Manage extends Phpfox_Component
{
    public function process()
    {
        $aPages = array(10, 20, 30);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt)
        {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }

        $aSorts = array(
            'attachment.time_stamp' => _p("Recently added")
        );

        $aFilters = array(
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '10'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'attachment.time_stamp'
            ),
            'name' => array(
                'type' => 'input:text',
                'search' => "AND attachment.file_name LIKE '%[VALUE]%'"
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => 'DESC'
            )
        );

        $oSearch = Phpfox_Search::instance()->set(array(
                'type' => 'attachment',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );
        /////
        $aConds = $oSearch->getConditions();
        $sSort = $oSearch->getSort();
        $iLimit = $oSearch->getDisplay();
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
            $aRows[$iKey]['using'] = "<span><b>". _p("Using: ") ."</b>" . $using . "</span>";
            $old_time = $time;
            $time = Phpfox_Date::instance()->convertTime($aRow['time_stamp']);
            if ($old_time != $time){
                $aRows[$iKey]['time_name'] = $time;
            }
        }
        $this->template()->setTitle(_p('attachments_title'))
            ->setBreadCrumb(_p('Apps'),$this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('attachments_title'), $this->url()->makeUrl('admincp.attachment.manage'))
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