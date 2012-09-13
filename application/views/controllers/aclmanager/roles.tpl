{if !$isajax}
<div id="rightSide">
    <div class="wrapper">
            <div class="widget">
                <div class="title"><h3>Roles list</h3></div>
            <div id="ajaxcontent">
            {/if}
                <table cellpadding="0" cellspacing="0" width="100%" class="sTable withCheck" id="rolelist">
                    {if $roles}
                    <thead>
                      <tr>
                          <td>Title</td>
                          <td>Description</td>
                          <td width="130px">actions</td>
                      </tr>
                    </thead>
                    <tbody>
                    {foreach from=$roles item=v key=k}
                    <tr>
                        <td>
                            <a href="javascript:void(0);" onclick="return acl.showroleform({$v.id});">{$v.name|htmlspecialchars}</a>
                        </td>
                        <td>{$v.description|htmlspecialchars}</td>
                        <td>
                            <a href="javascript:void(0);" onclick="return acl.deleterole({$v.id});">delete</a>
                            <a href="javascript:void(0);" onclick="return acl.rolepermissions({$v.id});">permissions</a>
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                    {else}
                    <tr>
                        <td>There is no roles present</td>
                    </tr>
                    {/if}
                </table>
            {if !$isajax}
            </div>
                <div class="title"><a id="addrolehandler" onclick="return acl.showroleform();" href="javascript:void(0);">Add role</a></div>
            <div class="dn">
                <div id="dialog"></div>
                <div id="roleform">
                    <input type="hidden" id="roleid" name="roleid" />
                    <table>
                        <tr>
                            <td>Name</td>
                            <td><div class="clear"><input class="text ui-widget-content ui-corner-all autocomplete-off" type="text" id="rolename" name="rolename" /></div></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><textarea cols="6" rows="2" id="roledescription" name="roledescription"></textarea></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}