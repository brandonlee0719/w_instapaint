$Behavior.manageFriends = function(){
	$('.friend_list_change_order').click(function(){
		if ($('.js_friend_edit_order_submit').hasClass('is_active')){
			$('.js_friend_edit_order').hide();
			$('.js_friend_edit_order_submit').removeClass('is_active');
			$('.friend_action_holder').show();
			$('.js_friend_sort_handler').hide();
		}
		else{
			$('.js_friend_edit_order').show();
			$('.js_friend_edit_order_submit').addClass('is_active');
			$('.friend_action_holder').hide();
			$('.js_friend_sort_handler').show();
			
			$('#js_friend_sort_holder').sortable({
				items: '.friend_row_holder',
				opacity: 0.4,
				cursor: 'move',
				helper: 'clone',
				handle: '.js_friend_sort_handler',
			});			
		}
		return false;
	});

    $('.friend_action_delete,.friend_action_remove').unbind().click(function(){
		var id = $(this).attr('rel');
		$Core.jsConfirm({}, function() {
			$.ajaxCall('friend.delete', 'test=1&id=' + id);
		}, function(){});
		return false;
	});
	
	$('#js_friend_list_order_form').submit(function(){
		$Core.processForm(this);
		$(this).ajaxCall('friend.updateListOrder');		
		return false;
	});
	
	$('.friend_list_display_profile').click(function(){
		$.ajaxCall('friend.setProfileList', 'list_id=' + $(this).attr('rel') + '&type=add', 'GET');
		return false;
	});
	
	$('.friend_list_remove_profile').click(function(){
		$.ajaxCall('friend.setProfileList', 'list_id=' + $(this).attr('rel') + '&type=remove', 'GET');
		return false;
	});	

	
	$('.js_core_menu_friend_add_list').click(function(){

		$Core.box('friend.addNewList', 400);
		
		return false;
	});

	$('.js_friend_list_edit_name').click(function(){
		var id = $(this).attr('rel');
		$Core.box('friend.editName', 400, 'id='+id);

		return false;
	});
	
	$('[data-dropdown-type="friend_action"] li.add_to_list:not(.divider) a').click(function(){
		var sRel = $(this).attr('rel');
		var sType = '';
		var aParts = explode('|', sRel);
		
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			sType = 'remove';
			if ($(this).hasClass('selected')) {
				$(this).closest('.friend_row_holder').remove();
			}
		}
		else {
			$(this).addClass('active');
			sType = 'add';
		}
		
		$.ajaxCall('friend.manageList', 'list_id=' + aParts[0] + '&friend_id=' + aParts[1] + '&type=' + sType, 'GET');
		
		return false;
	});
}