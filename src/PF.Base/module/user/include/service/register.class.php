<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Service_Register
 */
class User_Service_Register extends Phpfox_Service
{
    public function getValidation($sStep = null, $bIsApi = false, $iUserGroupId = null, $bCustomField = false)
    {
        $aValidation = array();

        if ($sStep == 1 || $sStep === null) {
            if (Phpfox::getParam('user.disable_username_on_sign_up') != 'username') {
                $aValidation['full_name'] = _p('provide_your_full_name');
            }
            $aValidation['email'] = array(
                'def' => 'email',
                'title' => _p('provide_a_valid_email_address')
            );
            $aValidation['password'] = array(
                'def' => 'password',
                'title' => _p('provide_a_valid_password')
            );

            if (Phpfox::getParam('user.signup_repeat_password')) {
                $aValidation['repassword'] = _p('confirm_your_password');
            }

            if (Phpfox::getParam('user.new_user_terms_confirmation') && !$bIsApi) {
                $aValidation['agree'] = array(
                    'def' => 'checkbox',
                    'title' => _p('check_our_agreement_in_order_to_join_our_site')
                );
            }

            if (!Phpfox::getParam('user.profile_use_id') && (Phpfox::getParam('user.disable_username_on_sign_up') != 'full_name')) {
                $aValidation['user_name'] = array(
                    'def' => 'username',
                    'title' => _p('provide_a_valid_user_name', array(
                        'min' => Phpfox::getParam('user.min_length_for_username'),
                        'max' => Phpfox::getParam('user.max_length_for_username')
                    ))
                );
            }
        }

        if ($sStep == 2 || $sStep === null) {
            if (Phpfox::getParam('core.registration_enable_dob')) {
                if ($bIsApi) {
                    $aValidation['month'] = _p('Provide month of birth.');
                    $aValidation['day'] = _p('Provide day of birth.');
                    $aValidation['year'] = _p('Provide year of birth.');
                } else {
                    $aValidation['month'] = _p('select_month_of_birth');
                    $aValidation['day'] = _p('select_day_of_birth');
                    $aValidation['year'] = _p('select_year_of_birth');
                }
            }
            if (Phpfox::getParam('core.registration_enable_location')) {
                if ($bIsApi) {
                    $aValidation['country_iso'] = _p('Provide current location.');
                } else {
                    $aValidation['country_iso'] = _p('select_current_location');
                }

            }
            if (Phpfox::getParam('core.registration_enable_gender')) {
                if ($bIsApi) {
                    $aValidation['gender'] = _p('Provide your gender.');
                } else {
                    $aValidation['gender'] = _p('select_your_gender');
                }
            }

            //Add validation for custom field
            if ($bCustomField) {
                $aFields = Phpfox::getService('custom')->getForEdit(array('user_main', 'user_panel', 'profile_panel'),
                    null,
                    $iUserGroupId, true);
                foreach ($aFields as $iKey => $aField) {
                    if (!$aField['is_required']) {
                        continue;
                    }
                    $aValidation['custom[' . $aField['field_id'] . ']'] = _p('Please provide ') . _p($aField['phrase_var_name']);
                }
            }
        }

        if (Phpfox::isModule('captcha') && Phpfox::getParam('user.captcha_on_signup') && $sStep === null && !$bIsApi) {
            $aValidation['image_verification'] = _p('complete_captcha_challenge');
        }
        return $aValidation;
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
        if ($sPlugin = Phpfox_Plugin::get('user.service_register__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
