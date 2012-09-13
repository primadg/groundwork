<form action="" method="POST">
    <table style="border: 1px solid black;">
        <tr>
            <th style="border: 1px solid black;">Permission</th>
            {foreach from=$roles item=v key=k}
            <th style="border: 1px solid black;">{$v.name}</th>
            {/foreach}
        </tr>
    {foreach from=$AllRolePermissions item=permission key=name}
        <tr style="border: 1px solid black;">
            <td style="border: 1px solid black;" width="200px">{$name}</td>
        {foreach from=$permission item=data key=role}
            <td style="border: 1px solid black;">
                <label><input type="radio" {if $data.present&&$data.status==$smarty.const.PERMISSION_STATUS_ALLOW}checked{/if} name="permissions[{$role}][{$data.id}]" value="{$smarty.const.PERMISSION_STATUS_ALLOW}" />allow</label>
                <label><input type="radio" {if $data.present&&$data.status==$smarty.const.PERMISSION_STATUS_DENY}checked{/if} name="permissions[{$role}][{$data.id}]" value="{$smarty.const.PERMISSION_STATUS_DENY}" />deny</label>
            </td>
        {/foreach}
        </tr>
    {/foreach}
        <tr>
            <td colspan="{$roles|count+1}">
                <input type="submit" value="Save" />
            </td>
        </tr>
    </table>
</form>