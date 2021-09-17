<?php
/**
 * @author  OvalSky
 * @license phpfox.com
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Admincp_Component_Block_Stat
 */
class Admincp_Component_Block_Stat extends Phpfox_Component
{
    public function process()
    {
        $aItems = [];

        $stats = Phpfox::getService('core.stat')->getSiteStatsForAdmin(0, time());
        $counter = 0;


        usort($stats, function ($a, $b) {
            if ($a['phrase'] == 'user.users') {
                return -2   ;
            }
            return $a['total'] < $b['total'] ? -1 : 1;
        });

        $stats = array_filter($stats, function ($a) use (&$counter) { // limit 4 items in selected array
            $a['phrase'] = isset($a['phrase']) ? $a['phrase'] : '';
            return in_array($a['phrase'], ['user.users', 'photo.photos', 'videos', 'event.events', 'blog.blogs', 'comment.comment_on_items'])
                and $counter++ < 4;
        });


        foreach ($stats as $stat) {
            $key = $stat['phrase'];
            $aItems[$key] = [
                'phrase' => _p($key),
                'value'  => $stat['total'],
                'photo'  => 'stat/' . $key . '.png',
            ];
        }

        $this->template()->assign([
            'aItems' => $aItems,
        ]);
        return 'block';
    }
}
