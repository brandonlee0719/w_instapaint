<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Service\Genre;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class Genre extends \Phpfox_Service
{
    private $_sLanguageId = '';
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('music_genre');
        $this->_sLanguageId = Phpfox::getLanguageId();
    }

    public function getForEdit($iGenreId)
    {
        $sCacheId = $this->cache()->set('music_genre_edit_' . (int)$iGenreId . '_' . $this->_sLanguageId);
        if (!$aGenre = $this->cache()->get($sCacheId, Phpfox::getParam('music.genres_cache_time'))) {
            $aGenre = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('genre_id=' . (int)$iGenreId)
                ->execute('getSlaveRow');
            $this->cache()->save($sCacheId, $aGenre);
        }
        return $aGenre;
    }

    public function getForManage()
    {
        $sCacheId = $this->cache()->set('music_genre_manage_' . $this->_sLanguageId);
        if (!$aGenres = $this->cache()->get($sCacheId, Phpfox::getParam('music.genres_cache_time'))) {
            $aGenres = $this->database()->select('mc.*')
                ->from($this->_sTable, 'mc')
                ->where('true')
                ->order('mc.ordering ASC')
                ->group('mc.genre_id')
                ->execute('getSlaveRows');
            //Get number items used
            foreach ($aGenres as $iKey => $aGenre) {
                $iTotalUsed = $this->getTotalItemBelongToGenre($aGenre['genre_id']);
                $aGenres[$iKey]['used'] = $iTotalUsed;
                $aGenres[$iKey]['name'] = Phpfox::getSoftPhrase($aGenre['name']);
                $aGenres[$iKey]['url'] = Phpfox::permalink('music.genre', $aGenre['genre_id'],
                    Phpfox::getSoftPhrase($aGenre['name']));
            }
            $this->cache()->save($sCacheId, $aGenres);
        }
        return $aGenres;
    }

    /**
     * Get all genre for "Genre block" list And when upload a song
     * @param mixed $bCareActive
     * @param int $notInclude
     * @param boolean $bUseCache
     *
     * @return array
     */
    public function getList($bCareActive = 0, $notInclude = 0, $bUseCache = true)
    {
        $sCacheId = $this->cache()->set('music_genre_' . $this->_sLanguageId);
        if (!($aRows = $this->cache()->get($sCacheId, Phpfox::getParam('music.genres_cache_time'))) || !$bUseCache) {
            $aRows = $this->database()->select('genre_id, name')
                ->from($this->_sTable)
                ->order('ordering ASC')
                ->where('1=1 ' . ($bCareActive ? ' AND is_active = 1' : '') . ' AND genre_id <> ' . $notInclude)
                ->execute('getSlaveRows');

            $this->cache()->save($sCacheId, $aRows);
        }
        foreach ($aRows as $iKey => $aRow) {
            $aRows[$iKey]['name'] = Phpfox::getSoftPhrase($aRow['name']);
            $aRows[$iKey]['link'] = Phpfox::permalink('music.genre', $aRow['genre_id'],
                Phpfox::getSoftPhrase($aRow['name']));
            $aRows[$iKey]['category_id'] = $aRows[$iKey]['genre_id'];
            $aRows[$iKey]['url'] = $aRows[$iKey]['link'];
        }

        return $aRows;
    }

    /**
     * @deprecated
     * @param $iUserId
     */
    public function getUserGenre($iUserId)
    {

    }

    public function getGenre($iGenreId)
    {
        $aRow = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('genre_id = ' . (int)$iGenreId)
            ->execute('getSlaveRow');

        return (isset($aRow['genre_id']) ? $aRow : false);
    }

    public function getTotalItemBelongToGenre($iGenreId)
    {
        $iTotalUsed = $this->database()->select('count(*)')
            ->from(':music_genre_data')
            ->where('genre_id=' . (int)$iGenreId)
            ->execute('getSlaveField');
        return $iTotalUsed;
    }

    public function getGenreDetailBySong($iSongId)
    {
        $aGenres = db()->select('mg.genre_id,mg.name')
            ->from(':music_genre_data', 'mgd')
            ->join(':music_genre', 'mg', 'mg.genre_id = mgd.genre_id AND mg.is_active = 1')
            ->where('mgd.song_id =' . (int)$iSongId)
            ->execute('getSlaveRows');
        $aResult = [];
        if (count($aGenres)) {
            foreach ($aGenres as $aGenre) {
                $aResult[] = [
                    'genre_id' => $aGenre['genre_id'],
                    'name' => Phpfox::getSoftPhrase($aGenre['name'])
                ];
            }
        }

        return $aResult;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = \Phpfox_Plugin::get('music.service_genre_genre__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        \Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}