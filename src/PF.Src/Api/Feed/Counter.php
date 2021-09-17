<?php

namespace Api\Feed;

class Counter extends \Core\Api
{
    public function incr($id, $sFeedTablePrefix = null)
    {
        if ($sFeedTablePrefix) {
            $this->db->updateCounter($sFeedTablePrefix . 'feed', 'total_view', 'feed_id', $id);
        } else {
            $this->db->updateCounter('feed', 'total_view', 'feed_id', $id);
        }
    }

    public function decr($id, $sFeedTablePrefix = null)
    {
        if ($sFeedTablePrefix) {
            $this->db->updateCounter($sFeedTablePrefix . 'feed', 'total_view', 'feed_id', $id, true);
        } else {
            $this->db->updateCounter('feed', 'total_view', 'feed_id', $id, true);
        }
    }
}