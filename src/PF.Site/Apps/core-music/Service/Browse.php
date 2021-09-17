<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Service;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');


class Browse extends \Phpfox_Service
{
    private $_aConditions = array();

    private $_iCnt = 0;

    private $_iPage = 0;

    private $_iPageSize = 25;

    private $_sOrder = 'u.joined DESC';

    private $_aRows = array();

    private $_sGenre = null;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('user');
    }

    public function condition($aConditions)
    {
        $this->_aConditions = $aConditions;

        return $this;
    }

    public function page($iPage)
    {
        $this->_iPage = $iPage;

        return $this;
    }

    public function size($iPageSize)
    {
        $this->_iPageSize = $iPageSize;

        return $this;
    }

    public function genre($sGenre)
    {
        if ($sGenre != 'browse') {
            $this->_sGenre = $sGenre;
        }

        return $this;
    }

    public function order($sOrder)
    {
        $this->_sOrder = $sOrder;

        return $this;
    }

    public function execute()
    {
        $this->_iCnt = $this->database()->select(($this->_sGenre !== null ? 'COUNT(*)' : 'COUNT(*)'))
            ->from($this->_sTable, 'u')
            ->where($this->_aConditions)
            ->execute('getSlaveField');

        if ($this->_iCnt) {
            if ($this->_sGenre !== null) {
                $this->database()
                    ->select('mg.name AS genre_name, ');
            }

            $aRows = $this->database()->select(Phpfox::getUserField() . ', u.country_iso')
                ->from($this->_sTable, 'u')
                ->where($this->_aConditions)
                ->order($this->_sOrder)
                ->limit($this->_iPage, $this->_iPageSize, $this->_iCnt)
                ->execute('getSlaveRows');

            $sUserIds = '';
            foreach ($aRows as $aRow) {
                $this->_aRows[$aRow['user_id']] = $aRow;

                $sUserIds .= $aRow['user_id'] . ',';
            }

            $aGenres = $this->database()->select('mg.name')
                ->from(Phpfox::getT('music_genre'), 'mg')
                ->execute('getSlaveRows');

            foreach ($aGenres as $aGenre) {
                $this->_aRows[$aGenre['user_id']]['genres'][] = $aGenre;
            }
        }
    }

    public function get()
    {
        return $this->_aRows;
    }

    public function getCount()
    {
        return $this->_iCnt;
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
        if ($sPlugin = \Phpfox_Plugin::get('music.service_browse__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        \Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}