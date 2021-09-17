<?php

namespace Core\Home;

class Run
{
    private $_out;

    public function __construct($action, $response = null)
    {
        call_user_func([$this, $action], $response);
    }

    public function version()
    {
        $this->_out = [
            'version' => \Phpfox::getVersion(),
        ];
    }

    public function install($response)
    {
        $Request = new \Core\Request();
        $Url = new \Core\Url();

        $zip = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . uniqid() . '.zip';

        $ch = curl_init($response->download);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $content = curl_exec($ch);

        if ($error = curl_errno($ch)) {
            curl_error($ch);
        }
        curl_close($ch);
        file_put_contents($zip, $content);

        switch ($Request->get('type')) {
            case 'isAppInstalled':
                echo "OK";
                break;
            case 'language':
                $file = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . uniqid() . PHPFOX_DS;
                mkdir($file);

                $Zip = new \ZipArchive();
                $Zip->open($zip);
                $Zip->extractTo($file);
                $Zip->close();

                // $xml = \Phpfox::getLib('xml.parser')->parse($file . 'phpfox-language-import.xml');
                $name = false;
                $fullPath = $file . 'upload/include/';
                foreach (scandir($fullPath . 'xml/language/') as $dir) {
                    if (file_exists($fullPath . 'xml/language/' . $dir . '/phpfox-language-import.xml')) {
                        $name = $dir;
                    }
                }

                if (!$name) {
                    throw new \Exception(_p('Not a valid language package to install.'));
                }

                \Phpfox::getService('language.process')->installPackFromFolder($name, $fullPath . 'xml/language/' . $name . '/');

                $Url->send('admincp/language/import', ['dir' => base64_encode($fullPath . 'xml/language/' . $name . '/')]);
                break;
            default:
                $Theme = new \Core\Theme();
                $Theme->import($zip, [
                    'image'   => $response->image,
                    'id'      => $response->internal_id,
                    'version' => $response->internal_version,
                ]);

                $Url->send('admincp/theme', null, _p('Theme successfully installed!'));
                break;
        }

        exit;
    }

    public function __toString()
    {
        $out = json_encode($this->_out);

        return $out;
    }
}