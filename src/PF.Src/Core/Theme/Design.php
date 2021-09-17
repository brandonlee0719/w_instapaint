<?php

namespace Core\Theme;

class Design extends \Core\Model
{
    private $_theme;
    private $_service;

    public function __construct(\Core\Theme\Object $Theme)
    {
        parent::__construct();

        $this->_theme = $Theme;
        $this->_service = new Service($this->_theme);
    }

    public function set($designs)
    {
        $less = $this->_service->css()->get(true);
        $css = $this->_service->css()->get();
        foreach ($designs as $key => $value) {
            $less = preg_replace('/\@' . $key . '\:(.*?);/i', '@' . $key . ': ' . $value . ';', $less);
        }
        return $this->_service->css()->set($css, $less, '', $this->_theme->name);
    }

    public function get()
    {

        $replacements = [
            'containerMaxWidthFull' => _p('Container Width'),
            'contentWidth'          => _p('Main Content Width'),
            'lineHeightComputed'    => _p('Line Height Computed'),
            'transition'            => _p('CSS Transitions'),
            'borderRadiusBase'      => _p('Border Radius'),
            'boxShadow'             => _p('Box Shadow'),
            'linkColor'             => _p('Link Color'),
            'linkHoverColor'        => _p('Link Color on Hover'),
            'linkHoverDecoration'   => _p('Link Decoration'),
            'linkFocus'             => _p('Important Link Color'),
            'linkFocusHover'        => _p('Important Link Color on Hover'),
            'brandPrimary'          => _p('Primary Brand Background'),
            'brandPrimaryColor'     => _p('Primary Brand Color'),
            'brandSuccess'          => _p('On Success Background Color'),
            'brandInfo'             => _p('Info Background Color'),
            'brandWarning'          => _p('Warning Background Color'),
            'brandDanger'           => _p('Danger Background Color'),
            'columnLeftWidth'       => _p('Secondary Panel Width'),
            'columnWidth'           => _p('Main Panel Width'),
            'navBg'                 => _p('Navigation Background Color'),
            'navColor'              => _p('Navigation Text Color'),
            'navWidth'              => _p('Navigation Width'),
            'headerBg'              => _p('Header Background Color'),
            'headerColor'           => _p('Header Text Color'),
            'headerHeight'          => _p('Header Height'),
            'headerFontSize'        => _p('Header Font Size'),
            'blockBg'               => _p('Block Background Color'),
            'blockColor'            => _p('Block Text Color'),
            'blockRadius'           => _p('Border Radius'),
            'blockMarginBottom'     => _p('Margin Bottom'),
            'blockBoxShadow'        => _p('Box Shadow'),
            'blockBoxShadowLight'   => _p('(light version) Box Shadow'),
            'blockTitleBg'          => _p('Header Background Color'),
            'blockTitlePadding'     => _p('Header Padding'),
            'blockTitleColor'       => _p('Header Text Color'),
            'blockTitleSize'        => _p('Header Font Size'),
            'blockContentPadding'   => _p('Content Padding'),
            'blockContentSize'      => _p('Content Font Size'),
            'formBg'                => _p('Background Color'),
            'formColor'             => _p('Text Color'),
            'formBorder'            => _p('Border'),
            'hoverCategories'       => _p('Hover on categories?'),
        ];

        $less = $this->_service->css()->get(true);
        $design = [];
        foreach (explode("\n", $less) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (substr($line, 0, 7) == '@import') {
                continue;
            }

            if (substr($line, 0, 1) == '@') {
                // d($line);

                $parts = explode(':', $line);
                preg_match('/^@([a-zA-Z0-9\-\_]+): (.*);(.*)$/is', $line, $matches);
                $matches = array_map('trim', $matches);
                if (!isset($matches[3])) {
                    continue;
                }

                // $var = trim(trim($parts[0]), '@');
                $var = $matches[1];
                // $sub = explode('//', (isset($parts[1]) ? trim($parts[1]) : ''));
                // $value = rtrim(trim($sub[0]), ';');
                $value = $matches[2];
                // $title = (isset($sub[1]) ? trim($sub[1]) : $var);
                if (isset($matches[3])) {
                    $matches[3] = str_replace('//', '', $matches[3]);
                }
                $title = (empty($matches[3]) ? $var : $matches[3]);

                $subType = '';
                if (strpos($title, '|')) {
                    list($title, $subType) = array_map('trim', explode('|', $title));
                }

                if (substr(trim($title), -4) == 'hide') {
                    continue;
                }

                $type = '<input type="text" name="design[' . $var . ']" value="' . htmlspecialchars($value) . '" data-old="' . htmlspecialchars($value) . '">';
                if (substr($value, 0, 1) == '#' || $subType == 'color') {
                    // $type = 'color';
                    $type = '<input type="text" name="design[' . $var . ']" value="' . htmlspecialchars($value) . '" data-old="' . htmlspecialchars($value) . '" class="_colorpicker">';
                    $type .= '<div class="_colorpicker_holder"></div>';
                } else {
                    if (substr($value, 0, 2) == '"\\') {
                        // $type = 'font';
                    }
                }

                if (substr($line, 0, 8) == '@logoUrl') {
                    // $info = str_replace('// Logo', '', trim($parts[1]));
                    $title = 'Logo';
                    $type = '<input type="text" name="design[' . $var . ']" value="' . htmlspecialchars($value) . '" data-old="' . htmlspecialchars($value) . '">';
                    $type .= '<div class="design-uploader">';
                    $type .= '<i class="fa fa-upload"></i>';
                    $type .= '<input type="file" name="image" class="ajax_upload" data-url="' . \Phpfox_Url::instance()
                            ->makeUrl('admincp.theme.manage', ['id' => $this->_theme->theme_id, 'logo' => 'upload']) . '" />';
                    $type .= '</div>';
                }

                foreach ($replacements as $_key => $_value) {
                    $title = preg_replace('/^' . $_key . '$/i', $_value, $title);
                }

                $design[] = [
                    'var'   => $var . ':',
                    'value' => rtrim($value, ';'),
                    'title' => $title,
                    'type'  => $type,
                ];
            } else {
                if (substr($line, 0, 4) == '//==') {
                    array_push($design, str_replace('//==', '', trim($line)));
                }
            }
        }

        return $design;
    }
}