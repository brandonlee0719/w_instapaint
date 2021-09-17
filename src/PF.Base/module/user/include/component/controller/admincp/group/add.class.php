<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Group_Add
 */
class User_Component_Controller_Admincp_Group_Add extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iGroupId = $this->request()->getInt('group_id');
        $sModule = $this->request()->get('module');
        $aLanguages = Phpfox::getService('language')->getAll(true);
        $aInvalid = [];
        $sAppId = null;

        if (Phpfox::isAppAlias($sModule)) {
            $sAppId = Phpfox::getAppId($sModule);
            $App = \Core\Lib::appInit($sAppId);
            Phpfox::getService('user.group.setting.process')->importFromApp($App);
        } elseif (Phpfox::isApps($sModule)) {
            $App = \Core\Lib::appInit($sModule);
            Phpfox::getService('user.group.setting.process')->importFromApp($App);
        }

        $bHideApp = isset($_REQUEST['hide_app']) ? $_REQUEST['hide_app'] : 0;
        if (!$bHideApp) {
            $this->template()->assign(['aSectionAppMenus'=>[]]);
            $this->template()->setActionMenu([
                _p('create_user_group') => [
                    'class' => 'popup',
                    'url'   => $this->url()->makeUrl('admincp.user.group.add')
                ]
            ]);
        }

        $this->template()
            ->assign([
                'aLanguages' => $aLanguages
            ])
            ->setSectionTitle(_p('manage_user_groups'))
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script'
                )
            );

        if ($iGroupId) {

            if ($bIsSetting = $this->request()->get('setting')) {
                Phpfox::getUserParam('user.can_manage_user_group_settings', true);
            } else {
                Phpfox::getUserParam('user.can_edit_user_group', true);
            }

            $aValidation = null;
            $aAssociateValidation = [];
            $oValidator = Phpfox_Validator::instance();
            $oUserGroup  = Phpfox::getService('user.group.setting.process');

            $aGroup = Phpfox::getService('user.group')->getGroup($iGroupId);
            $aSettings = Phpfox::getService('user.group.setting')->get($iGroupId, $sModule);

            $sPluginName = 'validator.admincp_user_settings_'. ($sModule? strtolower($sModule):$sAppId);

            (($sPlugin = Phpfox_Plugin::get($sPluginName)) ? eval($sPlugin) : false);

            // re-map validation settings to associate array and settings.
            if(is_array($aValidation)){
                foreach($aSettings as $i=>$aProduct){
                    foreach($aProduct as $j=> $aModule){
                        foreach($aModule as $aSetting){
                            $sSettingName = $aSetting['name'];
                            $sSettingId  =  $aSetting['setting_id'];
                            if(isset($aValidation[$sSettingName])){
                                $aAssociateValidation[$sSettingId]=  $aValidation[$sSettingName];
                            }
                        }
                    }

                }
            }

            if ($aAssociateValidation) {
                $oValidator->set(['sFormName' => 'js_form', 'aParams' => $aAssociateValidation]);
            }

            Phpfox_Error::reset();

            if ($aVals = $this->request()->getArray('val')) {
                if ($aAssociateValidation && !$oValidator->isValid($aVals['value_actual'])) {
                    $aInvalid = $oValidator->getInvalidate();
                } elseif ($bIsSetting) {
                    if ($oUserGroup->update($iGroupId, $aVals)) {
                        $this->url()->send('current', null, _p('user_group_updated'));
                    }
                } elseif (Phpfox::getService('user.group.process')->update($iGroupId, $aVals)) {
                    $this->url()->send('admincp.user.group', null, _p('user_group_updated'));
                }
            }


            if (Phpfox::getParam('core.allow_cdn')) {
                $aGroup['server_id'] = Phpfox::getLib('cdn')->getServerId();
            } else {
                $aGroup['server_id'] = 0;
            }

            if (!isset($aGroup['user_group_id'])) {
                return Phpfox_Error::display(_p('invalid_user_group'));
            }


            if ($sAppId && $App && $bHideApp) {
                $this->template()->setBreadCrumb(_p('Apps'), $this->url()->makeUrl('admincp.apps'))
                    ->setBreadCrumb(Phpfox::getPhraseT($App->name, 'module'), $this->url()->makeUrl('admincp.app',['id' => $sAppId]));
            }else{
                $this->template()->setBreadCrumb(_p('Members'),'#')
                    ->setBreadCrumb(_p('user_group_settings'),$this->url()->makeUrl('admincp.user.group.add',['group_id'=>1,'setting'=>1,'module'=>'core']));
            }
            $this->template()->assign(array(
                    'bHideApp'      => $bHideApp,
                    'aGroups'       => Phpfox::getService('user.group')->getAll(),
                    'aModules'      => Phpfox::getService('user.group.setting')->getModules($iGroupId),
                    'aForms'        => $aGroup,
                    'sModule'       => $sModule,
                    'iGroupId'      => $iGroupId,
                    'sAppId'        => $sAppId,
                    'sTitleVarName' => $aGroup['title_var_name'],
                    'bEditSettings' => ($this->request()->get('setting') ? true : false),
                )
            )
                ->setSectionTitle(Phpfox_Locale::instance()->convert($aGroup['title']) . ' (ID#' . $aGroup['user_group_id'] . ')')
                ->setTitle( Phpfox_Locale::instance()->convert($aGroup['title']) . ' (ID#' . $aGroup['user_group_id'] . ')')
                ->setBreadCrumb( Phpfox_Locale::instance()->convert($aGroup['title']) . ' (ID#' . $aGroup['user_group_id'] . ')')
                ->setHeader('cache', array(
                        'template.css' => 'style_css'
                    )
                );


            $aCurr = array();
            $aAvoidDup = array();

            // remap error
            if(is_array($aInvalid)){
                foreach($aSettings as $i=>$aProduct){
                    foreach($aProduct as $j=> $aModule){
                        foreach($aModule as $k=>$aSetting){
                            $sSettingId  =  $aSetting['setting_id'];
                            if(isset($aInvalid[$sSettingId])){
                                $aSettings[$i][$j][$k]['error']=  $aInvalid[$sSettingId];
                            }
                        }
                    }

                }
            }

            foreach ($aSettings as $sModule => $aSets) {
                foreach ($aSets as $iKey => $mSets) {
                    foreach ($mSets as $jKey => $aSetting) {
                        if (preg_match('/_sponsor_price/i', $aSetting['name'])) {
                            $aVals = Phpfox::getLib('parse.format')->isSerialized($aSetting['value_actual']) ? unserialize($aSetting['value_actual']) : 'No price set';
                            if (is_array($aVals) && is_numeric(reset($aVals))) // so a module can have 2 settings with currencies (music.song, music.album)
                            {
                                $this->setParam('currency_value_val[value_actual][' . $aSetting['setting_id'] . ']',
                                    $aVals);
                            }
                            $aSettings[$sModule][$iKey][$jKey]['isCurrency'] = 'Y';
                        }

                        if (isset($aAvoidDup[$aSetting['setting_id']])) {
                            unset($aSettings[$sModule][$iKey][$jKey]);
                        }
                        $aAvoidDup[$aSetting['setting_id']] = true;
                    }
                }
            }

            if(!$this->request()->get('hide_app')){
                $this->template()->setActiveMenu('admincp.member.group_settings');
            }

            $this->template()
                ->assign(array(
                    'aSettings'       => $aSettings,
                    'aForms'          => $aGroup,
                    'aCurrency'       => $aCurr,
                    'bShowClearCache' => true,
                )
            );
        } else {
            if ($aVals = $this->request()->getArray('val')) {
                if ($iId = Phpfox::getService('user.group.process')->add($aVals)) {
                    $this->url()->send('admincp.user.group', null, _p('user_group_successfully_added'));
                }
            }

            $this->template()
                ->setBreadCrumb(_p('create_new_user_group'), $this->url()->makeUrl('current'), true)
                ->setTitle(_p('create_new_user_group'))
                ->setActiveMenu('admincp.member.group')
                ->assign(array(
                        'bShowClearCache' => true,
                        'aGroups'         => Phpfox::getService('user.group')->get()
                    )
                );
        }
        return null;
    }
}
