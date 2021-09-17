<?php
return function (Phpfox_Installer $Installer) {
    /*
     * list convert category
     */
    $aConvertCategories = [
        'blog_category'        => [
            'id'   => 'category_id',
            'name' => 'name'
        ],
        'event_category'       => [
            'id'   => 'category_id',
            'name' => 'name'
        ],
        'photo_category'       => [
            'id'   => 'category_id',
            'name' => 'name'
        ],
        'marketplace_category' => [
            'id'   => 'category_id',
            'name' => 'name'
        ],
        'forum'                => [
            'id'   => 'forum_id',
            'name' => 'name'
        ],
        'music_genre'          => [
            'id'   => 'genre_id',
            'name' => 'name'
        ],
        'pages_type'           => [
            'id'   => 'type_id',
            'name' => 'name'
        ],
        'pages_category'       => [
            'id'   => 'category_id',
            'name' => 'name'
        ],
    ];
    
    foreach ($aConvertCategories as $sTableName => $aInfo) {
        $aCategories = $Installer->db->select($aInfo['id'] . " AS id," . $aInfo['name'] . " AS name")
                                     ->from(Phpfox::getT($sTableName))
                                     ->where('1')
                                     ->execute('getRows');
        foreach ($aCategories as $aCategory) {
            if (substr($aCategory['name'], 0, 7) == '{phrase' && substr($aCategory['name'], -1) == '}') {
                //Clean all double spaces
                $aCategory['name'] = preg_replace('/\s+/', ' ', $aCategory['name']);
                $aCategory['name'] = str_replace([
                    "{phrase var='",
                    "{phrase var=&#039;",
                    "{phrase var=\"",
                    "{phrase var=&quot;quot;",
                    "'}",
                    "&#039;}",
                    "\"}",
                    "&quot;quot;}"
                ], "", $aCategory['name']);
                $Installer->db->update(Phpfox::getT($sTableName), [
                    $aInfo['name'] => $aCategory['name']
                ], $aInfo['id'] . "=" . (int)$aCategory['id']);
            }
        }
    }
};