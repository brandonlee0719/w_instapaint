{if ((count($aSubMenus) || isset($customMenu))) && Phpfox::isUser() && empty($bNotShowActionButton)}
<div class="app-addnew-block">
    <div class="btn-app-addnew">
         <a href="javascript:void(0)" class="btn btn-success btn-gradient" data-toggle="dropdown">
            <span class="ico ico-plus"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-right">
            {if (isset($aCustomMenus))}
            {foreach from=$aCustomMenus key=iKey name=menu item=aMenu}
            <li>
                <a class="{if (isset($aMenu.css_class))} {$aMenu.css_class}{/if}" href="{$aMenu.url}" {$aMenu.extra}>
                    {if !empty($aMenu.icon_class)}
                    <span class="{$aMenu.icon_class}"></span>
                    {else}
                    <span class="ico ico-compose-alt"></span>
                    {/if}
                    {$aMenu.title}
                </a>
            </li>
            {/foreach}
            {/if}

            {foreach from=$aSubMenus key=iKey name=submenu item=aSubMenu}
            <li>
                {if isset($aSubMenu.module) && (isset($aSubMenu.var_name) || isset($aSubMenu.text))}
                <a href="{url link=$aSubMenu.url)}"{if (isset($aSubMenu.css_name))} class="{$aSubMenu.css_name} no_ajax"{else}class=""{/if}>
                {if !empty($aSubMenu.icon_class)}
                <span class="{$aMenu.icon_class}"></span>
                {else}
                <span class="ico ico-compose-alt"></span>
                {/if}
                {if isset($aSubMenu.text)}
                {$aSubMenu.text}
                {else}
                {_p var=$aSubMenu.var_name}
                {/if}
                </a>
                {/if}
            </li>
            {/foreach}
        </ul>
    </div>
</div>
{/if}