<div class="poll-app feed {if empty($aPoll.image_path)}no-photo{/if}">
	<div class="poll-media">
        {if !empty($aPoll.image_path)}
            <a class="item-media-bg" href="{permalink id=$aPoll.poll_id module='poll' title=$aPoll.question}"
               style="background-image: url({img server_id=$aPoll.server_id path='poll.url_image' file=$aPoll.image_path suffix='' return_url=true})">
            </a>
        {/if}
		<span class="poll-vote-number {if $aPoll.total_votes > 99}more{/if}"><b>{$aPoll.total_votes|short_number}</b>{if $aPoll.total_votes == 1}{_p('poll_total_vote')}{else}{_p('poll_total_votes')}{/if}</span>
	</div>
	<div class="poll-inner pl-2 pr-2">
		<a href="{permalink id=$aPoll.poll_id module='poll' title=$aPoll.question}" class="poll-title fw-bold">{$aPoll.question|clean}</a>
		<div class="poll-description item_view_content">{$aPoll.description|stripbb|feed_strip|split:55|max_line}</div>
		<span class="poll-vote-number {if $aPoll.total_votes > 99}more{/if} hide"><b>{$aPoll.total_votes|short_number}</b>{if $aPoll.total_votes == 1}{_p('poll_total_vote')}{else}{_p('poll_total_votes')}{/if}</span>
	</div>
</div>