<?php

class  Like_Job_ItemLiked extends \Core\Queue\JobAbstract
{
    public function perform()
    {
        $this->delete();
    }
}