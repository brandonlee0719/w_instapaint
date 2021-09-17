<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_APP_INSTALLING', true);

class Admincp_Component_Controller_Store_Verify extends Phpfox_Component
{
    public function process()
    {
        try{
            $zip = $this->request()->get('zip');
            $type = $this->request()->get('type');
            $appDir = $this->request()->get('apps_dir');
            $productId = $this->request()->get('id');
            $extra_info = $this->request()->get('extra_info');

            $manager = new \Core\Installation\Manager();

            $param = [
                'type'      => $type,
                'filename'  => $zip,
                'productId' => $productId,
                'apps_dir' => $appDir,
                'extra'     => json_decode(base64_decode($extra_info), true),
            ];

            $result = $manager->verifyFilesystem($param);

            $this->template()
                ->setSectionTitle('<a href="' . $this->url()->current() . '">' . _p('Installation') . '</a>');

            if ($type != 'module') {
                if ($this->request()->method() == 'POST') {
                    $form = '';
                    $params = [
                        'productName' => isset($result['productName']) ? $result['productName'] : '',
                        'type' => $type,
                        'productId' => $productId,
                        'apps_dir' => $appDir,
                        'extra_info' => $extra_info,
                        'targetDirectory' => (isset($result['targetDirectory']) ? $result['targetDirectory'] : '')
                    ];
                    foreach ($params as $key => $value) {
                        $form .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
                    }
                    echo '
                            <form id="verify" target="_top" method="post" action="' . $this->url()->makeUrl('admincp.store.ftp') . '">
                                ' . $form . '
                            </form>
                            <script>
                                window.document.getElementById(\'verify\').submit();
                            </script>
                        ';
                    exit;
                }

                $this->url()->send('admincp.store.ftp', [
                    'productName' =>  isset($result['productName']) ? $result['productName'] : '',
                    'type' => $type,
                    'productId' => $productId,
                    'extra_info' => $extra_info,
                    'apps_dir' => $appDir,
                    'targetDirectory' => (isset($result['targetDirectory']) ? $result['targetDirectory'] : '')
                ]);
            }
        }catch(\Exception $ex){
            return Phpfox_Error::display($ex->getMessage());
        }

        $this->template()
            ->assign([
                'storeUrl'      => Core\Home::store(),
                'productId'     => $productId,
                'newFiles'      => $result['new'],
                'removeFiles'   => $result['remove'],
                'overrideFiles' => (isset($result['update']) ? $result['update'] : []),
                'productName'   => isset($result['productName']) ? $result['productName'] : '',
                'type'          => $type,
                'extra_info'   => $extra_info,
                'targetDirectory' => (isset($result['targetDirectory']) ? $result['targetDirectory'] : '')
            ]);

    }
}