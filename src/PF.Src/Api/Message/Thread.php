<?php

namespace Api\Message;

class Thread extends \Core\Api
{
    public function get($id)
    {
        list($thread, $messages) = \Phpfox::getService('mail')->getThreadedMail($id);

        $objects = [];
        foreach ($messages as $message) {
            $objects[] = new Thread\Object($message);
        }

        $object = [
            'thread'   => new Object($thread),
            'messages' => $objects,
        ];

        return $object;
    }

    public function post($id)
    {
        $this->auth();
        $this->requires([
            'message',
        ]);

        \Phpfox::getService('mail.process')->add([
            'thread_id' => $id,
            'message'   => $this->request->get('message'),
        ]);

        $thread = new Thread\Object(\Phpfox::getService('mail')->getThreadedMail($id, 0, true));

        return $thread;
    }
}