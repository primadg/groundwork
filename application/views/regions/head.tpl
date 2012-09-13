{if isset($sheadContent) && $sheadContent}
{$sheadContent}
{else}
<head>
	<title>{$sSiteTitle}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="{$sMetaKeywords}" />
    <meta name="description" content="{$sMetaDescription}"/>
    <link rel="canonical" href="{$site_url}" />
    <script type="text/javascript">
        {foreach from=$aTemplateVar item=v key=k}
            var {$k}={$v};
        {/foreach}
    </script>
    {$sStyles}
    {$sScripts}
</head>
{/if}