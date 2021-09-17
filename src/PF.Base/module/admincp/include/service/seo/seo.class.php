<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Service
 * @version 		$Id: seo.class.php 7052 2014-01-20 13:45:52Z Fern $
 */
class Admincp_Service_Seo_Seo extends Phpfox_Service 
{
    /**
     * Set header
     */
	public function setHeaders()
	{
		$sCacheId = $this->cache()->set('seo_nofollow_build');
        if (($aNoFollows = $this->cache()->get($sCacheId)) === false) {
            $aRows = $this->database()->select('*')
                ->from(Phpfox::getT('seo_nofollow'))
                ->execute('getSlaveRows');
            $aNoFollows = array();
            foreach ($aRows as $aRow) {
                $aNoFollows[$aRow['url']] = true;
            }
            $this->cache()->save($sCacheId, $aNoFollows);
            Phpfox::getLib('cache')->group('seo', $sCacheId);
        }
		
		if (count($aNoFollows))
		{
			$sUrl = trim(Phpfox_Url::instance()->getFullUrl(true), '/');
			if (isset($aNoFollows[$sUrl]))
			{
				Phpfox_Template::instance()->setHeader('<meta name="robots" content="nofollow" />');
			}
		}
		
		$sCacheId = $this->cache()->set('seo_meta_build');

        $aMetas = $this->cache()->get($sCacheId);
		if ($aMetas === false)
		{
			$aRows = $this->database()->select('*')
				->from(Phpfox::getT('seo_meta'))
				->execute('getSlaveRows');

			$aMetas = array();
			foreach ($aRows as $aRow)
			{
				if (!isset($aMetas[$aRow['url']]))
				{
					$aMetas[$aRow['url']] = array();
				}
				$aMetas[$aRow['url']][] = $aRow;
			}
			$this->cache()->save($sCacheId, $aMetas);
            Phpfox::getLib('cache')->group('seo', $sCacheId);
		}		

		if (count($aMetas))
		{
			$sUrl = trim(Phpfox_Url::instance()->getFullUrl(true), '/');

			if (isset($aMetas[$sUrl]))
			{
				foreach ($aMetas[$sUrl] as $aMeta)
				{
					if ($aMeta['type_id'] == '2')
					{
						Phpfox_Template::instance()->setTitle(Phpfox_Locale::instance()->convert($aMeta['content']));
						
						continue;
					}
					Phpfox_Template::instance()->setMeta((!$aMeta['type_id'] ? 'keywords' : 'description'), $aMeta['content']);
				}
			}
		}
	}
    
    /**
     * @param string $sUrl
     *
     * @return string
     */
	public function getUrl($sUrl)
	{
		$sUrl = str_replace(Phpfox::getParam('core.path'), '', $sUrl);
		$sUrl = str_replace('index.php?' . PHPFOX_GET_METHOD . '=', '', $sUrl);
		$sUrl = trim($sUrl, '/');
		
		return $sUrl;
	}
    
    /**
     * @return array
     */
	public function getNoFollows()
	{
		$sCacheId = $this->cache()->set('seo_nofollow');

		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('*')
				->from(Phpfox::getT('seo_nofollow'))
				->order('time_stamp')			
				->execute('getSlaveRows');
			$this->cache()->save($sCacheId, $aRows);
            Phpfox::getLib('cache')->group('seo', $sCacheId);
		}		
		return $aRows;
	}
    
    /**
     * @return array
     */
	public function getSiteMetas()
	{
		$sCacheId = $this->cache()->set('seo_meta');

        $aRows = $this->cache()->get($sCacheId);
        if ($aRows === false) {
			$aRows = $this->database()->select('*')
				->from(Phpfox::getT('seo_meta'))
				->order('time_stamp')
				->execute('getSlaveRows');
            if ($aRows === false) {
                $aRows = [];
            }
			$this->cache()->save($sCacheId, $aRows);
            Phpfox::getLib('cache')->group('seo', $sCacheId);
		}
		return $aRows;		
	}
}