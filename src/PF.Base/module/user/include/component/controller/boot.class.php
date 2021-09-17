<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Boot
 */
class User_Component_Controller_Boot extends Phpfox_Component {
	public function process() {
		header('Content-type: application/javascript');

		Phpfox::getBlock('core.template-notification');
		$sticky_bar = ob_get_contents(); ob_clean();

		if (auth()->isLoggedIn()) {
			$image = Phpfox_Image_Helper::instance()->display([
				'user' => Phpfox::getUserBy(),
				'suffix' => '_50_square'
			]);

			$imageUrl = Phpfox_Image_Helper::instance()->display([
				'user' => Phpfox::getUserBy(),
				'suffix' => '_50_square',
				'return_url' => true
			]);

			$image = htmlspecialchars($image);
			$image = str_replace(['<', '>'], ['&lt;', '&gt;'], $image);

			$sticky_bar .= '<div id="auth-user" data-image-url="' . str_replace("\"", '\'', $imageUrl) . '" data-user-name="' . Phpfox::getUserBy('user_name') . '" data-id="' . Phpfox::getUserId() . '" data-name="' . Phpfox::getUserBy('full_name') . '" data-image="' . $image . '"></div>';

		}

		Phpfox::massCallback('getGlobalNotifications');

		echo 'var user_boot = ' . json_encode(['sticky_bar' => $sticky_bar]) . ';';
		echo 'var user_obj = document.getElementById(\'user_sticky_bar\');';
		echo 'if (user_obj !== null) { document.getElementById(\'user_sticky_bar\').innerHTML = user_boot.sticky_bar;';

		$notifications = Phpfox_Ajax::instance()->returnCalls();
		echo '$Event(function() {';
		if ($notifications) {
			foreach ($notifications as $call) {
				echo $call;
			}
		}

        if (Phpfox::isModule('notification') && Phpfox::isUser() && Phpfox::getParam('notification.notify_on_new_request')) {
            echo 'if (typeof $Core.notification !== \'undefined\') $Core.notification.setTitle();';
        }

		if ($sPlugin = Phpfox_Plugin::get('notification.component_ajax_update_1')) {
			$sPlugin = str_replace('$this->call(', 'print(', $sPlugin);
			eval($sPlugin);
		}
		echo '});';
		echo '}';
		exit;
	}
}
