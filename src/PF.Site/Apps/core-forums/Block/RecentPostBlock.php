<?php
namespace Apps\Core_Forums\Block;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class RecentPostBlock extends Phpfox_Component
{
    public function process()
    {
        if ($this->request()->segment(2) == 'search') {
            return false;
        }

        if ($this->getParam('iActiveForumId') &&
            !Phpfox::getService('forum')->hasAccess($this->getParam('iActiveForumId'), 'can_view_thread_content')
        ) {
            return false;
        }

        $title = _p('recent_posts');
        $iLimit = $this->getParam('limit', 4);
        if (!(int)$iLimit) {
            return false;
        }
        if (redis()->enabled()) {
            $aPosts = [];
            $rows = redis()->lrange('forum/recent/reply/' . $this->request()->segment(2), 0, 20);
            foreach ($rows as $post_id) {
                $post = redis()->get('forum/reply/' . $post_id);
                $thread = redis()->get('forum/thread/' . $post->thread_id);
                $post->post_id = $post_id;
                $post->thread_title = $thread->title;

                $aPosts[] = array_merge(redis()->user($post->user_id), (array)$post);
            }

        } else {
            $aPosts = Phpfox::getService('forum.post')->getRecentForForum($this->request()->segment(2), $iLimit);
        }

        $type = 'posts';

        if (empty($aPosts)) {
            return false;
        }
        $this->template()->assign([
            'sHeader' => $title,
            'threads' => $aPosts,
            'type' => $type
        ]);

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Recent Posts Limit'),
                'description' => _p('Define the limit of how many posts can be displayed when viewing the forum section. Set 0 will hide this block.'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Recent Posts Limit" must be greater than or equal to 0')
            ],
        ];
    }

}