<form action="" method="POST">
        <div class="">
        <div class="left inputtitle"><b>Login:</b></div>
        <div class="left inputtitle">&nbsp;</div>
        <div class="left inputtitle"><b>Password:</b></div>
        <div class="clear-all"></div>
        <div class="left"><input type="text" name="login" value="{$aData.login|default:''}"/></div>
        <div class="left"><input type="password" name="password" value="{$aData.password|default:''}"/></div>
        <div class="left"><input type="submit" value="login"/></div>
        <div class="clear-all"></div>
        <div><a href="javascript:void(0);" onclick="$('#forgotform').slideToggle('fast');">Forgot password</a></div>
        </div>
        <div class="clear-all"></div>
</form>
<div class="forgotform {if !$aData.forgot|default:0}dn{/if}" id="forgotform">
        <form action="{$site_url}user/forgot" method="POST">
                <div>
                        <div class="left">Email</div>
                        <div class="left"><input type="text" value="{$aData.email|default:''}" name="email" /></div>
                        <div class="left"><input type="submit" value="Send" /></div>
                </div>
        </form>
</div>