<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Ajax
 * @version 		$Id: ajax.class.php 7092 2014-02-05 21:42:42Z Fern $
 */
class Feed_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function checkNew()
	{
		$iLastFeedUpdate = $this->get('iLastFeedUpdate');
        //Make sure feed loaded
        if ($iLastFeedUpdate > 0 ){
            define('PHPFOX_CHECK_FOR_UPDATE_FEED',true);
            define('PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE', $iLastFeedUpdate);

            Phpfox::getBlock('feed.checknew');

            $this->html('#js_new_feed_update', $this->getContent(false));
            $this->call('$Core.loadInit();');
        }
	}

	public function loadNew()
	{
        $iLastFeedUpdate = $this->get('iLastFeedUpdate');

		define('FEED_LOAD_MORE_NEWS', false);
		define('FEED_LOAD_NEW_NEWS', true);

		define('PHPFOX_CHECK_FOR_UPDATE_FEED',true);
		define('PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE', $iLastFeedUpdate);

		if ($this->get('callback_module_id') == 'pages' && Phpfox::getService('pages')->isTimelinePage($this->get('callback_item_id')))
		{
			define('PAGE_TIME_LINE', true);
		}

		Phpfox::getBlock('feed.display');
		if (!$this->get('forceview') && !$this->get('resettimeline'))
		{
			$this->html('#js_new_feed_comment','');
			$this->insertAfter('#js_new_feed_comment', $this->getContent(false));
		}
		else
		{
			$this->html('#js_new_feed_comment','');
			$this->insertAfter('#js_new_feed_comment', $this->getContent(false));
		}
		$this->call('$Core.loadInit();');
	}
	
	public function loadDropDates()
	{
		Phpfox::getBlock('feed.loaddates');
		
		$sContent = $this->getContent(false);
		$sContent = str_replace(array("\n", "\t"), '', $sContent);
		
		$this->html('.timeline_date_holder_share', $sContent);
	}
	
	public function share()
	{
		$aPost = $this->get('val');		
		if ($aPost['post_type'] == '2')
		{
			if (!isset($aPost['friends']) || (isset($aPost['friends']) && !count($aPost['friends'])))
			{
				Phpfox_Error::set(_p('select_a_friend_to_share_this_with_dot'));
			}
			else
			{
				$iCnt = 0;
				foreach ($aPost['friends'] as $iFriendId)
				{
					$aVals = array(
						'user_status' => $aPost['post_content'],
						'parent_user_id' => $iFriendId,
						'parent_feed_id' => $aPost['parent_feed_id'],
						'parent_module_id' => $aPost['parent_module_id']
					);
					
					if (Phpfox::getService('user.privacy')->hasAccess($iFriendId, 'feed.share_on_wall') && Phpfox::getUserParam('profile.can_post_comment_on_profile'))
					{	
						$iCnt++;
						
						Phpfox::getService('feed.process')->addComment($aVals);
					}				
				}			

				$sMessage = '<div class="message">' . str_replace("'", "\\'", _p('successfully_shared_this_item_on_your_friends_wall')) . '</div>';
				if (!$iCnt)
				{
					$sMessage = '<div class="error_message">' . str_replace("'", "\\'", _p('unable_to_share_this_post_due_to_privacy_settings')) . '</div>';


				}
				$this->call('$(\'#\' + tb_get_active()).find(\'.js_box_content:first\').html(\'' . $sMessage . '\');');
				if ($iCnt)
				{
					$this->call('setTimeout(\'tb_remove();\', 2000);');
                    if (!empty($aVals['parent_module_id']) && !empty($aVals['parent_feed_id'])) {
                        $this->call('$Core.updateShareFeedCount(\'' . $aVals['parent_module_id'] . '\', ' . $aVals['parent_feed_id'] . ', \'+\', ' . $iCnt . ');');
                    }
				}
			}
			$this->call('$("#btnShareFeed").removeAttr("disabled");');
			return null;
		}
		
		$aVals = array(
			'user_status' => $aPost['post_content'],
			'privacy' => '0',
			'privacy_comment' => '0',
			'parent_feed_id' => $aPost['parent_feed_id'],
			'parent_module_id' => $aPost['parent_module_id'],
            'no_check_empty_user_status' => true,
		);		
		
		if (($iId = Phpfox::getService('user.process')->updateStatus($aVals)))
		{
			$this->call('$(\'#\' + tb_get_active()).find(\'.js_box_content:first\').html(\'<div class="message">' . str_replace("'", "\\'", _p('successfully_shared_this_item')) . '</div>\'); setTimeout(\'tb_remove();\', 2000);');

			if (!empty($aVals['parent_module_id']) && !empty($aVals['parent_feed_id'])) {
                $this->call('$Core.updateShareFeedCount(\'' . $aVals['parent_module_id'] . '\', ' . $aVals['parent_feed_id'] . ', \'+\', 1);');
            }
		}
		else {
			$this->call("$('#btnShareFeed').attr('disabled', false); $('#imgShareFeedLoading').hide();");
		}
	}
	
	public function addComment()
	{
		Phpfox::isUser(true);
		
		$aVals = (array) $this->get('val');		
		
		if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status']))
		{
			$this->alert(_p('add_some_text_to_share'));
			$this->call('$Core.activityFeedProcess(false);');
			return false;
		}

		if (isset($aVals['parent_user_id']) && $aVals['parent_user_id'] > 0 && !($aVals['parent_user_id'] == Phpfox::getUserId() || (Phpfox::getUserParam('profile.can_post_comment_on_profile') && Phpfox::getService('user.privacy')->hasAccess('' . $aVals['parent_user_id'] . '', 'feed.share_on_wall')))) {
			$this->alert(_p('You don\'t have permission to post comment on this profile.'));
			$this->call('$Core.activityFeedProcess(false);');
			return false;
		}

		/* Check if user chose an egift */
		if (Phpfox::isModule('egift') && isset($aVals['egift_id']) && !empty($aVals['egift_id']))
		{
			/* is this gift a free one? */
			$aGift = Phpfox::getService('egift')->getEgift($aVals['egift_id']);
			if (!empty($aGift))
			{
				$bIsFree = true;
				foreach ($aGift['price'] as $sCurrency => $fVal)
				{
					if ($fVal > 0)
					{
						$bIsFree = false;
					}
				}	
				/* This is an important change, in v2 birthday_id was the mail_id, in v3
				 * birthday_id is the feed_id
				*/
				$aVals['feed_type'] = 'feed_egift';
				$iId = Phpfox::getService('feed.process')->addComment($aVals);
				// Always make an invoice, so the feed can check on the state
                $aGift['message'] = Phpfox::getLib('parse.input')->prepare($aVals['user_status']);
				$iInvoice = Phpfox::getService('egift.process')->addInvoice($iId, $aVals['parent_user_id'], $aGift);
				
				if (!$bIsFree)
				{
                    Phpfox::getBlock('api.gateway.form', [
                        'gateway_data' => [
                            'item_number'                => 'egift|' . $iInvoice,
                            'currency_code'              => Phpfox::getService('user')->getCurrency(),
                            'amount'                     => $aGift['price'][Phpfox::getService('user')->getCurrency()],
                            'item_name'                  => _p('egift_card_with_message') . ': ' . $aVals['user_status'] . '',
                            'return'                     => Phpfox_Url::instance()->makeUrl('friend.invoice'),
                            'recurring'                  => 0,
                            'recurring_cost'             => '',
                            'alternative_cost'           => 0,
                            'alternative_recurring_cost' => 0
                        ]
                    ]);
                    $this->call('$("#js_activity_feed_form").hide().after("' . $this->getContent(true) . '");');
				}
				else
				{
					//send notification
                    $aInvoice = Phpfox::getService('egift')->getEgiftInvoice((int)$iInvoice);
                    Phpfox::getService('egift.process')->sendNotification($aInvoice);

					// egift is free
					Phpfox::getService('feed')->processAjax($iId);

				}
			}

		}
		else
		{			
			if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->addComment($aVals)))
			{
			    if (isset($aVals['feed_id'])) {
                    $this->call("$('#js_item_feed_$aVals[feed_id]').find('div.activity_feed_content_status').text('$aVals[user_status]');");
                    $this->call('tb_remove();');
                    $this->call('setTimeout(function(){$Core.resetActivityFeedForm();$Core.loadInit();}, 500);');
                } else {
                    Phpfox::getService('feed')->processAjax($iId);
                }
			}
			else 
			{
				$this->call('$Core.activityFeedProcess(false);');
			}	
		}
		
	}
	
	public function viewMore()
	{
    	define('FEED_LOAD_MORE_NEWS', true);

    	$sCallbackModuleId = $this->get('callback_module_id', false);
    	if ($sCallbackModuleId && in_array($sCallbackModuleId, ['pages', 'groups'])) {
            define('PHPFOX_IS_PAGES_VIEW', true);
            define('PHPFOX_PAGES_ITEM_TYPE', $sCallbackModuleId);
        }

		if ($sCallbackModuleId == 'pages' && Phpfox::getService('pages')->isTimelinePage($this->get('callback_item_id'))) {
			define('PAGE_TIME_LINE', true);
		}
		
		Phpfox::getBlock('feed.display');
		
		$this->remove('#feed_view_more');
		if (!$this->get('forceview') && !$this->get('resettimeline'))
		{
            $this->call('var feed_current_position = $(window).scrollTop();');
			$this->append('#js_feed_content', $this->getContent(false));
            $this->call('$(window).scrollTop(feed_current_position);');
		}
		else
		{
			$this->call('$.scrollTo(\'.timeline_left\', 800);');
			$this->html('#js_feed_content', $this->getContent(false));
		}
		$this->call('$iReloadIteration = 0;$Core.loadInit();');
	}
	
	public function rate()
	{		
		Phpfox::isUser(true);
		
		list($sRating, $iLastVote) = Phpfox::getService('feed.process')->rate($this->get('id'), $this->get('type'));
		Phpfox::getBlock('feed.rating', array(
				'sRating' => (int) $sRating,
				'iFeedId' => $this->get('id'),
				'bHasRating' => true,
				'iLastVote' => $iLastVote
			)
		);
		$this->html('#js_feed_rating' . $this->get('id'), $this->getContent(false));		
	}

	public function delete()
	{
		if (Phpfox::getService('feed.process')->deleteFeed($this->get('id'), $this->get('module'), $this->get('item')))
		{
			$this->slideUp('#js_item_feed_' . $this->get('id'));
			$this->alert(_p('feed_successfully_deleted'), _p('feed_deletion'), 300, 150, true);
		}
		else
		{
			$this->alert(_p('unable_to_delete_this_entry'));
		}
	}
    
    /* Loads Pages and results from Google Places Autocomplete given a latitude and longitude
     * This function populates $Core.Feed.aPlaces with new items by passing parameters in jSon format */
     
    public function loadEstablishments()
    {
		$aPages = array();
		if (Phpfox::isModule('pages'))
		{
			$aPages = Phpfox::getService('pages')->getPagesByLocation( $this->get('latitude'), $this->get('longitude') );
		}
		
		if (count($aPages))
		{
			foreach ($aPages as $iKey => $aPage)
			{
				$aPages[$iKey]['geometry'] = array('latitude' => $aPage['location_latitude'], 'longitude' => $aPage['location_longitude']);
				$aPages[$iKey]['name'] = $aPage['title'];
				unset($aPages[$iKey]['location_latitude']);
				unset($aPages[$iKey]['location_longitude']);	
			}
		}
		
		if (!empty($aPages))
		{
			$jPages = json_encode($aPages);
			$this->call('$Core.Feed.storePlaces(\'' . $jPages .'\');');
		}		
	}

    public function editUserStatus(){
        $iFeedId = $this->get('id');
        $sModule = $this->get('module');
        Phpfox::getBlock('feed.edit-user-status', ['id' => $iFeedId, 'module' => $sModule]);
    }

    public function updatePost()
    {
        $aVals = (array)$this->get('val');

        // todo temporary not synchornize feed_comment with user_status, we will improve this in next version.
        if (isset($aVals['feed_id']) &&
            Phpfox::getService('feed.process')->updateFeedComment($aVals['feed_id'], $aVals['user_status'], false, isset($aVals['tagged_friends']) ? explode(',', $aVals['tagged_friends']) : [])
        ) {
            $this->reload();
        }
    }
}