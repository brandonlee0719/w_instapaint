<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Service_Word
 */
class Ban_Service_Word extends Phpfox_Service
{
    /**
     * @var string
     */
    protected $_sTable = '';

    /**
     * @var array
     */
    private $_aWords = array();

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ban');
    }

    /**
     * @param string $sTxt
     *
     * @return string
     */
    function clean($sTxt)
    {
        if ($this->_aWords) {
            return $this->_parseString($sTxt);
        }

        $sCacheId = $this->cache()->set("ban_word");

        $this->_aWords = $this->cache()->get($sCacheId);
        if ($this->_aWords === false) {
            $aRows = $this->database()->select('find_value, replacement')
                ->from($this->_sTable)
                ->where("type_id = 'word'")
                ->executeRows();
            foreach ($aRows as $aRow) {
                $this->_aWords[Phpfox::getLib('parse.input')->reversePrepare($aRow['find_value'])] = Phpfox::getLib('parse.input')->reversePrepare($aRow['replacement']);
            }
            if ($this->_aWords === false) {
                $this->_aWords = [];
            }
            $this->cache()->save($sCacheId, $this->_aWords);
            Phpfox::getLib('cache')->group('ban', $sCacheId);
        }

        return $this->_parseString($sTxt);
    }

    /**
     * @param string $sTxt
     *
     * @return string
     */
    function _parseString($sTxt)
    {
        if (!is_array($this->_aWords)) {
            return $sTxt;
        }

        if (!count($this->_aWords)) {
            return $sTxt;
        }

        foreach ($this->_aWords as $sFilter => $mValue) {
            $sFilter = str_replace("/", "\/", $sFilter);
            $sFilter = str_replace('&#42;', '*', $sFilter);
            if (preg_match('/\*/i', $sFilter)) {
                $sFilter = str_replace(array('.', '*'), array('\.', '([a-zA-Z@]?)'), $sFilter);
                $sTxt = preg_replace('/' . $sFilter . '/is', ' ' . $mValue . ' ', $sTxt);
            } else {
                $sTxt = preg_replace("/(\b)" . $sFilter . "(\b)/i", '${1}' . $mValue . '${2}', $sTxt);
                $sTxt = ltrim($sTxt);
                $sTxt = rtrim($sTxt);
            }
        }

        return $sTxt;
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
        if ($sPlugin = Phpfox_Plugin::get('ban.service_word__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
