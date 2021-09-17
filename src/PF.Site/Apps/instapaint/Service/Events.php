<?php

namespace Apps\Instapaint\Service;

require __DIR__ . '/../../../../PF.Base/vendor/autoload.php';

class Events extends \Phpfox_Service
{   
    private $client;

    public function __construct() {

        if (strpos($_SERVER['SERVER_NAME'], 'instapaint.com') == true) {
            // production
            $account_id = "5536679";
        } else {
            // developer
            $account_id = "7668798";
        }

        $this->client = new \Drip\Client("79a5b70a85f36c37fb6f44bf28ee2668", $account_id);
        
    }

    public function getCampaigns() {
        $campaigns = $client->get_campaigns('all');
        print_r($campaigns);
    }

    public function createSubscriber($userId) {

        $post_data = array();
        
        // check if crm_id is populated in user table
        $db_user_details = \Phpfox::getService('user')->getUser($userId);
        // use crm_id is populated, else use email from user_data
        if ($db_user_details['crm_id']) {
            // do nothing as user already exists
            $newCrmId = $db_user_details['crm_id'];
        }
        else {
            $post_data = [
                'email' => $db_user_details['email']
            ];
            // add fields to post data
            $post_data['user_id'] = $userId;
            $post_data['time_zone'] = \Phpfox::getService('instapaint.stats')->getTimeZoneName();

            $name_data = explode(" ", $db_user_details['full_name'],2);
            $custom_fields = [
                'first_name'=> $name_data[0],
                'last_name'=> $name_data[1]
            ];
            $post_data['custom_fields'] = $custom_fields;

            $results = $this->client->create_or_update_subscriber($post_data);

            // if email has not changed or user_id was already used on a deleted subscriber will result in error, remove fields and try again
            if (get_class($results) == "Drip\ErrorResponse") {
                unset($post_data['user_id']);
                $results = $this->client->create_or_update_subscriber($post_data);
            }            

            // save crm_id if not in db
            $result_array = $results->get_contents();
            $newCrmId = ($result_array['subscribers'][0]['id']);
            $this->addCRMId($userId, $newCrmId);
        }

        return $newCrmId;

    }   

    public function updateSubscriber($userId, $data) {

        $post_data = [];

        // check if crm_id is populated in user table
        $db_user_details = \Phpfox::getService('user')->getUser($userId);
        // use crm_id is populated, else use email from user_data
        if ($db_user_details['crm_id']) {
            $newCrmId = $db_user_details['crm_id'];
            $post_data['id'] = $db_user_details['crm_id'];
            $post_data['new_email'] = $data['email'];
        }
        else {
            // create a new subscriber
            $post_data['email'] = $db_user_details['email'];
        }

        // add fields to post data
        $post_data['user_id'] = $userId;
        $post_data['time_zone'] = \Phpfox::getService('instapaint.stats')->getTimeZoneName();

        $name_data = explode(" ", $db_user_details['full_name'],2);
        $custom_fields = [
            'first_name'=> $name_data[0],
            'last_name'=> $name_data[1]
        ];
        $post_data['custom_fields'] = $custom_fields;

        $results = $this->client->create_or_update_subscriber($post_data);

        // if email has not changed or user_id was already used on a deleted subscriber will result in error, remove fields and try again
        if (get_class($results) == "Drip\ErrorResponse") {
            unset($post_data['new_email']);
            unset($post_data['user_id']);
            $results = $this->client->create_or_update_subscriber($post_data);
        }

        // save crm_id if not in db
        if (!$db_user_details['crm_id']) {
            $result_array = $results->get_contents();
            $newCrmId = ($result_array['subscribers'][0]['id']);
            $this->addCRMId($userId, $newCrmId);
        }

        return $newCrmId;

    }   
    
    public function addEvent($userId, $eventData) {

        $post_data = [];

        $db_user_details = \Phpfox::getService('user')->getUser($userId);

        if (!$db_user_details['crm_id']) {
            $crmId = $this->createSubscriber($userId);
        } else {
            $crmId = $db_user_details['crm_id'];
        }

        $post_data['id'] = $crmId;

        $post_data['action'] = $eventData['action'];
        
        $results = $this->client->record_event($post_data);

        return $results;

    }    

    public function addOrderEvent($userId, $orderData) {

        $post_data = [];

        $db_user_details = \Phpfox::getService('user')->getUser($userId);
        if (!$db_user_details['crm_id']) {
            $crmId = $this->createSubscriber($userId);
        } else {
            $crmId = $db_user_details['crm_id'];
        }

        $post_data['id'] = $crmId;

        $post_data['action'] = $eventData['action'];
        
        // order method not written in wrapper yet :(
        // $results = $this->client->record_event($post_data);

        // return $results;
    }   

    public function getSubscriber($userId) {

        $db_user_details = \Phpfox::getService('user')->getUser($userId);

        if (!$db_user_details['crm_id']) {
            $crmId = $this->createSubscriber($userId);
        } else {
            $crmId = $db_user_details['crm_id'];
        }      

        $results = $this->client->fetch_subscriber($crmId);

        return $results;

    }    
    
    public function addCRMId($userId, $crmId) {
        return db()->update(':user', ['crm_id' => $crmId], "user_id = $userId");
    }
}