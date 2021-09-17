<div class="item-container with-user by-listing at-left border-top" id="user_lists">
{foreach from=$aActiveAdmins name=admins item=aActiveAdmin}
    <article data-url="">
        <div class="item-outer">
            <div class="item-media">
                {img user=$aActiveAdmin no_link=true suffix='_50_square' max_width=50 max_height=50}
            </div>
            <div class="item-inner">
                {$aActiveAdmin|user}<br/>
                <small>{$aActiveAdmin.ip_address}</small>
            </div>
        </div>
    </article>
{/foreach}
</div>