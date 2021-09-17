<?php

namespace Apps\Instapaint\Service;

class BrowseDiscounts extends \Phpfox_Service
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
        db()->select('COUNT(pd.package_id) AS count_packages, ')
            ->leftJoin('phpfox_instapaint_package_discount', 'pd', 'd.discount_id = pd.discount_id')
            ->group('d.discount_id');
    }

    public function processRows(&$aRows)
    {
        // Add number to each row to display in template:
        foreach ($aRows as $iKey => $aRow) {
            // Cryptic code to get the correct row number through ajax requests:
            $aRows[$iKey]['number'] = ($this->search()->getPage() ? $this->search()->getPage() - 1 : 0) * $this->search()->getDisplay() + ($iKey + 1);

            // Create details string:
            $aRows[$iKey]['details'] = $aRows[$iKey]['discount_percentage'] . '% off for ';

            switch ($aRows[$iKey]['count_packages']) {
                case 0:
                    $aRows[$iKey]['details'] .= ' all packages';
                    break;
                case 1:
                    $aRows[$iKey]['details'] .= ' 1 package';
                    break;
                default:
                    $aRows[$iKey]['details'] .= $aRows[$iKey]['count_packages'] . ' packages';
                    break;
            }
        }
    }
}
