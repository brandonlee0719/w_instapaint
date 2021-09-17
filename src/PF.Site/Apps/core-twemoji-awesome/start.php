<?php

new Core\Route('/emojis', function () {
    $doc = new \DOMDocument();
    libxml_use_internal_errors(true);
    $html = '';
    $emoji_cheat_sheet = dirname(__FILE__) . PHPFOX_DS . 'emoji-cheat-sheet.html';
    $handle = fopen($emoji_cheat_sheet, 'r');
    $emoticons = fread($handle, filesize($emoji_cheat_sheet));
    fclose($handle);
    if ($emoticons !== false) {
        $doc->loadHTML($emoticons);
        $xml = $doc->saveXML($doc);

        $xml = @simplexml_load_string($xml);
        $skip = [
            'feelsgood',
            'finnadie',
            'goberserk',
            'godmode',
            'hurtrealbad',
            'rage1',
            'rage2',
            'rage3',
            'rage4',
            'suspect',
            'trollface',
            'bowtie',
            'disappointed_relieved',
            'neckbeard',
            'collision',
            'hankey',
            'shit',
            '+1',
            '-1',
            'facepunch',
            'metal',
            'fu',
            'running',
            'raising_hand',
            'simple_smile'
        ];
        if ($xml instanceof SimpleXMLElement && isset($xml->body) && isset($xml->body->ul)) {
            foreach ($xml->body->ul as $ul) {
                if ($ul instanceof SimpleXMLElement) {
                    if (!isset($ul->attributes()->class)) {
                        continue;
                    }

                    $class = (string)$ul->attributes()->class;
                    if ($class == 'people emojis') {
                        foreach ($ul->li as $li) {
                            $key = (string)$li->div->span;
                            if (in_array($key, $skip)) {
                                continue;
                            }

                            $html .= '<li><i class="twa twa-' . str_replace('_', '-',
                                    $key) . '"></i><span>:' . $key . ':</span></li>';
                        }
                    }
                }
            }
        }
    }

    return [
        'h1_clean' => _p('emoji_cheat_sheet'),
        'content' => ($html ? '<ul class="emoji-list">' . $html . '</ul>' : '<div class="error_message">Unable to load Emojis</div>')
    ];
});

\Phpfox_Module::instance()->addComponentNames('block', [
    'PHPfox_Twemoji_Awesome.share' => Apps\PHPfox_Twemoji_Awesome\Block\Share::class
])->addTemplateDirs([
    'PHPfox_Twemoji_Awesome' => PHPFOX_DIR_SITE_APPS . 'core-twemoji-awesome/views'
]);
