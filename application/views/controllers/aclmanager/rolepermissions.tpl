<form id="rolepermissionsform" action="">
    <table>
        <tr>
            <td>Permission</td>
            <td>Status</td>
        </tr>
    {foreach from=$AllPermissions item=v key=k}
        <tr>
            <td width="200px">{$v.name}</td>
            <td>
                <label><input type="radio" {if $v.present&&$v.status==$smarty.const.PERMISSION_STATUS_ALLOW}checked{/if} name="permission[{$v.id}]" value="{$smarty.const.PERMISSION_STATUS_ALLOW}" />allow</label>
                <label><input type="radio" {if $v.present&&$v.status==$smarty.const.PERMISSION_STATUS_DENY}checked{/if} name="permission[{$v.id}]" value="{$smarty.const.PERMISSION_STATUS_DENY}" />deny</label>
            </td>
        </tr>
    {/foreach}
    </table>
</form>