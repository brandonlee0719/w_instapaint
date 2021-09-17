<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Phpfox_Service
 * @version         $Id: process.class.php 7230 2014-03-26 21:14:12Z phpFox $
 */
abstract class Phpfox_Pages_Process extends Phpfox_Service
{
    protected $_bHasImage = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('pages');
    }

    /**
     * @return Phpfox_Pages_Facade
     */
    abstract public function getFacade();

    public function removeLogo($iPageId = null)
    {
        $aPage = $this->getFacade()->getItems()->getPage($iPageId);
        if (!isset($aPage['page_id'])) {
            return false;
        }

        $aPage['link'] = $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
            $aPage['vanity_url']);

        if (!$this->getFacade()->getItems()->isAdmin($aPage)) {
            return false;
        }

        $this->database()->update(Phpfox::getT('pages'), array('cover_photo_id' => '0', 'cover_photo_position' => null),
            'page_id = ' . (int)$iPageId);

        return $aPage;
    }

    public function deleteWidget($iId)
    {
        $aWidget = $this->database()->select('*')
            ->from(Phpfox::getT('pages_widget'))
            ->where('widget_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aWidget['widget_id'])) {
            return false;
        }

        $aPage = $this->getFacade()->getItems()->getPage($aWidget['page_id']);

        if (!isset($aPage['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_looking_for'));
        }

        if (!$this->getFacade()->getItems()->isAdmin($aPage)) {
            if (!Phpfox::isAdmin()) {
                return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_delete_this_widget'));
            }
        }

        $this->database()->delete(Phpfox::getT('pages_widget'), 'widget_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('pages_widget_text'), 'widget_id = ' . (int)$iId);

        return true;
    }

    public function addWidget($aVals, $iEditId = null)
    {
        $aPage = $this->getFacade()->getItems()->getPage($aVals['page_id']);

        if (!isset($aPage['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_looking_for'));
        }

        $bCanModerate = $this->getFacade()->getUserParam('can_moderate_pages');
        if ($bCanModerate === null) {
            $bCanModerate = $this->getFacade()->getUserParam('can_approve_pages') || $this->getFacade()->getUserParam('can_edit_all_pages') || $this->getFacade()->getUserParam('can_delete_all_pages');
        }

        if (!$this->getFacade()->getItems()->isAdmin($aPage) && !$bCanModerate) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_add_a_widget_to_this_page'));
        }

        if (empty($aVals['title'])) {
            Phpfox_Error::set($this->getFacade()->getPhrase('provide_a_title_for_your_widget'));
        }

        // parse content, remove script
        $aVals['text'] = preg_replace('/<script.*<\/script>/', '', $aVals['text']);
        
        if (empty($aVals['text'])) {
            Phpfox_Error::set($this->getFacade()->getPhrase('provide_content_for_your_widget'));
        }

        if (!$aVals['is_block']) {
            if (empty($aVals['menu_title'])) {
                Phpfox_Error::set($this->getFacade()->getPhrase('provide_a_menu_title_for_your_widget'));
            }

            if (empty($aVals['url_title'])) {
                Phpfox_Error::set($this->getFacade()->getPhrase('provide_a_url_title_for_your_widget'));
            }
        }

        if (Phpfox::isModule($aVals['url_title'])) {
            Phpfox_Error::set($this->getFacade()->getPhrase('you_cannot_use_this_url_for_your_widget'));
        }

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        $oFilter = Phpfox::getLib('parse.input');

        if ($iEditId !== null) {
            $sNewTitle = $this->database()->select('url_title')
                ->from(Phpfox::getT('pages_widget'))
                ->where('widget_id = ' . (int)$iEditId)
                ->execute('getSlaveField');
        }

        if (!$aVals['is_block'] && ($iEditId !== null && ($sNewTitle != $aVals['url_title']))) {
            $sNewTitle = Phpfox::getLib('parse.input')->prepareTitle('pages', $aVals['url_title'], 'url_title',
                Phpfox::getUserId(), Phpfox::getT('pages_widget'),
                'page_id = ' . (int)$aPage['page_id'] . ' AND url_title LIKE \'%' . $aVals['url_title'] . '%\'');
        }

        //Check duplicate widget title_url
        if (!$aVals['is_block']) {
            if ($iEditId) {
                $sMoreConds = ' AND widget_id !=' . (int)$iEditId;
            } else {
                $sMoreConds = '';
            }
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(':pages_widget')
                ->where('page_id=' . (int)$aPage['page_id'] . ' AND url_title="' . $aVals['url_title'] . '"' . $sMoreConds)
                ->executeField();

            if ($iCnt) {
                return Phpfox_Error::set(_p("The Url title is exist."));
            }
        }
        $aSql = array(
            'page_id'    => $aPage['page_id'],
            'title'      => $aVals['title'],
            'is_block'   => (int)$aVals['is_block'],
            'menu_title' => ($aVals['is_block'] ? null : $aVals['menu_title']),
            'url_title'  => ($aVals['is_block'] ? null : (isset($sNewTitle) ? $sNewTitle : $aVals['url_title']))
        );

        if ($iEditId === null) {
            $aSql['time_stamp'] = PHPFOX_TIME;
            $aSql['user_id'] = Phpfox::getUserId();

            $iId = $this->database()->insert(Phpfox::getT('pages_widget'), $aSql);

            $this->database()->insert(Phpfox::getT('pages_widget_text'), array(
                    'widget_id'   => $iId,
                    'text'        => $oFilter->clean($aVals['text']),
                    'text_parsed' => $oFilter->prepare($aVals['text'])
                )
            );
        } else {
            $this->database()->update(Phpfox::getT('pages_widget'), $aSql, 'widget_id = ' . (int)$iEditId);
            $this->database()->update(Phpfox::getT('pages_widget_text'), array(
                'text'        => $oFilter->clean($aVals['text']),
                'text_parsed' => $oFilter->prepare($aVals['text'])
            ), 'widget_id = ' . (int)$iEditId
            );

            $iId = $iEditId;
        }

        return $iId;
    }

    public function updateWidget($iId, $aVals)
    {
        return $this->addWidget($aVals, $iId);
    }

    public function updateCategory($iId, $aVals)
    {
        //Update phrase
        $aLanguages = Phpfox::getService('language')->getAll();
        if (Core\Lib::phrase()->isPhrase($aVals['name'])) {
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']])) {
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'],
                        $aVals['name'], $name);
                }
            }
        } else {
            //Add new phrase if before is not phrase
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = $this->getFacade()->getItemType() . '_category_' . md5('Pages/Groups Category' . $name . PHPFOX_TIME);
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.',
                        ['language_name' => $aLanguage['title']])));
                }
            }
            $aValsPhrase = [
                'product_id' => 'phpfox',
                'module'     => $this->getFacade()->getItemType() . '|' . $this->getFacade()->getItemType(),
                'var_name'   => $phrase_var_name,
                'text'       => $aText
            ];
            $aVals['name'] = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        }

        if (!empty($aVals['type_id'])) {
            $this->database()->update(Phpfox::getT('pages_category'), array(
                'type_id'   => (int)$aVals['type_id'],
                'name'      => $aVals['name'],
                'page_type' => isset($aVals['page_type']) ? (int)$aVals['page_type'] : 0
            ), 'category_id = ' . (int)$iId
            );

            // update item's type_id
            db()->update(':pages', ['type_id' => $aVals['type_id']], 'category_id = ' . (int)$iId);
        } else {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $this->getFacade()->getType()->deleteImage((int)$iId);
                $sFileName = $this->_processImage();
            }
            $this->database()->update(Phpfox::getT('pages_type'), array_merge([
                'name' => $aVals['name']
            ], !isset($sFileName) ? [] : [
                'image_path'      => $sFileName,
                'image_server_id' => \Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
            ]), 'type_id = ' . (int)$iId);
        }
        //remove category cache
        $this->cache()->remove();

        return true;
    }

    public function addCategory($aVals)
    {
        if (!isset($aVals['phrase_var_name'])) {
            //Add phrase for category
            $aLanguages = Phpfox::getService('language')->getAll();
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = $this->getFacade()->getItemType() . '_category_' . md5('Pages/Groups Category' . $name . PHPFOX_TIME);
            //Add phrases
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    return Phpfox_Error::set((_p('Provide a "{{ language_name }}" name.',
                        ['language_name' => $aLanguage['title']])));
                }
            }
            $aValsPhrase = [
                'var_name' => $phrase_var_name,
                'text'     => $aText
            ];
            $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        } else {
            $finalPhrase = $aVals['phrase_var_name'];
        }

        if (!empty($aVals['type_id'])) {
            $iId = $this->database()->insert(Phpfox::getT('pages_category'), array(
                    'type_id'   => (int)$aVals['type_id'],
                    'is_active' => isset($aVals['is_active']) ? $aVals['is_active'] : '1',
                    'name'      => $finalPhrase,
                    'page_type' => isset($aVals['page_type']) ? (int)$aVals['page_type'] : 0
                )
            );
        } else {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $sFileName = $this->_processImage();
            }
            $iId = $this->database()->insert(Phpfox::getT('pages_type'), array_merge([
                'is_active'  => isset($aVals['is_active']) ? $aVals['is_active'] : '1',
                'name'       => $finalPhrase,
                'time_stamp' => PHPFOX_TIME,
                'ordering'   => '0',
                'item_type'  => isset($aVals['item_type']) ? $aVals['item_type'] : $this->getFacade()->getItemTypeId(),
            ], !isset($sFileName) ? [] : [
                'image_path'      => $sFileName,
                'image_server_id' => \Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
            ]));
        }
        Core\Lib::phrase()->clearCache();
        $this->cache()->remove();
        return $iId;
    }

    private function _processImage()
    {
        // upload image
        $oImage = \Phpfox_Image::instance();
        $oFile = Phpfox_File::instance();
        $oFile->load('image', array('jpg', 'gif', 'png'),
            (Phpfox::getUserParam('user.max_upload_size_profile_photo') === 0 ? null : (Phpfox::getUserParam('user.max_upload_size_profile_photo') / 1024)));

        $sFileName = $oFile->upload('image', Phpfox::getParam('pages.dir_image'), '');
        $iFileSizes = filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));

        $iSize = 50;
        $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
            Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize, false);
        $iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));

        $iSize = 120;
        $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
            Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize, false);
        $iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));

        $iSize = 200;
        $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
            Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize, false);
        $iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));

        //Crop max width
        if (Phpfox::isModule('photo')) {
            Phpfox::getService('photo')->cropMaxWidth(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));
        }
        // Update user space usage
        Phpfox::getService('user.space')->update(Phpfox::getUserId(), $this->getFacade()->getItemType(), $iFileSizes);

        return str_replace(Phpfox::getParam('core.path_actual'), '', Phpfox::getParam('pages.url_image')) . $sFileName;
    }

    public function updateActivity($iId, $iType, $iSub)
    {
        Phpfox::isUser(true);
        $this->getFacade()->getUserParam('admincp.has_admin_access');

        $this->database()->update(($iSub ? Phpfox::getT('pages_category') : Phpfox::getT('pages_type')),
            array('is_active' => (int)($iType == '1' ? 1 : 0)),
            ($iSub ? 'category_id' : 'type_id') . ' = ' . (int)$iId);

        $this->cache()->remove();
    }

    public function deleteCategory($iId, $bIsSub = false)
    {
        if ($bIsSub) {
            //Delete phrase of category
            $aCategory = $this->database()->select('*')
                ->from(':pages_category')
                ->where('category_id=' . (int)$iId)
                ->execute('getSlaveRow');
            if (isset($aCategory['name']) && Core\Lib::phrase()->isPhrase($aCategory['name'])) {
                Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
            }
            $this->database()->delete(Phpfox::getT('pages_category'), 'category_id = ' . (int)$iId);
        } else {
            // delete category image
            $this->getFacade()->getType()->deleteImage((int)$iId);

            //Delete phrase of type
            $aType = $this->database()->select('*')
                ->from(':pages_type')
                ->where('type_id=' . (int)$iId)
                ->execute('getSlaveRow');
            if (isset($aType['name']) && Core\Lib::phrase()->isPhrase($aType['name'])) {
                Phpfox::getService('language.phrase.process')->delete($aType['name'], true);
            }
            $this->database()->delete(Phpfox::getT('pages_type'), 'type_id = ' . (int)$iId);
            //Delete phrase of all categories have this type
            $aCategories = $this->database()->select('*')
                ->from(':pages_category')
                ->where('type_id=' . (int)$iId)
                ->execute('getSlaveRows');
            if (is_array($aCategories) && count($aCategories)) {
                foreach ($aCategories as $aCategory) {
                    Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
                }
            }
            $this->database()->delete(Phpfox::getT('pages_category'), 'type_id = ' . (int)$iId);
        }

        $this->cache()->remove();

        return true;
    }

    public function add($aVals, $bIsApp = false)
    {
        $iViewId = ($this->getFacade()->getUserParam('approve_pages') ? '1' : '0');
        //Check last time created
        $iLastTimestamp = $this->database()->select('time_stamp')
            ->from(':pages')
            ->where('user_id=' . Phpfox::getUserId())
            ->order('time_stamp DESC')
            ->execute('getSlaveField');
        if ((PHPFOX_TIME - $iLastTimestamp) < 10) {
            return Phpfox_Error::set(_p('try_again_in_1_minute'));
        }
        if (empty($aVals['title'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('page_name_cannot_be_empty'));
        }

        if (defined('PHPFOX_APP_CREATED') || $bIsApp) {
            $iViewId = 0;
        }

        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_process_add_1')) {
            eval($sPlugin);
        }

        $aInsert = [
            'view_id'     => $iViewId,
            'type_id'     => (isset($aVals['type_id']) ? (int)$aVals['type_id'] : 0),
            'app_id'      => (isset($aVals['app_id']) ? (int)$aVals['app_id'] : 0),
            'category_id' => (isset($aVals['category_id']) ? (int)$aVals['category_id'] : 0),
            'user_id'     => Phpfox::getUserId(),
            'title'       => $this->preParse()->clean($aVals['title'], 255),
            'time_stamp'  => PHPFOX_TIME,
            'item_type'   => $this->getFacade()->getItemTypeId()
        ];

        $iId = $this->database()->insert($this->_sTable, $aInsert);

        $aInsertText = array('page_id' => $iId);
        if (isset($aVals['info'])) {
            $aInsertText['text'] = $this->preParse()->clean($aVals['info']);
            $aInsertText['text_parsed'] = $this->preParse()->prepare($aVals['info']);
        }
        $this->database()->insert(Phpfox::getT('pages_text'), $aInsertText);

        $sSalt = '';
        for ($i = 0; $i < 3; $i++) {
            $sSalt .= chr(rand(33, 91));
        }

        $sPossible = '23456789bcdfghjkmnpqrstvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $sPassword = '';
        $i = 0;
        while ($i < 10) {
            $sPassword .= substr($sPossible, mt_rand(0, strlen($sPossible) - 1), 1);
            $i++;
        }

        $iUserId = $this->database()->insert(Phpfox::getT('user'), array(
                'profile_page_id' => $iId,
                'user_group_id'   => NORMAL_USER_ID,
                'view_id'         => '7',
                'full_name'       => $this->preParse()->clean($aVals['title']),
                'joined'          => PHPFOX_TIME,
                'password'        => Phpfox::getLib('hash')->setHash($sPassword, $sSalt),
                'password_salt'   => $sSalt
            )
        );

        $aExtras = array(
            'user_id' => $iUserId
        );

        $this->database()->insert(Phpfox::getT('user_activity'), $aExtras);
        $this->database()->insert(Phpfox::getT('user_field'), $aExtras);
        $this->database()->insert(Phpfox::getT('user_space'), $aExtras);
        $this->database()->insert(Phpfox::getT('user_count'), $aExtras);
        $this->setDefaultPermissions($iId);

        $this->cache()->remove(array('user', $this->getFacade()->getItemType() . '_' . Phpfox::getUserId()));
        $this->cache()->remove($this->getFacade()->getItemType() . '_' . Phpfox::getUserId());
        $this->cache()->remove(array($this->getFacade()->getItemType(), Phpfox::getUserId()));

        if (!$this->getFacade()->getUserParam('approve_pages')) {
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), $this->getFacade()->getItemType());
        }

        Phpfox::getService('like.process')->add($this->getFacade()->getItemType(), $iId, null, null);

        return $iId;
    }

    public function update($iId, $aVals, $aPage)
    {
        if (!$this->_verify($aVals)) {
            return false;
        }
        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_process_update_0')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }

        $aUser = $this->database()->select('user_id')
            ->from(Phpfox::getT('user'))
            ->where('profile_page_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aUpdate = array(
            'type_id'     => (isset($aVals['type_id']) ? (int)$aVals['type_id'] : '0'),
            'category_id' => (isset($aVals['category_id']) ? (int)$aVals['category_id'] : 0),
            'reg_method'  => (isset($aVals['reg_method']) ? (int)$aVals['reg_method'] : 0),
            'privacy'     => (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0)
        );

        /* Only store the location if the admin has set a google key or ipinfodb key. This input is not always available */
        if (Phpfox::getParam('core.google_api_key') && isset($aVals['location'])) {
            if (isset($aVals['location']['name'])) {
                $aUpdate['location_name'] = $this->preParse()->clean($aVals['location']['name']);
            }
            if (isset($aVals['location']['latlng'])) {
                $aMatch = explode(',', $aVals['location']['latlng']);
                if (isset($aMatch[1])) {
                    $aUpdate['location_latitude'] = $aMatch[0];
                    $aUpdate['location_longitude'] = $aMatch[1];
                }
            }
        }

        if (isset($aVals['landing_page'])) {
            $aUpdate['landing_page'] = $aVals['landing_page'];
        }
        if (!empty($aVals['title'])) {
            $aUpdate['title'] = $this->preParse()->clean($aVals['title']);
        }

        if ($this->_bHasImage) {
            if (!empty($aPage['image_path'])) {
                $this->deleteImage($aPage);
            }

            $oImage = Phpfox_Image::instance();

            $sFileName = Phpfox_File::instance()->upload('image', Phpfox::getParam('pages.dir_image'), $iId);
            $iFileSizes = filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));

            $aUpdate['image_path'] = $sFileName;
            $aUpdate['image_server_id'] = Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID');
            $aUpdate['item_type'] = $this->getFacade()->getItemTypeId();

            $iSize = 50;
            $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
                Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize, false);
            $iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));

            $iSize = 120;
            $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
                Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize, false);
            $iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));

            $iSize = 200;
            $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
                Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize, false);
            $iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));
            //200 square
            $iSize = 200;
            $oImage->createThumbnail(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''),
                Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . '200_square'), $iSize, $iSize, false);
            $iFileSizes += filesize(Phpfox::getParam('pages.dir_image') . sprintf($sFileName, '_' . $iSize));
            //Crop max width
            if (Phpfox::isModule('photo')) {
                Phpfox::getService('photo')->cropMaxWidth(Phpfox::getParam('pages.dir_image') . sprintf($sFileName,
                        ''));
            }

            define('PHPFOX_PAGES_IS_IN_UPDATE', true);

            Phpfox::getService('user.process')->uploadImage($aUser['user_id'], true,
                Phpfox::getParam('pages.dir_image') . sprintf($sFileName, ''));

            // Update user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), $this->getFacade()->getItemType(),
                $iFileSizes);
        }

        $this->database()->update($this->_sTable, $aUpdate, 'page_id = ' . (int)$iId);

        $this->database()->update(Phpfox::getT('pages_text'), array(
            'text'        => $this->preParse()->clean($aVals['text']),
            'text_parsed' => $this->preParse()->prepare($aVals["text"])
        ), 'page_id = ' . (int)$iId);

        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_process_update_1')) {
            eval($sPlugin);
            if (isset($mReturnFromPlugin)) {
                return $mReturnFromPlugin;
            }
        }

        // Invite to page
        if ((isset($aVals['invite']) && is_array($aVals['invite'])) || (isset($aVals['emails']) && $aVals['emails'])) {
            // get invited friends, emails
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('pages_invite'))
                ->where('page_id = ' . (int)$iId)
                ->execute('getSlaveRows');
            $aInvited = array();
            foreach ($aInvites as $aInvite) {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }

            // invite friends
            if (isset($aVals['invite']) && is_array($aVals['invite'])) {
                $sUserIds = '';
                foreach ($aVals['invite'] as $iUserId) {
                    if (!is_numeric($iUserId)) {
                        continue;
                    }
                    $sUserIds .= $iUserId . ',';
                }
                $sUserIds = rtrim($sUserIds, ',');

                $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                    ->from(Phpfox::getT('user'))
                    ->where('user_id IN(' . $sUserIds . ')')
                    ->execute('getSlaveRows');

                $sLink = $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']);

                list(, $aMembers) = $this->getFacade()->getItems()->getMembers($aPage['page_id']);

                foreach ($aUsers as $aUser) {
                    if (in_array($aUser['user_id'], array_column($aMembers, 'user_id'))) {
                        continue;
                    }

                    if (isset($aCachedEmails[$aUser['email']])) {
                        continue;
                    }

                    if (isset($aInvited['user'][$aUser['user_id']])) {
                        continue;
                    }

                    $sMessage = $this->getFacade()->getPhrase('full_name_invited_you_to_the_page_title', [
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'title'     => $aPage['title']
                    ]);
                    $sMessage .= "\n" . $this->getFacade()->getPhrase('to_view_this_page_click_the_link_below_a_href_link_link_a',
                            ['link' => $sLink]) . "\n";

                    // add personal message
                    if (!empty($aVals['personal_message'])) {
                        $sMessage .= _p('full_name_added_the_following_personal_message',
                                ['full_name' => Phpfox::getUserBy('full_name')], $aUser['language_id'])
                            . $aVals['personal_message'];
                    }
                    // send email to user
                    Phpfox::getLib('mail')->to($aUser['user_id'])
                        ->subject($this->getFacade()->getPhrase('full_name_sent_you_a_page_invitation',
                            array('full_name' => Phpfox::getUserBy('full_name'))))
                        ->message($sMessage)
                        ->translated()
                        ->send();
                    // add to table pages_invite
                    $this->database()->insert(Phpfox::getT('pages_invite'), array(
                            'page_id'         => $iId,
                            'type_id'         => $this->getFacade()->getItemTypeId(),
                            'user_id'         => Phpfox::getUserId(),
                            'invited_user_id' => $aUser['user_id'],
                            'time_stamp'      => PHPFOX_TIME
                        )
                    );
                    // send notification
                    (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add($this->getFacade()->getItemType() . '_invite',
                        $iId, $aUser['user_id']) : null);
                }
            }

            // invite emails
            if (isset($aVals['emails']) && $aVals['emails']) {
                $aEmails = explode(',', $aVals['emails']);
                foreach ($aEmails as $sEmail) {
                    $sEmail = trim($sEmail);
                    if (!Phpfox::getLib('mail')->checkEmail($sEmail)) {
                        continue;
                    }

                    if (isset($aInvited['email'][$sEmail])) {
                        continue;
                    }

                    $sLink = $this->getFacade()->getItems()->getUrl($iId, $aPage['title'], $aPage['vanity_url']);

                    $sMessage = _p('full_name_invited_you_to_the_title', [
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'title'     => $aPage['title'],
                        'link'      => $sLink
                    ]);
                    if (!empty($aVals['personal_message'])) {
                        $sMessage .= _p('full_name_added_the_following_personal_message',
                                ['full_name' => Phpfox::getUserBy('full_name')])
                            . $aVals['personal_message'];
                    }
                    $oMail = Phpfox::getLib('mail');
                    if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1) {
                        $oMail->fromEmail(Phpfox::getUserBy('email'))
                            ->fromName(Phpfox::getUserBy('full_name'));
                    }
                    $bSent = $oMail->to($sEmail)
                        ->subject([
                            'event.full_name_invited_you_to_the_event_title',
                            [
                                'full_name' => Phpfox::getUserBy('full_name'),
                                'title'     => $aPage['title']
                            ]
                        ])
                        ->message($sMessage)
                        ->send();

                    if ($bSent) {
                        // cache email for not duplicate invite.
                        $aCachedEmails[$sEmail] = true;

                        $this->database()->insert(Phpfox::getT('pages_invite'), array(
                                'page_id'       => $iId,
                                'type_id'       => $this->getFacade()->getItemTypeId(),
                                'user_id'       => Phpfox::getUserId(),
                                'invited_email' => $sEmail,
                                'time_stamp'    => PHPFOX_TIME
                            )
                        );
                    }
                }
            }
            // notification message
            Phpfox::addMessage($this->getFacade()->getPhrase('invitations_sent_out'));
        }

        $aUserCache = array();
        // get old admins
        $aOldAdmins = db()->select('user_id')->from(':pages_admin')->where(['page_id' => (int)$iId])->executeRows();
        $this->database()->delete(Phpfox::getT('pages_admin'), 'page_id = ' . (int)$iId);
        $aAdmins = Phpfox_Request::instance()->getArray('admins');
        if (count($aAdmins)) {
            foreach ($aAdmins as $iAdmin) {
                if (isset($aUserCache[$iAdmin])) {
                    continue;
                }

                $aUserCache[$iAdmin] = true;
                //Add to member first
                $sType = $this->getFacade()->getItemType();
                //Check is liked
                $iCnt = $this->database()->select('COUNT(*)')
                    ->from(':like')
                    ->where('type_id="' . $sType . '" AND item_id=' . (int)$iId . " AND user_id=" . (int)$iAdmin)
                    ->executeField();
                if (!$iCnt) {
                    Phpfox::getService('like.process')->add($sType, $iId, $iAdmin);
                }
                // Notify to new admin for the first time
                if (!in_array($iAdmin, array_column($aOldAdmins, 'user_id'))) {
                    Phpfox::getService('notification.process')->add($this->getFacade()->getItemType() . '_invite_admin',
                        $iId, $iAdmin);
                }
                //Then add to admin
                $this->database()->insert(Phpfox::getT('pages_admin'), array('page_id' => $iId, 'user_id' => $iAdmin));
                // Notify to new admin
                Phpfox::getService('notification.process')->add('pages_invite_admin', $iId, $iAdmin);

                $this->cache()->remove(array('user', 'pages_' . $iAdmin));
                $this->cache()->remove(array('pages', $iAdmin));
            }
        }

        if (isset($aVals['perms'])) {
            $this->database()->delete(Phpfox::getT('pages_perm'), 'page_id = ' . (int)$iId);
            foreach ($aVals['perms'] as $sPermId => $iPermValue) {
                $this->database()->insert(Phpfox::getT('pages_perm'),
                    array('page_id' => (int)$iId, 'var_name' => $sPermId, 'var_value' => (int)$iPermValue));
            }
        }


        $this->database()->update(Phpfox::getT('user'),
            array('full_name' => Phpfox::getLib('parse.input')->clean($aVals['title'], 255)),
            'profile_page_id = ' . (int)$iId);

        return true;
    }

    public function deleteImage($aPage)
    {
        if (!empty($aPage['image_path'])) {
            $aImages = array(
                Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], ''),
                Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_50'),
                Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_120'),
                Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_200')
            );

            $iFileSizes = 0;
            foreach ($aImages as $sImage) {
                if (file_exists($sImage)) {
                    $iFileSizes += filesize($sImage);

                    Phpfox_File::instance()->unlink($sImage);
                }
                // http://www.phpfox.com/tracker/view/15187/
                if (Phpfox::getParam('core.allow_cdn')) {
                    Phpfox::getLib('cdn')->remove($sImage);
                }
            }

            if ($iFileSizes > 0) {
                Phpfox::getService('user.space')->update($aPage['user_id'], $this->getFacade()->getItemType(),
                    $iFileSizes, '-');
            }
        }

        $this->database()->update($this->_sTable, array('image_path' => null), 'page_id = ' . (int)$aPage['page_id']);

        return true;
    }

    public function updateTitle($iId, $sNewTitle)
    {
        if (!Phpfox::getService('ban')->check('username', $sNewTitle) || !Phpfox::getService('ban')->check('word',
                $sNewTitle)
        ) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('that_title_is_not_allowed'));
        }

        $aTitle = $this->database()->select('*')
            ->from(Phpfox::getT('pages_url'))
            ->where('page_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (isset($aTitle['vanity_url'])) {
            $this->database()->update(Phpfox::getT('pages_url'), array('vanity_url' => $sNewTitle),
                'page_id = ' . (int)$iId);
        } else {
            $this->database()->insert(Phpfox::getT('pages_url'),
                array('vanity_url' => $sNewTitle, 'page_id' => (int)$iId));
        }

        $this->database()->update(Phpfox::getT('user'), array('user_name' => $sNewTitle),
            'profile_page_id = ' . (int)$iId);

        return true;
    }

    /**
     * @deprecated This function will be removed in version 4.6.0
     * @param $iId
     * @return bool
     */
    public function register($iId)
    {
        $aPage = $this->database()->select('*')
            ->from(Phpfox::getT('pages'))
            ->where('page_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aPage['page_id'])) {
            return false;
        }

        $iId = $this->database()->insert(Phpfox::getT('pages_signup'), array(
                'page_id'    => $iId,
                'user_id'    => Phpfox::getUserId(),
                'time_stamp' => PHPFOX_TIME
            )
        );

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add($this->getFacade()->getItemType() . '_register', $iId,
                $aPage['user_id']);

            $aAdmins = $this->database()->select('*')
                ->from(Phpfox::getT('pages_admin'))
                ->where('page_id = ' . (int)$aPage['page_id'])
                ->execute('getSlaveRows');
            foreach ($aAdmins as $aAdmin) {
                if ($aAdmin['user_id'] == $aPage['user_id']) {
                    continue;
                }

                Phpfox::getService('notification.process')->add($this->getFacade()->getItemType() . '_register', $iId,
                    $aAdmin['user_id']);
            }
        }

        return true;
    }

    /**
     * Mass action moderations
     * @param $aModerations
     * @param $sAction
     * @return bool
     */
    public function moderation($aModerations, $sAction)
    {
        $iCnt = 0;
        if (is_array($aModerations) && count($aModerations)) {
            foreach ($aModerations as $iModeration) {
                $iCnt++;
                $aPage = $this->database()->select('p.*, ps.user_id AS post_user_id')
                    ->from(Phpfox::getT('pages_signup'), 'ps')
                    ->join(Phpfox::getT('pages'), 'p', 'p.page_id = ps.page_id')
                    ->where('ps.signup_id = ' . (int)$iModeration)
                    ->execute('getSlaveRow');

                if (!isset($aPage['page_id'])) {
                    return Phpfox_Error::display($this->getFacade()->getPhrase('unable_to_find_the_page'));
                }

                if (!$this->getFacade()->getItems()->isAdmin($aPage)) {
                    return Phpfox_Error::display($this->getFacade()->getPhrase('unable_to_moderate_this_page'));
                }

                if ($sAction == 'approve') {
                    Phpfox::getService('like.process')->add($this->getFacade()->getItemType(), $aPage['page_id'],
                        $aPage['post_user_id'], null, ['ignoreCheckPermission' => true]);
                }

                Phpfox::getService('notification.process')->delete($this->getFacade()->getItemType() . '_register',
                    $iModeration, Phpfox::getUserId());
                $this->database()->delete(Phpfox::getT('pages_signup'), 'signup_id = ' . (int)$iModeration);
            }
        }

        return true;
    }

    public function login($iPageId)
    {
        $aPage = $this->database()->select('p.*, p.user_id AS owner_user_id, u.*')
            ->from(Phpfox::getT('pages'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
            ->where('p.page_id = ' . (int)$iPageId)
            ->execute('getSlaveRow');

        if (!isset($aPage['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_trying_to_login_to'));
        }

        $iCurrentUserId = Phpfox::getUserId();

        $bCanLogin = false;
        if ($aPage['owner_user_id'] == Phpfox::getUserId()) {
            $bCanLogin = true;
        }

        if (!$bCanLogin) {
            $iAdmin = (int)$this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('pages_admin'))
                ->where('page_id = ' . (int)$aPage['page_id'] . ' AND user_id = ' . (int)Phpfox::getUserId())
                ->execute('getSlaveField');

            if ($iAdmin) {
                $bCanLogin = true;
            }
        }

        if (!$bCanLogin) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_log_in_as_this_page'));
        }

        if (Phpfox::getParam('core.auth_user_via_session')) {
            $this->database()->delete(Phpfox::getT('session'), 'user_id = ' . (int)Phpfox::getUserId());
            $this->database()->insert(Phpfox::getT('session'), array(
                    'user_id'       => $aPage['user_id'],
                    'last_activity' => PHPFOX_TIME,
                    'id_hash'       => Phpfox_Request::instance()->getIdHash()
                )
            );
        }

        $sPasswordHash = Phpfox::getLib('hash')->setRandomHash(Phpfox::getLib('hash')->setHash($aPage['password'],
            $aPage['password_salt']));

        $iTime = 0;

        $aUserCookieNames = Phpfox::getService('user.auth')->getCookieNames();

        Phpfox::setCookie($aUserCookieNames[0], $aPage['user_id'], $iTime);
        Phpfox::setCookie($aUserCookieNames[1], $sPasswordHash, $iTime);

        Phpfox::getLib('session')->remove(Phpfox::getParam('core.theme_session_prefix') . 'theme');

        $this->database()->update(Phpfox::getT('user'), array('last_login' => PHPFOX_TIME),
            'user_id = ' . $aPage['user_id']);
        $this->database()->insert(Phpfox::getT('user_ip'), array(
                'user_id'    => $aPage['user_id'],
                'type_id'    => 'login',
                'ip_address' => Phpfox::getIp(),
                'time_stamp' => PHPFOX_TIME
            )
        );

        $iLoginId = $this->database()->insert(Phpfox::getT('pages_login'), array(
                'page_id'    => $aPage['page_id'],
                'user_id'    => $iCurrentUserId,
                'time_stamp' => PHPFOX_TIME
            )
        );

        Phpfox::setCookie('page_login', $iLoginId, $iTime);

        return true;
    }

    public function clearLogin($iUserId)
    {
        $this->database()->delete(Phpfox::getT('pages_login'), 'user_id = ' . (int)$iUserId);

        Phpfox::setCookie('page_login', '', -1);
    }

    public function delete($iId, $bDoCallback = true)
    {
        $aPage = $this->database()->select('*')
            ->from(Phpfox::getT('pages'))
            ->where('page_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aPage['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_trying_to_delete'));
        }

        $bCanModerate = $this->getFacade()->getUserParam('can_moderate_pages');
        if ($bCanModerate === null) {
            $bCanModerate = $this->getFacade()->getUserParam('can_approve_pages') || $this->getFacade()->getUserParam('can_edit_all_pages') || $this->getFacade()->getUserParam('can_delete_all_pages');
        }

        if ($aPage['user_id'] == Phpfox::getUserId() || $bCanModerate) {
            $iUser = $this->database()->select('user_id')->from(Phpfox::getT('user'))->where('profile_page_id = ' . (int)$aPage['page_id'] . ' AND view_id = 7')->execute('getSlaveField');

            $this->database()->delete(Phpfox::getT('pages_url'), 'page_id = ' . (int)$aPage['page_id']);
            $this->database()->delete(Phpfox::getT('feed'),
                'type_id = \'' . $this->getFacade()->getItemType() . '_itemLiked\' AND item_id = ' . (int)$aPage['page_id']);

            if (((int)$iUser) > 0 && $bDoCallback === true) {
                Phpfox::massCallback('onDeleteUser', $iUser);
            }
            if ($bDoCallback) {
                Phpfox::massCallback('onDeletePage', $iId, $this->getFacade()->getItemType());
            }

            $this->deleteImage($aPage);

            $this->database()->delete(Phpfox::getT('pages'), 'page_id = ' . $aPage['page_id']);

            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), $this->getFacade()->getItemType(), '-');

            (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete($this->getFacade()->getItemType(),
                (int)$aPage['page_id'], 0, true) : null);

            $this->cache()->remove(array($this->getFacade()->getItemType(), $aPage['user_id']));

            return true;
        }

        return Phpfox_Error::set($this->getFacade()->getPhrase('you_are_unable_to_delete_this_page'));
    }

    public function approve($iId)
    {
        $bCanModerate = $this->getFacade()->getUserParam('can_moderate_pages');
        if ($bCanModerate === null) {
            $bCanModerate = $this->getFacade()->getUserParam('can_approve_pages');
        }

        if (!$bCanModerate) {
            return false;
        }

        $aPage = $this->getFacade()->getItems()->getPage($iId);

        if (!isset($aPage['page_id'])) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('unable_to_find_the_page_you_are_trying_to_approve'));
        }

        if ($aPage['view_id'] != '1') {
            return false;
        }

        $this->database()->update(Phpfox::getT('pages'), array('view_id' => '0', 'time_stamp' => PHPFOX_TIME),
            'page_id = ' . $aPage['page_id']);

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add($this->getFacade()->getItemType() . '_approved',
                $aPage['page_id'], $aPage['user_id']);
        }

        Phpfox::getService('user.activity')->update($aPage['user_id'], $this->getFacade()->getItemType());

        (($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_process_approve__1')) ? eval($sPlugin) : false);

        // Send the user an email
        $sLink = $this->getFacade()->getItems()->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        Phpfox::getLib('mail')->to($aPage['user_id'])
            ->subject($this->getFacade()->getPhrase('page_title_approved', array('title' => $aPage['title'])))
            ->message($this->getFacade()->getPhrase('your_page_title_has_been_approved',
                array('title' => $aPage['title'], 'link' => $sLink)))
            ->translated()
            ->send();

        return true;
    }

    /* Claim status:
            1: Not defined
            2: Approved
            3: Denied
    */
    public function approveClaim($iClaimId)
    {
        // get the claim
        $aClaim = $this->database()->select('pc.*, p.user_id as old_user_id')
            ->from(Phpfox::getT('pages_claim'), 'pc')
            ->join(':pages', 'p', 'p.page_id=pc.page_id')
            ->where('claim_id = ' . (int)$iClaimId . ' AND status_id = 1')
            ->execute('getSlaveRow');

        if (empty($aClaim)) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('not_a_valid_claim'));
        }

        // set the user_id to the page
        $this->database()->update(Phpfox::getT('pages'), array('user_id' => $aClaim['user_id']),
            'page_id = ' . $aClaim['page_id']);
        $this->database()->update(Phpfox::getT('pages_claim'), array('status_id' => 2), 'claim_id = ' . (int)$iClaimId);

        //update user activity
        Phpfox::getService('user.activity')->update($aClaim['user_id'], 'pages');
        Phpfox::getService('user.activity')->update($aClaim['old_user_id'], 'pages', '-');

        // send notification to claimer
        Phpfox::getService('notification.process')->add('pages_approve_claim', $aClaim['page_id'], $aClaim['user_id']);
        // send notification to old owner
        Phpfox::getService('notification.process')->add('pages_remove_owner', $aClaim['page_id'],
            $aClaim['old_user_id']);

        return true;
    }

    public function denyClaim($iClaimId)
    {
        // get the claim
        $aClaim = $this->database()->select('*')
            ->from(Phpfox::getT('pages_claim'))
            ->where('claim_id = ' . (int)$iClaimId . ' AND status_id = 1')
            ->execute('getSlaveRow');

        if (empty($aClaim)) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('not_a_valid_claim'));
        }

        // set the user_id to the page
        $this->database()->update(Phpfox::getT('pages_claim'), array('status_id' => 3), 'claim_id = ' . (int)$iClaimId);

        // send notification
        Phpfox::getService('notification.process')->add('pages_deny_claim', $aClaim['page_id'], $aClaim['user_id']);

        return true;
    }

    /**
     * param $bAjaxPageUpload
     * @param $iPageId
     * @param $iPhotoId
     * @param bool $bIsAjaxPageUpload
     * @return bool
     */
    public function setCoverPhoto($iPageId, $iPhotoId, $bIsAjaxPageUpload = false)
    {
        $bCanModerate = $this->getFacade()->getUserParam('can_moderate_pages');
        if ($bCanModerate === null) {
            $bCanModerate = $this->getFacade()->getUserParam('can_approve_pages') || $this->getFacade()->getUserParam('can_edit_all_pages') || $this->getFacade()->getUserParam('can_delete_all_pages');
        }

        if (!$this->getFacade()->getItems()->isAdmin($iPageId) && !Phpfox::isAdmin() && !$bCanModerate) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('user_is_not_an_admin'));
        }

        if ($bIsAjaxPageUpload == false) {
            // check that this photo belongs to this page
            $iPhotoId = $this->database()->select('photo_id')
                ->from(Phpfox::getT('photo'))
                ->where('module_id = \'' . $this->getFacade()->getItemType() . '\' AND group_id = ' . (int)$iPageId . ' AND photo_id = ' . (int)$iPhotoId)
                ->execute('getSlaveField');
        }

        if (!empty($iPhotoId)) {
            $this->database()->update(Phpfox::getT('pages'),
                array('cover_photo_position' => '', 'cover_photo_id' => (int)$iPhotoId), 'page_id = ' . (int)$iPageId);
            return true;
        }

        return Phpfox_Error::set($this->getFacade()->getPhrase('the_photo_does_not_belong_to_this_page'));
    }

    public function updateCoverPosition($iPageId, $iPosition)
    {
        if (!$this->getFacade()->getItems()->isAdmin($iPageId) && !Phpfox::isAdmin()) {
            return Phpfox_Error::set($this->getFacade()->getPhrase('user_is_not_an_admin'));
        }
        $this->database()->update(Phpfox::getT('pages'), array(
            'cover_photo_position' => (int)$iPosition
        ), 'page_id = ' . (int)$iPageId);

        return true;
    }

    public function removeCoverPhoto($iPageId)
    {
        if (!Phpfox::isAdmin()) {
            $bIsAdmin = $this->database()->select('user_id')
                ->from(Phpfox::getT('pages_admin'))
                ->where('page_id = ' . (int)$iPageId . ' AND user_id = ' . Phpfox::getUserId())
                ->execute('getSlaveField');

            if (empty($bIsAdmin)) {
                return Phpfox_Error::set($this->getFacade()->getPhrase('user_is_not_an_admin'));
            }
        }

        $this->database()->update(Phpfox::getT('pages'), array('cover_photo_id' => '', 'cover_photo_position' => ''),
            'page_id = ' . (int)$iPageId);
        return true;
    }

    /**
     * set default permissions for page/group
     * @param integer $iPageId is the ID of page/group
     * @return bool
     */
    public function setDefaultPermissions($iPageId)
    {
        $iDefaultValue = 0;
        $aPermissions = [];
        switch ($this->getFacade()->getItemType()) {
            case 'pages':
                $iDefaultValue = 0;
                $aPermissions = $this->getFacade()->getItems()->getPerms($iPageId);
                break;

            case 'groups':
                $iDefaultValue = 1;
                $aPermissions = Core\Lib::appsGroup()->getPerms($iPageId);
                break;
        }
        foreach ($aPermissions as $aPerm) {
            $this->database()->insert(Phpfox::getT('pages_perm'),
                array('page_id' => (int)$iPageId, 'var_name' => $aPerm['id'], 'var_value' => $iDefaultValue));
        }
        return true;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get($this->getFacade()->getItemType() . '.service_process__call')) {
            eval($sPlugin);
            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * Verify params on update page/group
     * @param $aVals
     * @return bool
     */
    private function _verify($aVals)
    {
        $bValid = true;
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != '')) {
            $aImage = Phpfox_File::instance()->load('image', ['jpg', 'gif', 'png'],
                ($this->getFacade()->getUserParam('max_upload_size_pages') == 0 ? null : ($this->getFacade()->getUserParam('max_upload_size_pages') / 1024)));

            if ($aImage === false) {
                $bValid = false;
            }

            $this->_bHasImage = true;
        }

        if (empty($aVals['title'])) {
            Phpfox_Error::set(_p('invalid_page_title'));
            $bValid = false;
        }

        return $bValid;
    }

    /**
     * update album id of cover photo
     * @param $iPhotoId
     * @param $iGroupId
     */
    public function updateCoverPhoto($iPhotoId, $iGroupId)
    {
        $iUserId = $this->getFacade()->getItems()->getUserId($iGroupId);
        $iAlbumId = db()->select('album_id')->from(':photo_album')
            ->where(['module_id' => $this->getFacade()->getItemType(), 'group_id' => $iGroupId, 'cover_id' => $iUserId])
            ->executeField();
        if (empty($iAlbumId)) {
            $iAlbumId = db()->insert(':photo_album', [
                'module_id'       => $this->getFacade()->getItemType(),
                'group_id'        => $iGroupId,
                'privacy'         => '0',
                'privacy_comment' => '0',
                'user_id'         => $iUserId,
                'name'            => "{_p var='cover_photo'}",
                'time_stamp'      => PHPFOX_TIME,
                'cover_id'        => $iUserId,
                'total_photo'     => 0
            ]);
            db()->insert(':photo_album_info', array('album_id' => $iAlbumId));
        }
        db()->update(':photo', ['is_cover' => 0], 'album_id=' . (int)$iAlbumId);
        db()->update(':photo', [
            'album_id'         => $iAlbumId,
            'is_cover'         => 1,
            'is_profile_photo' => 0,
            'view_id' => 0
        ], 'photo_id=' . (int)$iPhotoId);

        Phpfox::getService('photo.album.process')->updateCounter((int)$iAlbumId, 'total_photo');
    }
}
