<?php

namespace Core\Language;

/**
 * Class Language
 *
 * @author  Neil <neil@phpfox.com>
 * @package Core\Language
 */
abstract class Language
{
    /**
     * @var array
     */
    protected $_aPhrases = [];

    /**
     * @var string
     */
    protected $_sDirection = 'ltr';

    /**
     * @var string
     */
    protected $_sLanguageId = '';

    /**
     * @var string
     */
    protected $_sTitle = '';

    /**
     * @var string
     */
    protected $_sCreated = 'phpFox';

    /**
     * @var string
     */
    protected $_sCharset = 'UTF-8';

    /**
     * @var string
     */
    protected $_sLanguageCode = '';

    /**
     * @var string
     */
    protected $_sFlagId = 'png';

    /**
     * @var string
     */
    protected $_sVersion = '';

    /**
     * Language constructor.
     */
    public function __construct()
    {
        if (empty($this->_sLanguageCode)) {
            $this->_sLanguageCode = $this->_sLanguageId;
        }
        $this->setPhrases();
        $this->init();
    }

    /**
     * @return void
     */
    abstract protected function setPhrases();

    /**
     * @return void
     */
    abstract protected function init();

    /**
     * Install or Upgrade this package
     *
     * @return bool
     */
    public function install()
    {
        if (!$this->isValid()) {
            return false;
        }
        //Add to table language
        $iCnt = db()
            ->select('COUNT(*)')
            ->from(':language')
            ->where("language_id='" . $this->_sLanguageId . "'")
            ->executeField();
        if ($iCnt) {
            //Mean upgrade
            $aUpdate = [
                'title'         => $this->_sTitle,
                'language_code' => $this->_sLanguageCode,
                'charset'       => $this->_sCharset,
                'direction'     => $this->_sDirection,
                'flag_id'       => $this->_sFlagId,
                'created'       => $this->_sCreated,
            ];
            db()->update(":language", $aUpdate, "language_id='" . $this->_sLanguageId . "'");
            foreach ($this->_aPhrases as $sVarName => $sValue) {
                $iCntPhrase = db()
                    ->select('COUNT(*)')
                    ->from(':language_phrase')
                    ->where("var_name='" . $sVarName . "' AND language_id='" . $this->_sLanguageId . "'")
                    ->executeField();
                if ($iCntPhrase) {
                    $aUpdate = [
                        'text'         => $sValue,
                        'text_default' => $sValue,
                    ];
                    db()->insert(':language_phrase', $aUpdate, "var_name='" . $sVarName . "' AND language_id='" . $this->_sLanguageId . "'");
                } else {
                    $aInsert = [
                        'language_id'  => $this->_sLanguageId,
                        'var_name'     => $sVarName,
                        'text'         => $sValue,
                        'text_default' => $sValue,
                        'added'        => time(),
                    ];
                    db()->insert(':language_phrase', $aInsert);
                }
            }
        } else {
            //Mean install new
            $aInsert = [
                'language_id'   => $this->_sLanguageId,
                'title'         => $this->_sTitle,
                'language_code' => $this->_sLanguageCode,
                'charset'       => $this->_sCharset,
                'direction'     => $this->_sDirection,
                'flag_id'       => $this->_sFlagId,
                'created'       => $this->_sCreated,
            ];
            db()->insert(":language", $aInsert);
            foreach ($this->_aPhrases as $sVarName => $sValue) {
                $aInsert = [
                    'language_id'  => $this->_sLanguageId,
                    'var_name'     => $sVarName,
                    'text'         => $sValue,
                    'text_default' => $sValue,
                    'added'        => time(),
                ];
                db()->insert(':language_phrase', $aInsert);
            }
        }
        //Add to table language_phrase
        return true;
    }

    /**
     * Check is language package is valid
     *
     * @return bool
     */
    public function isValid()
    {
        if (empty($this->_aPhrases)) {
            return false;
        }
        if (empty($this->_sLanguageId)) {
            return false;
        }
        if (empty($this->_sTitle)) {
            return false;
        }
        if (empty($this->_sVersion)) {
            return false;
        }
        return true;
    }
}