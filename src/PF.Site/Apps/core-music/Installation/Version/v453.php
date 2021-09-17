<?php

namespace Apps\Core_Music\Installation\Version;

use Phpfox;


class v453
{

    private $_aGenresSong;

    public function __construct()
    {

        $this->_aGenresSong = array(
            'Hip hop',
            'Rock',
            'Pop',
            'Alternative',
            'Country',
            'Indie',
            'Rap',
            'R&B',
            'Metal',
            'Punk',
            'Hardcore',
            'House',
            'Electronica',
            'Techno',
            'Reggae',
            'Latin',
            'Jazz',
            'Classic Rock',
            'Blues',
            'Folk',
            'Progressive'
        );
    }

    public function process()
    {

        // add activity song
        if (!db()->isField(':user_activity', 'activity_music_song')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_activity') . "` ADD `activity_music_song` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add statistics total song
        if (!db()->isField(':user_field', 'total_song')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('user_field') . "` ADD `total_song` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        }

        // add change song description
        if (db()->isField(':music_song', 'description')) {
            db()->query("ALTER TABLE  `" . Phpfox::getT('music_song') . "` CHANGE  `description`  `description` TEXT");
        }

        //delete menu
        db()->delete(':menu', "`module_id` = 'music' AND `url_value` = 'music.upload'");

        // add default genre
        $iTotalGenre = db()
            ->select('COUNT(genre_id)')
            ->from(':music_genre')
            ->execute('getField');
        if ($iTotalGenre == 0) {
            sort($this->_aGenresSong);
            $iOrder = 0;
            foreach ($this->_aGenresSong as $sGenre) {
                $iOrder++;
                db()->insert(':music_genre', array(
                        'user_id' => 0,
                        'added' => 0,
                        'used' => 0,
                        'name' => $sGenre,
                        'ordering' => $iOrder,
                        'is_active' => 1
                    )
                );
            }
        }
        else {
            //migrate genre data from old music
            $aSongs = db()->select('song_id,genre_id')
                        ->from(':music_song')
                        ->where('genre_id != 0')
                        ->execute('getRows');
            if(count($aSongs))
            {
                foreach ($aSongs as $aSong)
                {
                    db()->insert(':music_genre_data',['song_id' => $aSong['song_id'],'genre_id' => $aSong['genre_id']]);
                    db()->update(':music_song',['genre_id' => 0],'song_id = '.$aSong['song_id']);
                }
            }
        }
        // Update old settings
        $aSettingsSponsorSong = array(
            'limit' => Phpfox::getParam('music.sponsored_songs_to_show', 10),
            'cache_time' => Phpfox::getParam('core.cache_time_default'),
        );
        db()->update(':block', array('params' => json_encode($aSettingsSponsorSong)), 'component = \'sponsored-song\' AND module_id = \'music\' AND params IS NULL');
        db()->update(':module', ['phrase_var_name' => 'module_apps', 'is_active' => 1], ['module_id' => 'music']);
        //remove setting mass upload
        db()->delete(':setting', 'module_id= "music" AND var_name = "music_enable_mass_uploader"');
        db()->delete(':setting', 'module_id= "music" AND var_name = "sponsored_songs_to_show"');

        // update module_id of sponsor
        db()->update(':ad_sponsor', ['module_id' => 'music_song'], ['module_id' => 'music-song']);
        db()->update(':ad_sponsor', ['module_id' => 'music_album'], ['module_id' => 'music-album']);
    }
}