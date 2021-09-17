<?php

namespace Apps\PHPfox_IM\Controller;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AdminImportDataController extends \Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $bIsImportDirectly = (boolean)$this->request()->get('submit');
        $bIsExport = (boolean)$this->request()->get('export_json');
        $aExport = [];

        // check if table im exist, if not we have no data to import
        if (!db()->tableExists(Phpfox::getT('im'))) {
            $this->template()->assign([
                'bNoImTable' => true,
                'sTableIm' => Phpfox::getT('im')
            ]);
            return;
        }

        // check if PhpRedis is enable, to use import directly
        if (class_exists('Redis')) {
            $redis = new \Redis();
        } else {
            $this->template()->assign('bNoRedis', true);
        }

        if ($bIsImportDirectly && isset($redis)) {
            $bIsImportDirectly = false;
            if ($redis->connect($this->request()->get('redis_host'), $this->request()->get('redis_port'))) {
                if ($this->request()->get('redis_password', false)) {
                    $bIsImportDirectly = $redis->auth($this->request()->get('redis_password'));
                } else {
                    $bIsImportDirectly = true;
                }
            }
            // cannot connect to redis server
            if (!$bIsImportDirectly) {
                \Phpfox_Error::set(_p('cannot_connect_to_redis'));
            }
        }

        // process when import directly or export json
        if ($bIsImportDirectly || $bIsExport) {
            $aUsers = db()->select('parent_id, user_id, time_stamp')
                ->from(Phpfox::getT('im'))->executeRows();

            $iLastParentId = 0;
            $iLastUserId = 0;
            $aUsersThreads = [];
            $aUsersPhotos = [];
            foreach ($aUsers as $aUser) {
                if ($iLastParentId !== $aUser['parent_id']) {
                    $iLastParentId = $aUser['parent_id'];
                    $iLastUserId = $aUser['user_id'];
                } else {
                    $sThreadId = "$iLastUserId:$aUser[user_id]";
                    if ($bIsImportDirectly && isset($redis)) {
                        $aLastUserThreads = $redis->lRange("threads:$iLastUserId", 0, -1);
                        if (in_array("$aUser[user_id]:$iLastUserId", $aLastUserThreads)) {
                            $sThreadId = "$aUser[user_id]:$iLastUserId";
                        }
                    }

                    // Insert messages
                    $aMessages = db()->select('user_id, text, time_stamp')->from(Phpfox::getT('im_text'))
                        ->where(['parent_id' => $iLastParentId])->executeRows();
                    foreach ($aMessages as $aMessage) {
                        if (!array_key_exists($aMessage['user_id'], $aUsersPhotos)) {
                            $aUsersPhotos[$aMessage['user_id']] = $aUserProfile = $this->getUserProfile($aMessage['user_id']);
                        } else {
                            $aUserProfile = $aUsersPhotos[$aMessage['user_id']];
                        }
                        $aExportMessage = [
                            'text'          => $aMessage['text'],
                            'user'          => $aUserProfile,
                            'time_stamp'    => $aMessage['time_stamp'] * 1000,
                            'thread_id'     => $sThreadId,
                            'attachment_id' => 0,
                            'listing_id'    => 0,
                            'deleted'       => false
                        ];
                        if ($bIsImportDirectly) {
                            // check exist thread
                            $redis->zAdd("message:$sThreadId", $aMessage['time_stamp'] * 1000,
                                json_encode($aExportMessage));
                        } else {
                            $aExport['message'][] = $aExportMessage;
                        }
                    }

                    // Import thread
                    $aThread = [
                        'thread_id'    => $sThreadId,
                        'listing_id'   => 0,
                        'created'      => $aUser['time_stamp'] * 1000,
                        'users'        => [$iLastUserId, $aUser['user_id']],
                        'preview'      => end($aMessages)['text'],
                        'updated'      => null,
                        'notification' => $sThreadId
                    ];
                    if ($bIsImportDirectly && $redis->get("thread:$sThreadId") === false) {
                        $redis->set("thread:$sThreadId", json_encode($aThread));
                    } else {
                        $aExport["thread"][] = $aThread;
                    }

                    // Threads of each user
                    $aUsersThreads[$iLastUserId][$sThreadId] = $aUsersThreads[$aUser['user_id']][$sThreadId] = end($aMessages)['time_stamp'] * 1000;
                }
            }
            // add user's threads of each user
            foreach ($aUsersThreads as $iUserId => $aThreads) {
                asort($aThreads);
                foreach ($aThreads as $sThreadId => $iTimeStamp) {
                    if ($bIsImportDirectly && isset($redis)) {
                        if (!in_array($sThreadId, $redis->lRange("threads:$iUserId", 0, -1))) {
                            $redis->lPush("threads:$iUserId", $sThreadId);
                        }
                    } else {
                        $aExport['threads'][$iUserId][] = $sThreadId;
                    }
                }
            }
            if ($bIsExport) {
                $data = json_encode($aExport);
                $sFilePath = PHPFOX_DIR_FILE . 'im-data.json';
                \Phpfox_File::instance()->write($sFilePath, $data);
                \Phpfox_File::instance()->forceDownload($sFilePath, 'im-data.json');
            } else {
                Phpfox::addMessage(_p('import_data_successfully'));
            }
        }
    }

    /**
     * Get user photo
     * @param $iUserId
     * @return array
     */
    public function getUserProfile($iUserId)
    {
        $dom = new \DOMDocument();
        $aUser = (new \Api\User())->get($iUserId);
        $sUserPhoto = $aUser->photo_link;
        $dom->loadHTML($sUserPhoto);
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $link->setAttribute('target', '_blank');
        }
        # remove <!DOCTYPE
        $dom->removeChild($dom->doctype);
        # remove <html><body></body></html>
        $dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);

        return [
            'id' => $aUser->id,
            'name' => $aUser->name,
            'photo_link' => $dom->saveHTML()
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('im.component_controller_admincp_manage_sound_clean')) ? eval($sPlugin) : false);
    }
}