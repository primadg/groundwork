{if !$isajax}
<div id="rightSide">
    <div class="wrapper">
            <div class="widget">
                <div class="title"><h3>Permissions</h3></div>
            <div id="ajaxcontent">
            {/if}
                <table cellpadding="0" cellspacing="0" width="100%" class="sTable withCheck" id="permlist">
                    {if $permissions}
                    <thead>
                      <tr>
                          <td>Title</td>
                          <td>Description</td>
                          <td width="130px">actions</td>
                      </tr>
                    </thead>
                    <tbody>
                    {foreach from=$permissions item=v key=k}
                    <tr>
                        <td>
                            <a href="javascript:void(0);" onclick="return acl.permissionform({$v.id});">{$v.name|htmlspecialchars}</a>
                        </td>
                        <td>{$v.description|htmlspecialchars}</td>
                        <td>
                            <a href="javascript:void(0);" onclick="return acl.deletepermission({$v.id});">delete</a>
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                    {else}
                    <tr>
                        <td>There is no permissions present</td>
                    </tr>
                    {/if}
                </table>
            {if !$isajax}
            </div>
                <div class="title"><a id="addpermissionhandler" onclick="return acl.permissionform();" href="javascript:void(0);">Add permission</a></div>
            <div class="dn">
                <div id="dialog"></div>                
            </div>
        </div>
    </div>
</div>
{/if}