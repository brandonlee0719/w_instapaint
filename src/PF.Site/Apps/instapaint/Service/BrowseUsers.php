<?php

namespace Apps\Instapaint\Service;

class BrowseUsers extends \Phpfox_Service
{
    public function __construct()
    {

    }

    /**
     *
     */
    public function query()
    {

    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {

    }

    public function processRows(&$aRows)
    {
        // Add number to each row to display in template:
        foreach ($aRows as $iKey => $aRow) {
            // Cryptic code to get the correct row number through ajax requests:
            $aRows[$iKey]['number'] = ($this->search()->getPage() ? $this->search()->getPage() - 1 : 0) * $this->search()->getDisplay() + ($iKey + 1);
        }
    }
}
