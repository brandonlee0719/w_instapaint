<?php

namespace Apps\Core_Newsletter\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox_Error;
use Phpfox;

class AddController extends Admincp_Component_Controller_App_Index
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;
        $aNewsletter = array();

        // Process an exits newsletter
        if ($iJob = $this->request()->getInt('job')) {
            $aNewsletter = Phpfox::getService('newsletter')->get($iJob);
            $this->_checkState($aNewsletter);
            Phpfox::getService('newsletter.process')->processNewsletter($iJob, $aNewsletter['job_id']);
            $this->url()->send('admincp.app', array('id' => 'Core_Newsletter'),
                _p('processing_job_newsletter_id', ['newsletter_id' => $aNewsletter['subject']]));
        }

        // Edit a newsletter
        if ($iEditId = $this->request()->getInt('newsletter_id')) {
            $aNewsletter = Phpfox::getService('newsletter')->get($iEditId);
            $this->_checkState($aNewsletter);
            $bIsEdit = true;
            $aNewsletter['text'] = $aNewsletter['text_html'];
            $aNewsletter['txtPlain'] = $aNewsletter['text_plain'];
            $aAccess = $aNewsletter['news_user_group_id'] ? unserialize($aNewsletter['news_user_group_id']) : null;
            $this->template()->assign(array(
                    'aForms' => $aNewsletter,
                    'aAccess' => $aAccess,
                )
            );
        }
        // When they first submit the newsletter this block adds it to the ongoing or scheduling
        if ($aVals = $this->request()->getArray('val')) {
            // Validate
            if ($this->_validate($aVals)) {
                if ($bIsEdit) {
                    $iId = Phpfox::getService('newsletter.process')->update($aVals, $iEditId);
                } else {
                    $iId = Phpfox::getService('newsletter.process')->add($aVals, Phpfox::getUserId());
                }

                if (!empty($iId)) {
                    if (!empty($aVals['run_now'])) {
                        // If the newsletter set run immediately. We'll add it to job queue now
                        Phpfox::getService('newsletter.process')->processNewsletter($iId);
                        $this->url()->send('admincp.app', array('id' => 'Core_Newsletter'),
                            _p('processing_job_newsletter_id', ['newsletter_id' => $aVals['subject']]));
                    } else {
                        $this->url()->send('admincp.app', array('id' => 'Core_Newsletter'),
                            _p('new_newsletter_successfully_added'));
                    }
                } else {
                    $this->url()->send('admincp.app', array('id' => 'Core_Newsletter'),
                        _p('fail_to_add_new_newsletter'));
                }
            }
        }

        $aAge = array();
        for ($i = (date('Y') - setting('user.date_of_birth_end')); $i <= (date('Y') - setting('user.date_of_birth_start')); $i++) {
            $aAge[$i] = $i;
        }

        $sTitle = !empty($iEditId) ? _p('edit_newsletter') . ': ' . $aNewsletter['subject'] : _p('add_newsletter');
        $this->template()
            ->setEditor()
            ->setTitle($sTitle)
            ->setHeader(array('jscript/add.js' => 'app_core-newsletter'))
            ->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('newsletter'), $this->url()->makeUrl('admincp.app',array('id' => 'Core_Newsletter')))
            ->setBreadCrumb($sTitle, null, true)
            ->setPhrase(array(
                    'min_age_cannot_be_higher_than_max_age',
                    'max_age_cannot_be_lower_than_the_min_age',
                    'notice'
                )
            )
            ->assign(array(
                'aAge' => $aAge,
                'bIsEdit' => $bIsEdit,
                'aUserGroups' => Phpfox::getService('user.group')->get(),
            ));
    }

    private function _checkState(array $aNewsletter)
    {
        if (empty($aNewsletter) || empty($aNewsletter['newsletter_id'])) {
            return Phpfox_Error::display(_p('the_newsletter_that_you_are_looking_for_cannot_be_found'));
        }

        if ($aNewsletter['state'] == CORE_NEWSLETTER_STATUS_COMPLETED) {
            return Phpfox_Error::display(_p('the_newsletter_that_you_are_looking_for_has_been_completed'));
        }
        if ($aNewsletter['state'] == CORE_NEWSLETTER_STATUS_IN_PROGRESS) {
            return Phpfox_Error::display(_p('the_newsletter_that_you_are_looking_for_is_in_progress'));
        }

        return null;
    }

    private function _validate(array $aVals)
    {
        // Check validations using the new method
        $aValidate = array(
            'subject' => array(
                'message' => _p('add_a_subject'),
                'type' => 'string:required'
            ),
            'total' => array(
                'message' => _p('how_many_users_to_contact_per_round'),
                'type' => 'int:required'
            ),
            'text' => array(
                'message' => _p('you_need_to_write_a_message_to_send'),
                'type' => 'string:required'
            )
        );

        Phpfox::getLib('validator')->process($aValidate, $aVals);

        return Phpfox_Error::isPassed();
    }
}
