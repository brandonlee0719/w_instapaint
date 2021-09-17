<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Admincp
 * @version 		$Id: cache.html.php 5332 2013-02-11 08:27:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bCacheLocked}
<div class="alert alert-warning" role="alert">
    <h4>{_p var='cache_system_is_locked'}</h4>
    <p>
    {_p var='the_cache_system_is_locked_during_an_operation_that_requires_all_cache_files_to_be_kept_in_place' link=$sUnlockCache}
    </p>
</div>

{else}
{if $iCacheCnt > 0}
{if !defined('PHPFOX_IS_HOSTED_SCRIPT')}
<div class="alert alert-empty" role="alert">
    {_p var='total_objects'}: {$aStats.total}
</div>
<div class="alert alert-empty" role="alert">
    {_p var='cache_size'}: {$aStats.size|filesize}
</div>
{/if}
{else}
<div class="alert alert-empty" role="alert">
	{_p var='no_cache_data_found'}
</div>
{/if}
{/if}