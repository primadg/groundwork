<div class="container">
        <form action="" method="POST">
                <table class="admtbl">
                        <tr>
                                <td>Login*:</td>
                                <td><input type="text" name="login" value="{$aData.user_info.login}" /></td>
                        </tr>
                        <tr>
                                <td>Password*:</td>
                                <td><input type="password" name="password" value="{$aData.user_info.password}" /></td>
                        </tr>
                        <tr>
                                <td>Confirm password*:</td>
                                <td><input type="password" name="confirm" value="{$aData.user_info.password}" /></td>
                        </tr>
                        <tr>
                                <td>Email*:</td>
                                <td><input type="text" name="email" value="{$aData.user_info.email}" /></td>
                        </tr>
                        <tr>
                                <td>First name*:</td>
                                <td><input type="text" name="first_name" value="{$aData.user_info.first_name}" /></td>
                        </tr>
                        <tr>
                                <td>Last name*:</td>
                                <td><input type="text" name="last_name" value="{$aData.user_info.last_name}" /></td>
                        </tr>
                        {if !$aData.self}
                        <tr>
                                <td><label>Has admin permissions?</label></td>
                                <td><input type="checkbox" name="status" {if $aData.user_info.status}checked="checked"{/if} value="1" /></td>
                        </tr>
                        {/if}
                        {if $aData.user_info.user_id}
                        <tr>
                                <td><label>Member since</label></td>
                                <td><span>{$aData.user_info.created|date_format:'%m/%d/%Y'}</span></td>
                        </tr>
                        <tr>
                                <td><label>Status:</label></td>
                                <td><span>{if $aData.user_info.blocked}<span class="no">blocked</span>{else}<span class="yes">active</span>{/if}</span></td>
                        </tr>                        
                        {/if}
                        <tr>
                                <td colspan="2" align="center">
                                        <input type="submit" value="Save" />
                                        <input type="button" onclick="return history.back();" value="Cancel" />
                                </td>
                        </tr>
                </table>
        </form>
</div>