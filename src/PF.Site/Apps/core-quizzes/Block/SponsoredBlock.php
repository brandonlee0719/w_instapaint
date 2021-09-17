<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Quizzes\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class SponsoredBlock extends \Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        if (!Phpfox::isModule('ad')) {
            return false;
        }
        if (defined('PHPFOX_IS_GROUP_VIEW') || defined('PHPFOX_IS_PAGES_VIEW') || defined('PHPFOX_IS_USER_PROFILE')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 4);
        $iCacheTime = $this->getParam('cache_time', 5);
        if (!(int)$iLimit) {
            return false;
        }

        $aSponsorQuizzes = Phpfox::getService('quiz')->getSponsored($iLimit, $iCacheTime);

        if (empty($aSponsorQuizzes)) {
            return false;
        }

        foreach ($aSponsorQuizzes as $aQuiz) {
            Phpfox::getService('ad.process')->addSponsorViewsCount($aQuiz['sponsor_id'], 'quiz', 'sponsor');
        }
        $this->template()->assign(array(
                'sHeader' => _p('sponsored'),
                'aSponsorQuizzes' => $aSponsorQuizzes,
            )
        );
        if (Phpfox::getUserParam('quiz.can_sponsor_quiz') || Phpfox::getUserParam('quiz.can_purchase_sponsor_quiz')) {
            $this->template()->assign([
                'aFooter' => array(
                    _p('encourage_sponsor_quiz') => $this->url()->makeUrl('quiz', array('view' => 'my', 'sponsor' => 1))
                )
            ]);
        }
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Sponsored Quizzes Limit'),
                'description' => _p('Define the limit of how many sponsored quizzes can be displayed when viewing the quizzes section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
            [
                'info' => _p('Sponsored Quizzes Cache Time'),
                'description' => _p('Define how long we should keep the cache for the <b>Sponsored Quizzes</b> by minutes. 0 means we do not cache data for this block.'),
                'value' => Phpfox::getParam('core.cache_time_default'),
                'options' => Phpfox::getParam('core.cache_time'),
                'type' => 'select',
                'var_name' => 'cache_time',
            ]
        ];
    }
    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Sponsored Quizzes Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('quiz.component_block_sponsor_clean')) ? eval($sPlugin) : false);
    }
}