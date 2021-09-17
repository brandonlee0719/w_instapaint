<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Component_Ajax_Ajax
 */
class Ban_Component_Ajax_Ajax extends Phpfox_Ajax
{
    public function ip()
    {
        if ($this->get('active')) {
            Phpfox::getService('ban.process')->add([
                'type_id' => 'ip',
                'find_value' => $this->get('ip')
            ]);
        } else {
            Phpfox::getService('ban.process')->deleteByValue('ip', $this->get('ip'));
        }
    }
}
