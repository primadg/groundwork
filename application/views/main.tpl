<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xml:lang="en" lang="en">
{include file="regions/head.tpl"}
{include file="regions/errors.tpl"}
<body>
<!-- Left side content -->
<div id="leftSide">
    <div class="sidebarSep"></div>
    <!-- Left navigation -->
    <ul id="menu" class="nav">
        <li class="tables"><a href="javascript:void(0);" title="" class="exp inactive"><span>Tables</span><strong></strong></a>
            <ul class="sub">
                <li><a href="{$site_url}liststaticajax" title="">Static AJAX List</a></li>
                <li class="last"><a href="{$site_url}listsdynamic" title="">Dynamic AJAX List</a></li>
            </ul>
        </li>
        <li class="forms"><a href="javascript:void(0);" title="" class="exp inactive"><span>Forms stuff</span><strong></strong></a>
            <ul class="sub">
                <li><a href="{$site_url}formvalidation" title="">Validation</a></li>
            </ul>
        </li>
        <li class="typo"><a href="{$site_url}gallery" title="" class="exp inactive"><span>Gallery</span><strong></strong></a></li>
        <li class="typo"><a href="javascript:void(0);" title="" class="exp inactive"><span>Pagers</span><strong></strong></a>
            <ul class="sub">
                <li class="last"><a href="{$site_url}paginators" title="">Pagers</a></li>
            </ul>
        </li>
        <li class="typo"><a href="javascript:void(0);" title="" class="exp inactive"><span>ACL</span><strong></strong></a>
            <ul class="sub">
                <li class="last"><a href="{$site_url}aclmanager/roles" title="">Roles</a></li>
                <li class="last"><a href="{$site_url}aclmanager/permissions" title="">Permissions</a></li>
                <li class="last"><a href="{$site_url}aclmanager/permissionmarix" title="">Permissions matrix</a></li>
            </ul>
        </li>
    </ul>
</div>
{$content}
{include file="regions/footer.tpl"}
</body>
</html>