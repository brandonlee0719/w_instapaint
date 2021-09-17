<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Pagination
 * This class handles all the pagination on the site and creates a 
 * template variable that is automatically picked up with a simple HTML
 * variable call.
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: pager.class.php 6699 2013-09-30 14:14:43Z Fern $
 */
class Phpfox_Pager
{
	/**
	 * Current page we are on.
	 *
	 * @var int
	 */
	private $_iPage = 0;
	
	/**
	 * Total items per page.
	 *
	 * @var int
	 */
	private $_iPageSize = 0;
	
	/**
	 * Total pages.
	 *
	 * @var int
	 */
	private $_iPagesCount = 0;
	
	/**
	 * From page number.
	 *
	 * @var int
	 */
	private $_iFirstRow;
	
	/**
	 * Last page number.
	 *
	 * @var int
	 */
	private $_iLastRow;
	
	/**
	 * Numbers to display in the pagination frame.
	 *
	 * @var int
	 */
	private $_iFrameSize = 5;
	
	/**
	 * AJAX request call.
	 *
	 * @var string
	 */
	private $_sAjax;
	
	/**
	 * Params to pass along with the pagination request.
	 *
	 * @var array
	 */
	private $_aParams = [];
	
    /** 
     * URL key for page param e.g. in http://example.com/?page=3 it is 'page'
     * 
     * @var string
     */
    private $_sUrlKey = 'page';	
    
    /**
     * Custom phrase for the pager.
     *
     * @var string
     */
    private $_sPhrase = '';
    
    /**
     * Custom icon for the pager.
     *
     * @var string
     */
    private $_sIcon = '';

	/**
	 * Pager on popup.
	 *
	 * @var boolean
	 */
	private $_bPopup = false;

    /**
     * @var int
     */
	private $_iCnt = 0;

    /**
     * @var string
     */
    private $_sPagingMode = 'loadmore';

    /**
     * @var array
     */
    private $_aAjaxPaging = [];
	
    /**
     * Class constructor.
     *
     */
    public function __construct()
    {
        $this->_sPagingMode = Phpfox::getParam('core.paging_mode', 'loadmore');
        $this->_aParams = [
            'paging_show_disabled' => false,
            'paging_show_icon' => false,
            'pagination_walk' => 2,
            'pagination_show_first_last' => true,
            'pagination_show_next_prev' => true
        ];
    }

	/**
	 * @return Phpfox_Pager
	 */
	public static function instance() {
		return Phpfox::getLib('pager');
	}
    
    /**
     * Set all variables and build the pagination environment for this specific page.
     *
     * @param array $aParams ARRAY of params.
     */
	public function set($aParams = array())
	{
	    $this->_iPage = $aParams['page'];
		$this->_iPageSize =  max(intval($aParams['size']), 1);
		$this->_iCnt = max(intval($aParams['count']), 0);
		$this->_iPagesCount = ceil($this->_iCnt / $this->_iPageSize);
		$this->_iPage = max(1, min($this->_iPagesCount, intVal($aParams['page'])));
        $this->_iFirstRow   = $this->_iPageSize*($this->_iPage-1);
        $this->_iLastRow    = min($this->_iFirstRow + $this->_iPageSize, $this->_iCnt);
        $this->_iFrameSize  = max(intval($this->_iFrameSize), 1);
        if (isset($aParams['ajax']))
        {
        	$this->_sAjax = $aParams['ajax'];
        }

		if (isset($aParams['popup']))
		{
			$this->_bPopup = $aParams['popup'];
		}
        
        if (isset($aParams['phrase']))
        {
        	$this->_sPhrase = $aParams['phrase'];
        }
        
        if (isset($aParams['icon']))
        {
        	$this->_sIcon = $aParams['icon'];
        }

        if (isset($aParams['paging_mode']))
        {
            $this->_sPagingMode = $aParams['paging_mode'];
        }

        if (isset($aParams['ajax_paging']))
        {
            $this->_aAjaxPaging = $aParams['ajax_paging'];
        }

        if (isset($aParams['params']))
        {
        	$this->_aParams = array_merge($this->_aParams, Phpfox_Request::instance()->getRequests(), $aParams['params']);
        }
        else 
        {
        	$this->_aParams = array_merge($this->_aParams, Phpfox_Request::instance()->getRequests());
        }


        if (empty($this->_aParams['paging_labels'])) {
            $this->_aParams['paging_labels'] = [
                'first' =>  $this->_aParams['paging_show_icon'] ? '<i class="fa fa-angle-double-left"></i>' : _p('first'),
                'last' =>  $this->_aParams['paging_show_icon'] ? '<i class="fa fa-angle-double-right"></i>' : _p('last'),
                'previous' =>  $this->_aParams['paging_show_icon'] ? '<i class="fa fa-angle-left"></i>' : _p('previous'),
                'next' =>  $this->_aParams['paging_show_icon'] ? '<i class="fa fa-angle-right"></i>' : _p('next'),
            ];
        }


        $this->_getInfo();        
	}
	
	/**
	 * Process the output data for pages that are cached.
	 *
	 * @param array $aRows ARRAY of SQL data.
	 */
	public function process(&$aRows)
	{
		$sActualLimit = $this->_iPageSize;
		$sNewLimit = ($this->_iPage > 0 ? (($this->_iPage - 1) * $sActualLimit) : 0);
		$iCurrentCnt = 0;					
		foreach ($aRows as $iKey => $aRow)
		{
			$iCurrentCnt++;				
			if ($this->_iPage > 0 && ($iCurrentCnt <= $sNewLimit || $iCurrentCnt > ($sNewLimit + $sActualLimit)))
			{
				unset($aRows[$iKey]);
			}
			
			if (!$this->_iPage)
			{
				if ($iCurrentCnt > $sActualLimit)
				{
					unset($aRows[$iKey]);	
				}
			}
		}

		$iNextPage = ($this->_iPage + 1);
		
		Phpfox_Template::instance()->assign('iPagerNextPageCnt', $iNextPage);
	}
	
	/**
	 * Get the number of total pages.
	 *
	 * @return int
	 */
	public function getTotalPages()
	{
		return $this->_iPagesCount;
	}

	/**
	 * Check is on popup
	 *
	 * @return boolean
	 */
	public function isPopup()
	{
		return $this->_bPopup;
	}

	/**
	 * Get the current page we are on.
	 *
	 * @return int
	 */
	public function getCurrentPage()
	{
		return $this->_iPage;
	}
	
	/**
	 * Get the number of the next page.
	 *
	 * @return int
	 */
	public function getNextPage()
	{
		return ($this->_iPage + 1);
	}	
	
	/**
	 * Get the number of the last page.
	 *
	 * @return int
	 */
	public function getLastPage()
	{
		return $this->_iPagesCount;
	}
	
    /**
     * Get offset for given page (fix page number if needed)
     *
     * @param int $iPage      page number
     * @param int $iPageSize  page size (rows per page)
     * @param int $iCnt       records number
     * @return int offset of LIMIT in SQL
     */
    public function getOffset($iPage, $iPageSize, $iCnt)
    {
        if ($iPageSize) //if get page -- fix current page and get offset
        {
            $iPages  = ceil($iCnt / $iPageSize);
            $iPage   = max(1, min($iPages, $iPage));
            return $iPageSize*($iPage-1);
        }

        return 0;
    }
    
    /** 
     * Calculates first/last page in a current frame
     * 
     * @return array ($nStart, $nEnd)
     */
    private function _getPos()
    {
        $nStart = 1;
        if (($this->_iPage - $this->_iFrameSize/2) > 0)
        {
            if (($this->_iPage + $this->_iFrameSize/2) > $this->_iPagesCount)
            {
                $nStart = (($this->_iPagesCount - $this->_iFrameSize)>0) ? ( $this->_iPagesCount - $this->_iFrameSize + 1) : 1;
            }
            else
            {
                $nStart = $this->_iPage - floor($this->_iFrameSize/2);
            }
        }

        $nEnd = (($nStart + $this->_iFrameSize - 1) < $this->_iPagesCount) ? ($nStart + $this->_iFrameSize - 1) : $this->_iPagesCount;
        
        return array($nStart, $nEnd);
    }    
	
    /** 
     * Returns paging info: 'totalPages', 'totalRows', 'current', 'fromRow','toRow', 'firstUrl', 'prevUrl', 'nextUrl', 'lastUrl',  'urls' (url=>page)
     * 
     * @param Url $oUrl page url
     * @return array paging info
     */
    private function _getInfo()
    {
	    if (Phpfox::isAdminPanel() || $this->isPopup()) {
		    if($this->getTotalPages() == 0)
		    {
			    return false;
		    }

		    $sParams = '';
		    if (count($this->_aParams))
		    {
			    foreach ($this->_aParams as $iKey => $sValue)
			    {
				    if (in_array($iKey, array(
						    'phpfox',
						    Phpfox::getTokenName(),
						    'page',
						    PHPFOX_GET_METHOD,
						    'ajax_page_display'
					    )
				    )
				    )
				    {
					    continue;
				    }

				    if (is_array($sValue))
				    {
					    foreach ($sValue as $sKey => $sNewValue)
					    {
						    if (is_numeric($sKey))
						    {
							    continue;
						    }

						    $sParams .= '&amp;' . $iKey . '[' . $sKey . ']=' . $sNewValue;
					    }
				    }
				    else
				    {
					    $sParams .= '&amp;' . $iKey . '=' . $sValue;
				    }
			    }
		    }

		    $aInfo = array(
			    'totalPages' => $this->_iPagesCount,
			    'totalRows'  => $this->_iCnt,
			    'current'    => $this->_iPage,
			    'fromRow'    => $this->_iFirstRow+1,
			    'toRow'      => $this->_iLastRow,
			    'displaying' => ($this->_iCnt <= ($this->_iPageSize * $this->_iPage) ? $this->_iCnt : ($this->_iPageSize * $this->_iPage)),
			    'sParams' => $sParams,
			    'phrase' => $this->_sPhrase,
			    'icon' => $this->_sIcon
		    );

		    list($nStart, $nEnd) = $this->_getPos();

		    $oUrl = Phpfox_Url::instance();
		    $oUrl->clearParam('page');

		    if ($this->_iPage != 1)
		    {
			    $oUrl->setParam($this->_sUrlKey, 1);
			    $aInfo['firstAjaxUrl'] = 1;
			    $aInfo['firstUrl'] = $oUrl->getFullUrl();

			    $oUrl->setParam($this->_sUrlKey, $this->_iPage-1);
			    $aInfo['prevAjaxUrl'] = ($this->_iPage-1);
			    $aInfo['prevUrl'] = $oUrl->getFullUrl();
			    Phpfox_Template::instance()->setHeader('<link rel="prev" href="' . $aInfo['prevUrl'] . '" />');
		    }

		    for ($i = $nStart; $i <= $nEnd; $i++)
		    {
			    if ($this->_iPage == $i)
			    {
				    $oUrl->setParam($this->_sUrlKey, $i);
				    $aInfo['urls'][$oUrl->getFullUrl()] = $i;
			    }
			    else
			    {
				    $oUrl->setParam($this->_sUrlKey, $i);
				    $aInfo['urls'][$oUrl->getFullUrl()] = $i;
			    }
		    }

		    $oUrl->setParam($this->_sUrlKey, ($this->_iPage + 1));
		    $aInfo['nextAjaxUrlPager'] = $oUrl->getFullUrl();

		    if ($this->_iPagesCount != $this->_iPage)
		    {
			    $oUrl->setParam($this->_sUrlKey, ($this->_iPage + 1));
			    $aInfo['nextAjaxUrl'] = ($this->_iPage + 1);
			    $aInfo['nextUrl'] = $oUrl->getFullUrl();
			    Phpfox_Template::instance()->setHeader('<link rel="next" href="' . $aInfo['nextUrl'] . '" />');

			    $oUrl->setParam($this->_sUrlKey, $this->_iPagesCount);
			    $aInfo['lastUrl']= $oUrl->getFullUrl();
			    $aInfo['lastAjaxUrl'] = $this->_iPagesCount;
		    }

		    $aInfo['sParamsAjax'] = str_replace("'", "\\'", $aInfo['sParams']);

		    Phpfox_Template::instance()->assign(array(
				    'aPager' => $aInfo,
				    'sAjax' => $this->_sAjax,
					'bPopup' => $this->isPopup()
			    )
		    );
	    }
	    else {
	        $iPage = (int) Phpfox_Request::instance()->get('page', 1);
            $iPage = ($iPage >= 1) ? $iPage : 1;

	        Phpfox_Url::instance()->clearParam('page');

		    $oUrl =  Phpfox::getLib('url');

		    $iTotalPage  = $this->getTotalPages();

		    if (!empty($this->_aAjaxPaging)) {
                $aAjaxPaging = $this->_aAjaxPaging;
                $aAjaxPaging['sParam'] = '';
                if (!empty($aAjaxPaging['params'])) {
                    $sExtra = '';
                    foreach ($aAjaxPaging['params'] as $key => $value) {
                        $sExtra .= '&' . $key . '=' . $value;
                    }

                    $aAjaxPaging['sParam'] = $sExtra;
                }
            }

		    if ($iTotalPage > 1 && $this->_sPagingMode != 'loadmore') {
		        $aPagers = [];

                //pagination
		        if ($this->_sPagingMode == 'pagination') {
                    $aPagers[] = [
                        'attr' => 'active',
                        'link' => $oUrl->makeUrl('current',['page'=> $iPage]),
                        'label' => $iPage,
                        'page_number' => $iPage
                    ];
		            $iWalk = $this->_aParams['pagination_walk'];
		            for ($i = 1; $i <= $iWalk; $i ++) {
                        $iPrev = $iPage - $i;
                        if ($iPrev > 0) {
                            array_unshift($aPagers, [
                                'link' => $oUrl->makeUrl('current',['page'=> $iPrev]),
                                'label' => $iPrev,
                                'page_number' => $iPrev
                            ]);
                        }

                        $iNext = $iPage + $i;
                        if ($iNext <= $iTotalPage) {
                            $aPagers[] = [
                                'link' => $oUrl->makeUrl('current',['page'=> $iNext]),
                                'label' => $iNext,
                                'page_number' => $iNext
                            ];
                        }
                    }
                }

                $aLabel  = $this->_aParams['paging_labels'];
		        //next - prev buttons
                if ($this->_sPagingMode == 'next_prev' || !empty($this->_aParams['pagination_show_next_prev'])) {
                    $iPrev = $iPage - 1;
                    if ($iPrev > 0 || $this->_aParams['paging_show_disabled']) {
                        array_unshift($aPagers, [
                            'link' => $oUrl->makeUrl('current',['page'=> $iPrev]),
                            'label' => $aLabel['previous'],
                            'attr' => ($iPrev <= 0) ? 'disabled' : '',
                            'page_number' => $iPrev,
                            'rel' => 'prev'
                        ]);
                    }

                    $iNext = $iPage + 1;
                    if ($iNext <= $iTotalPage || $this->_aParams['paging_show_disabled']) {
                        $aPagers[] = [
                            'link' => $oUrl->makeUrl('current',['page'=> $iNext]),
                            'label' => $aLabel['next'],
                            'attr' => ($iNext > $iTotalPage) ? 'disabled' : '',
                            'page_number' => $iNext,
                            'rel' => 'next'
                        ];
                    }
                }

                //first - last buttons
                if ($this->_sPagingMode == 'pagination' && !empty($this->_aParams['pagination_show_first_last'])) {
                    if ($iPage > 1 || $this->_aParams['paging_show_disabled']) {
                        array_unshift($aPagers, [
                            'link' => $oUrl->makeUrl('current',['page'=> 1]),
                            'label' => $aLabel['first'],
                            'attr' => ($iPage == 1) ? 'disabled' : '',
                            'page_number' => 1,
                            'rel' => 'first'
                        ]);
                    }

                    if ($iPage < $iTotalPage || $this->_aParams['paging_show_disabled']) {
                        $aPagers[] = [
                            'link' => $oUrl->makeUrl('current',['page'=> $iTotalPage]),
                            'label' => $aLabel['last'],
                            'attr' => ($iPage == $iTotalPage) ? 'disabled' : '',
                            'page_number' => $iTotalPage,
                            'rel' => 'last'
                        ];
                    }
                }

                Phpfox_Template::instance()->assign([
                    'aPagers' => $aPagers
                ]);
            }
            Phpfox_Template::instance()->assign([
                'sPagingMode' => $this->_sPagingMode,
                'aAjaxPaging' => empty($aAjaxPaging) ? [] : $aAjaxPaging,
                'sNextUrl' => $oUrl->makeUrl('current',['page'=> $iPage + 1]),
                'iNextPage' => $iPage + 1
            ]);
	    }
    }	
}