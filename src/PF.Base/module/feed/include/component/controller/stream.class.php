<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Feed_Component_Controller_Stream
 */
class Feed_Component_Controller_Stream extends Phpfox_Component {

    /**
     *
     */
	public function process() {
		define('PHPFOX_FEED_STREAM_MODE', true);

		if (($val = $this->request()->get('val'))) {
			Phpfox::isUser(true);

			$val['user_status'] = $val['content'];
			$id = Phpfox::getService('user.process')->updateStatus($val);

			Phpfox::getService('feed')->processAjax($id);

			echo Phpfox_Ajax::instance()->getData();
			exit;
		}

		$aFeedCallback = [];
		if ($module = $this->request()->get('module')) {
			$aFeedCallback = [
				'module' => $this->request()->get('module'),
				'table_prefix' => $this->request()->get('module') . '_',
				'item_id' => $this->request()->get('item_id')
			];
		}

		$aFeed = Phpfox::getService('feed')->callback($aFeedCallback)->get(null, $this->request()->get('id'));

		header('Content-type: application/javascript');
        if ($sponsor = $this->request()->get('sponsor')){
            $url = $this->url()->makeUrl('feed.stream', [
                'id' => $this->request()->get('id'),
                'sponsor' => $sponsor,
            ]);
        } else {
            $url = $this->url()->makeUrl('feed.stream', ['id' => $this->request()->get('id')]);
        }

		if (!isset($aFeed[0])) {
			$js = ';__(' . json_encode([
					'url' => $url,
					'content' => false
				]) . ');';
		} else {
			$this->template()->assign('aGlobalUser', (Phpfox::isUser() ? Phpfox::getUserBy(null) : array()));
			$this->template()->assign('aFeed', $aFeed[0]);
			$this->template()->assign('sponsor', $sponsor);
			if ($aFeedCallback) {
				$this->template()->assign('aFeedCallback', $aFeedCallback);
				$url = $this->url()->makeUrl('feed.stream', ['id' => $this->request()->get('id'), 'module' => $this->request()->get('module'), 'item_id' => $this->request()->get('item_id')]);
			}
			$this->template()->getTemplate('feed.block.entry');

			$js = ';__(' . json_encode([
					'url' => $url,
					'content' => ob_get_clean()
				]) . ');';
		}

		echo $js;
		exit;
	}
}