<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Like_Service_Api extends \Core\Api\ApiServiceBase
{
    /**
     * @description: add like for an item
     * @return array|bool
     */
    public function post()
    {
        $this->isUser();

        //validate data
        $this->requireParams([
            'type_id',
            'item_id'
        ]);

        $sPrefix = '';
        if ($this->request()->get('type_id') == 'app' && Phpfox::hasCallback($this->request()->get('parent_module', ''), 'getFeedDetails'))
        {
            $aCallback = Phpfox::callback($this->request()->get('parent_module', '') . '.getFeedDetails', $this->request()->get('item_id'));
            $sPrefix = isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : $sPrefix;
        }

        if (Phpfox::getService('like.process')->add($this->request()->get('type_id'), $this->request()->get('item_id'), null, $this->request()->get('app_id', null), [], $sPrefix) && Phpfox_Error::isPassed())
        {
            return $this->success([], [_p('{{ item }} successfully added.', ['item' => _p('like')])]);
        }

        return $this->error();
    }

    /**
     * @description: delete like
     * @return array|bool
     */
    public function delete()
    {
        $this->isUser();

        //validate data
        $this->requireParams([
            'type_id',
            'item_id'
        ]);

        $sPrefix = '';
        if ($this->request()->get('type_id') == 'app' && Phpfox::hasCallback($this->request()->get('parent_module', ''), 'getFeedDetails'))
        {
            $aCallback = Phpfox::callback($this->request()->get('parent_module', '') . '.getFeedDetails', $this->request()->get('item_id'));
            $sPrefix = isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : $sPrefix;
        }
        if (Phpfox::getService('like.process')->delete($this->request()->get('type_id'), $this->request()->get('item_id'), 0, false, $sPrefix) && Phpfox_Error::isPassed())
        {
            return $this->success([], [_p('{{ item }} successfully deleted.', ['item' => _p('like')])]);
        }

        return $this->error();
    }

    public function get()
    {
        $this->requireParams([
            'type_id',
            'item_id'
        ]);

        $sPrefix = '';
        if ($this->request()->get('type_id') == 'app' && Phpfox::hasCallback($this->request()->get('parent_module', ''), 'getFeedDetails'))
        {
            $aCallback = Phpfox::callback($this->request()->get('parent_module', '') . '.getFeedDetails', $this->request()->get('item_id'));
            $sPrefix = isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : $sPrefix;
        }
        $aLikes = Phpfox::getService('like')->getLikes($this->request()->get('type_id'), $this->request()->getInt('item_id'), $sPrefix);
        return $this->success($aLikes);
    }
}