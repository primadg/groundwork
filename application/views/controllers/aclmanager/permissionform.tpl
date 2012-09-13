<form id="permissionform" action="javascript:void(0);">
    <input type="hidden" name="id" id="id" value="{$permission.id}"/>
    <table>
        <tr>
            <td>Parent</td>
            <td>
                <select id="parent" name="parent">
                    <option value="">--</option>
                    {foreach from=$permissions item=v key=k}
                    <option value="{$v.id}">{$v.name}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td>Name</td>
            <td>
                <input name="name" id="name" value="{$permission.name}" />
            </td>
        </tr>
        <tr>
            <td>Description</td>
            <td>
                <textarea cols="6" rows="3" name="description" id="description">{$permission.description}</textarea>
            </td>
        </tr>
    </table>
</form>