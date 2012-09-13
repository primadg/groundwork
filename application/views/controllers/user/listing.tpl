<div id="content">
        {if empty($aData.users)}
        <div class="text">No users found</div>
        {else}
        <table class="admtbl">
                <tr class="head">
                        <th width="30px">
                                {if $aData.order=='user_id'}
                                        {if $aData.dirrection=='ASC'}
                                                {assign var=image value='s_desc.png'}
                                                {assign var=order value='DESC'}
                                        {else}
                                                {assign var=image value='s_asc.png'}
                                                {assign var=order value='ASC'}
                                        {/if}
                                {else}
                                        {assign var=order value='ASC'}
                                        {assign var=image value=''}
                                {/if}
                                <a href="javascript:void(0);" onclick="util.reorder('content', 'user_id', '{$order}', 'user/listing');">id{if $image}<img src="{$site_url}images/{$image}" />{/if}</a>
                        </th>
                        <th width="80px">
                                {if $aData.order=='login'}
                                        {if $aData.dirrection=='ASC'}
                                                {assign var=image value='s_desc.png'}
                                                {assign var=order value='DESC'}
                                        {else}
                                                {assign var=image value='s_asc.png'}
                                                {assign var=order value='ASC'}
                                        {/if}
                                {else}
                                        {assign var=order value='ASC'}
                                        {assign var=image value=''}
                                {/if} 
                                <a href="javascript:void(0);" onclick="util.reorder('content', 'login', '{$order}', 'user/listing');">Login{if $image}<img src="{$site_url}images/{$image}" />{/if}</a>
                        </th>
                        <th width="130px">
                                {if $aData.order=='email'}
                                        {if $aData.dirrection=='ASC'}
                                                {assign var=image value='s_desc.png'}
                                                {assign var=order value='DESC'}
                                        {else}
                                                {assign var=image value='s_asc.png'}
                                                {assign var=order value='ASC'}
                                        {/if}
                                {else}
                                        {assign var=order value='ASC'}
                                        {assign var=image value=''}
                                {/if}
                                <a href="javascript:void(0);" onclick="util.reorder('content', 'email', '{$order}', 'user/listing');">Email{if $image}<img src="{$site_url}images/{$image}" />{/if}</a>
                        </th>
                        <th width="80px">
                                {if $aData.order=='first_name'}
                                        {if $aData.dirrection=='ASC'}
                                                {assign var=image value='s_desc.png'}
                                                {assign var=order value='DESC'}
                                        {else}
                                                {assign var=image value='s_asc.png'}
                                                {assign var=order value='ASC'}
                                        {/if}
                                {else}
                                        {assign var=order value='ASC'}
                                        {assign var=image value=''}
                                {/if}
                                <a href="javascript:void(0);" onclick="util.reorder('content', 'first_name', '{$order}', 'user/listing');">Name{if $image}<img src="{$site_url}images/{$image}" />{/if}</a>
                        </th>
                        <th width="120px">
                                {if $aData.order=='created'}
                                        {if $aData.dirrection=='ASC'}
                                                {assign var=image value='s_desc.png'}
                                                {assign var=order value='DESC'}
                                        {else}
                                                {assign var=image value='s_asc.png'}
                                                {assign var=order value='ASC'}
                                        {/if}
                                {else}
                                        {assign var=order value='ASC'}
                                        {assign var=image value=''}
                                {/if}
                                <a href="javascript:void(0);" onclick="util.reorder('content', 'created', '{$order}', 'user/listing');">Member since{if $image}<img src="{$site_url}images/{$image}" />{/if}</a>
                        </th>
                        <th width="30px">is admin?</th>
                        <th width="60px">Actions</th>
                </tr>
        {foreach from=$aData.users item=user key=key}
                <tr class="row{$key%2}">
                        <td width="30px">{$user.user_id}</td>
                        <td width="80px">{$user.login|htmlspecialchars|mb_strimwidth:0:15:'...'}</td>
                        <td width="130px">{$user.email|htmlspecialchars|mb_strimwidth:0:22:'...'}</td>
                        <td width="80px">{$user.first_name|htmlspecialchars|mb_strimwidth:0:22:'...'} {$user.last_name|mb_strimwidth:0:22:'...'}</td>
                        <td width="120px">{$user.created|date_format:'%m/%d/%Y'}</td>
                        <td width="30px">{if $user.admin}<span class="yes">yes</span>{else}<span class="no">no</span>{/if}</td>
                        <td width="60px">                                
                                <a onclick="return confirm('Are you sure to delete this user?');" href="{$site_url}user/delete/{$user.user_id}" class="but delete"></a>
                                <a href="{$site_url}user/edit/{$user.user_id}" class="but edit"></a>
                        </td>
                </tr>
        {/foreach}
                <tr>
                        <td colspan="7"><a title="Add user" class="but add" href="{$site_url}user/add"></a></td>
                </tr>
                {if $aData.pagecount>1}
                <tr>
                        <td colspan="7" align="center">
                                {section name=pages start=1 loop=$aData.pagecount+1}
                                        {if $smarty.section.pages.index==$aData.page}
                                                <strong>{$smarty.section.pages.index}</strong>
                                        {else}
                                                <a onclick="return util.showpage('content', 'user/listing', {$smarty.section.pages.index});" href="{$site_url}user/listing/{$smarty.section.pages.index}/{$aData.order}/{$aData.dirrection}"><strong>{$smarty.section.pages.index}</strong></a>
                                        {/if}
                                {/section}                        
                        </td>
                </tr>
                {/if}
        </table>
        {/if}
</div>