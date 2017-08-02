{if $render_type=="all"}
{$p.doctype}
{$p.html_tag}
<head>
<meta http-equiv="Content-Type" content="{$p.content_type}" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
{foreach item=tag from=$p.meta_tags}
<meta name="{$tag.name}" content="{$tag.content}" />
{/foreach}

{$page_after_meta_tags|default:''}

<title>{tr tags="titles"}{$p.title}{/tr}</title>

{eval var=$__before_skin}

{foreach item=style from=$p.css_files}
	{if $style.browser_cond}<!--[{$style.browser_cond}]>{/if}
	<link rel="stylesheet" type="{$style.type}" href="{eval var=$style.href}" media="{$style.media}"/>	
	{if $style.browser_cond}<![endif]-->{/if}
{/foreach}

{foreach item=js from=$p.js_files}
	<script src="{eval var=$js.src}" type="{$js.type}"></script>
{/foreach}

{if isset($p.session.script) && $p.session.script}
<script src="{$root_module}script_file_{$random}.js" type="text/javascript"></script>
{/if}

{$page_before_close_head|default:''}
</head>
{$p.body_tag}
	{$page_after_start_body|default:''}
	{if isset($__noscript) && $__noscript != ''}<noscript>{eval var=$__noscript|default:' '}</noscript>{/if}
	{$p.settings.before_page_text.value}
	
	{$page_trace}
	
	{$page}
    
	{$p.settings.after_page_text.value}
	{$page_before_close_body}
</body>
{$page_after_body}
</html>
{else}
{capture assign=ptemp}{literal}{{/literal}${$render_type}{literal}}{/literal}{/capture}
{eval var=$ptemp}
{$bottom_script}
{/if}