<?php

namespace Core\Block;

class Group
{
    public static $blocks = [];

    public static function make(array $group)
    {
        foreach ($group as $controller => $blocks) {
            foreach ($blocks as $location => $block) {
                foreach ($block as $name) {
                    $cache = storage()->get('_apps_block_' . $name);
                    if (!$cache) {
                        $exists = db()->select('*')
                            ->from(':block')
                            ->where("m_connection='$controller' and module_id='_app' and component='$name'")
                            ->execute('getSlaveRow');

                        if (empty($exists)) {
                            db()->insert(':block', [
                                'title'        => $name,
                                'type_id'      => 5,
                                'm_connection' => $controller,
                                'component'    => $name,
                                'module_id'    => '_app',
                                'product_id'   => 'phpfox',
                                'is_active'    => 1,
                                'location'     => $location,
                            ]);
                        }

                        storage()->set('_apps_block_' . $name, 1);
                    }
                }
            }
        }
    }
}