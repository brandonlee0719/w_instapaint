<?php

namespace Apps\PHPfox_Facebook\Model;

use Phpfox_Image;
use Phpfox_Request;
use Core\Hash as Hash;
use Core\Model as Model;
use Facebook\GraphUser as GraphUser;
use Phpfox;

/**
 * Service class for Facebook Connect App
 *
 * @package Apps\PHPfox_Facebook\Model
 */
class Service extends Model
{

    /**
     * Create a new user or log them in if they exist
     *
     * @param \Facebook\GraphUser $fb
     * @return bool
     * @throws \Exception
     */
    public function create(GraphUser $fb)
    {
        $email = $fb->getEmail();
        $url = null;
        $blank_email = false;
        $bSkipPass = false;
        if (!$email) {
            stream_context_set_default(
                array(
                    'http' => array(
                        'header' => "User-Agent: {$_SERVER['HTTP_USER_AGENT']}\r\n"
                    )
                )
            );
            $headers = array();
            $filename = rtrim(str_replace('app_scoped_user_id/', '', $fb->getLink()), '/');

            if ($filename) {
                $headers = get_headers($filename);
            }


            if (isset($headers[1])) {
                $url = trim(str_replace('Location: https://www.facebook.com/', '', $headers[1]));
                $email = strtolower($url) . '@facebook.com';
                $blank_email = true;
            }
        }

        if (!$email) {
            $email = $fb->getId() . '@fb';
            $blank_email = true;
        }

        $cached = storage()->get('fb_users_' . $fb->getId());
        if ($cached) {
            $user = $this->db->select('*')->from(':user')->where(['user_id' => $cached->value->user_id])->get();
            if (isset($user['email'])) {
                $email = $user['email'];
            } else {
                storage()->del('fb_users_' . $fb->getId());
            }
        } else {
            $user = $this->db->select('*')->from(':user')->where(['email' => $email])->get();
        }

        if (isset($user['user_id'])) {
            //don't reset current user password if account existed
            $_password = $user['password'];
            $bSkipPass = true;
        } else {
            if (!Phpfox::getParam('user.allow_user_registration')) {
                return false;
            }
            if (Phpfox::getParam('user.invite_only_community') && !Phpfox::getService('invite')->isValidInvite($user['email'])) {
                return false;
            }
            $_password = $fb->getId() . uniqid();
            $password = (new Hash())->make($_password);
            if ($fb->getGender() == 'male') {
                $iGender = 1;
            } elseif ($fb->getGender() == 'female') {
                $iGender = 2;
            } else {
                $iGender = 0;
            }

            $id = $this->db->insert(':user', [
                'user_group_id' => NORMAL_USER_ID,
                'email' => $email,
                'password' => $password,
                'gender' => $iGender,
                'full_name' => ($fb->getFirstName() === null ? $fb->getName() : $fb->getFirstName() . ' ' . $fb->getLastName()),
                'user_name' => ($url === null ? 'fb-' . $fb->getId() : str_replace('.', '-', $url)),
                'user_image' => '',
                'joined' => PHPFOX_TIME,
                'last_activity' => PHPFOX_TIME
            ]);

            // Get user's avatar
            $sImage = fox_get_contents("https://graph.facebook.com/" . $fb->getId() . "/picture?type=large");
            $sFileName = md5('user_avatar' . time()) . '.jpg';
            file_put_contents(Phpfox::getParam('core.dir_user') . $sFileName, $sImage);

            // check in case using cdn
            $aImage = (Phpfox::getService('user.process')->uploadImage($id, false,
                Phpfox::getParam('core.dir_user') . $sFileName));
            $oImage = Phpfox_Image::instance();

            //crop thumbnail avatar
            foreach (Phpfox::getParam('user.user_pic_sizes') as $iSize) {
                if (Phpfox::getParam('core.keep_non_square_images')) {
                    $oImage->createThumbnail(Phpfox::getParam('core.dir_user').$sFileName, Phpfox::getParam('core.dir_user') . sprintf($aImage['user_image'], '_' . $iSize), $iSize, $iSize);
                }
                $oImage->createThumbnail(Phpfox::getParam('core.dir_user').$sFileName, Phpfox::getParam('core.dir_user') . sprintf($aImage['user_image'], '_' . $iSize . '_square'), $iSize, $iSize, false);
            }

            register_shutdown_function(function () use ($sFileName) {
                @unlink(Phpfox::getParam('core.dir_user') . $sFileName);
            });
            // update user image
            count($aImage) && $this->db->update(':user', ['user_image' => $aImage['user_image'], 'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')], ['user_id' => $id]);

            if ($blank_email) {
                storage()->set('fb_force_email_' . $id, $fb->getId());
            } else {
                //Set cache to show popup notify
                storage()->set('fb_user_notice_' . $id, ['email' => $email]);
            }

            storage()->set('fb_users_' . $fb->getId(), [
                'user_id' => $id,
                'email' => $email
            ]);

            //Storage account login by Facebook, in the first time this user change password, he/she doesn't need confirm old password.
            storage()->set('fb_new_users_' . $id, [
                'fb_id' => $fb->getId(),
                'email' => $email
            ]);

            $tables = [
                'user_activity',
                'user_field',
                'user_space',
                'user_count'
            ];
            foreach ($tables as $table) {
                $this->db->insert(':' . $table, ['user_id' => $id]);
            }

            $iFriendId = (int)Phpfox::getParam('user.on_signup_new_friend');
            if ($iFriendId > 0 && Phpfox::isModule('friend')) {
                $iCheckFriend = db()->select('COUNT(*)')
                    ->from(Phpfox::getT('friend'))
                    ->where('user_id = ' . (int)$id . ' AND friend_user_id = ' . (int)$iFriendId)
                    ->execute('getSlaveField');

                if (!$iCheckFriend) {
                    db()->insert(Phpfox::getT('friend'), array(
                            'list_id' => 0,
                            'user_id' => $id,
                            'friend_user_id' => $iFriendId,
                            'time_stamp' => PHPFOX_TIME
                        )
                    );

                    db()->insert(Phpfox::getT('friend'), array(
                            'list_id' => 0,
                            'user_id' => $iFriendId,
                            'friend_user_id' => $id,
                            'time_stamp' => PHPFOX_TIME
                        )
                    );

                    if (!Phpfox::getParam('user.approve_users')) {
                        Phpfox::getService('friend.process')->updateFriendCount($id, $iFriendId);
                        Phpfox::getService('friend.process')->updateFriendCount($iFriendId, $id);
                    }
                }
            }
        }
        Phpfox::getService('user.auth')->login($email, $_password, true, 'email', $bSkipPass);
        if (!\Phpfox_Error::isPassed()) {
            throw new \Exception(implode('', \Phpfox_Error::get()));
        }

        return true;
    }
}
