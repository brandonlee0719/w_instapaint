<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div id="profile_activity_points_block" class="activity-point-container">
    <div class="item-total">
        <div class="item-total-info"><i class="ico ico-box-o"></i> <div class="item-number"><span>{$iTotalItems}</span> {_p var='total_items'}</div></div>
        <div class="item-total-info"><i class="ico ico-star-circle-o"></i> <div class="item-number"><span>{$iTotalPoints}</span> {_p var='activity_points'}</div></div>
        {if Phpfox::getParam('user.can_purchase_activity_points')}
        <div class="item-purchase">
            <a role="button" onclick="$Core.box('user.purchasePoints', 500); return false;" class="btn btn-primary">{_p var='purchase_points'}</a>
        </div>
        {/if}
    </div>
    <div class="item-detail-container">
        {foreach from=$aActivites key=sPhrase item=sValue}
        <div class="item-info">
            <div class="item-info-outer">
                <div class="item-title">
                    <?php if (isset($this->_aVars['aIcons'][$this->_aVars['sPhrase']])): ?>
                        <i class="<?php echo $this->_aVars['aIcons'][$this->_aVars['sPhrase']]; ?>"></i>
                    <?php endif; ?>
                    {$sPhrase}:
                </div>
                <div class="item-count">
                    <span class="item-number">{if $sValue < 2}{_p var='point_item' point=$sValue}{else}{_p var='point_items' point=$sValue}{/if}</span>
                </div>
            </div>
        </div>
        {/foreach}
    </div>
</div>
