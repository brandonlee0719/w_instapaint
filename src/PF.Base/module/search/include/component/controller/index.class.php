<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Search_Component_Controller_Index
 */
class Search_Component_Controller_Index extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('search.can_use_global_search', true);

        $sView = $this->request()->get('view', null);
        $sGetHistory = $this->request()->get('history');
        $sQuery = $this->request()->get('q', null, false);
        $iTotalShow = 10;
        $iPage = $this->request()->getInt('page', 1);

        if (!empty($sQuery)) {
            $aSearchResults = Phpfox::getService('search')->query($sQuery, $iPage, $iTotalShow, $sView);
            $aFilterMenu = array(
                _p('all_results') => $this->url()->makeUrl('search',
                    array('q' => urlencode($sQuery), 'encode' => '1'))
            );

            if (empty($sGetHistory)) {
                $sHistory = '';
                foreach ($aSearchResults as $aSearchResult) {
                    if (isset($aSearchTypes[$aSearchResult['item_type_id']])) {
                        continue;
                    }

                    $aSearchTypes[$aSearchResult['item_type_id']] = true;
                    $sHistory .= $aSearchResult['item_type_id'] . ',';
                }
                $sHistory = rtrim($sHistory, ',');
            } else {
                $sHistory = $sGetHistory;
            }

            $aMenus = Phpfox::massCallback('getSearchTitleInfo');
            foreach ($aMenus as $sKey => $aMenu) {
                $aFilterMenu[$aMenu['name']] = $this->url()->makeUrl('search',
                    array('q' => urlencode($sQuery), 'view' => $sKey, 'encode' => '1', 'history' => $sHistory));
            }

            $this->template()->buildSectionMenu('search', $aFilterMenu);
            $sQuery = htmlspecialchars($sQuery);


            $this->template()->clearBreadCrumb()
                ->assign(array(
                    'iTotalShow' => $iTotalShow,
                    'aSearchResults' => $aSearchResults,
                    'sQuery' => $sQuery,
                    'sNextPage' => 'q=' . urlencode($sQuery) . '&amp;encode=1&amp;view=' . $sView . '&amp;history=' . $sHistory . '&amp;page=' . ($iPage + 1),
                    'sMenuBlockTitle' => _p('filter_results_by')
                ))
                ->setTitle(_p('results'));
        }

        (($sPlugin = Phpfox_Plugin::get('search.component_controller_index_process_end')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('search.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
