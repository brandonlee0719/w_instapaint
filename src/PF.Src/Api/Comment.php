<?php

namespace Api;

class Comment extends \Core\Api
{
    public function post()
    {
        $this->auth();
        $this->requires([
            'comment',
            'feed_id',
        ]);

        return \Phpfox::getService('comment.process')->add([
            'parent_id'       => 0,
            'type'            => 'app',
            'item_id'         => $this->request->get('feed_id'),
            'comment_user_id' => 0,
            'text'            => $this->request->get('comment'),
        ]);
    }

    public function get($feedId = null)
    {
        if (!$feedId) {
            $feedId = $this->request->get('feed_id');
            $object = [];
            list($total, $comments) = \Phpfox::getService('comment')->get('cmt.*', [
                'cmt.type_id = \'app\' AND cmt.item_id = \'' . (int)$feedId . '\'',
            ]);

            foreach ($comments as $comment) {
                $object[] = new Comment\Object($comment);
            }
        } else {
            $comment = \Phpfox::getService('comment')->getComment($feedId);
            $object = new Comment\Object($comment);
        }

        return $object;
    }

    public function delete($commentId)
    {
        return \Phpfox::getService('comment.process')->delete($commentId);
    }
}