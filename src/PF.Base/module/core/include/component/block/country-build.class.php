<?php
defined('PHPFOX') or exit('NO DICE!');

class Core_Component_Block_Country_Build extends Phpfox_Component
{
    public function process()
    {
        $aArgsCountry = $this->getParam('param');

        if (isset($aArgsCountry['value_title']) && strpos($aArgsCountry['value_title'], 'phrase var=') !== false) {
            $aArgsCountry['value_title'] = _p(str_replace(array('phrase var=', '"', "'"), '',
                $aArgsCountry['value_title']));
        }

        if (!isset($aArgsCountry['name'])) {
            $aArgsCountry['name'] = 'country_iso';
        }

        if (!isset($aArgsCountry['style'])) {
            $aArgsCountry['style'] = '';
        }

        if (!isset($aArgsCountry['value_title'])) {
            $aArgsCountry['value_title'] = _p('select');
        }

        //Get all countries
        $aCountries = Phpfox::getService('core.country')->get();

        foreach ($aCountries as $sIso => $sCountry) {
            if (\Core\Lib::phrase()->isPhrase('translate_country_iso_' . strtolower($sIso))) {
                $aCountries[$sIso] = _p('translate_country_iso_' . strtolower($sIso));
            }
        }

        $this->template()->assign([
            'aArgsCountry' => $aArgsCountry,
            'aCountries' => $aCountries,
            'bIsMultiple' => isset($aArgsCountry['multiple']) && !empty($aArgsCountry['multiple']),
            'country_iso' => $this->getParam('country_child_value')
        ]);
    }
}