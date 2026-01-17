{if ($action == 'add' && $success|isset && $success) || $showNoUnknownUsers || $showMaxReached}
	<meta http-equiv="refresh" content="3;url={link controller='HytaleUserList'}{/link}" />
{/if}

{include file='userMenuSidebar'}

{include file='header' __sidebarLeftHasMenu=true}

{if $action == 'add' && $success|isset && $success}
	<div class="success">
		<p>{lang}wcf.global.success.{$action}{/lang}</p>
		<a href="{link controller='HytaleUserList'}{/link}">{lang}wcf.page.redirect.url{/lang}</a>
	</div>
{else}
	{if $showMaxReached}
		<div class="error">
			<p>{lang}wcf.form.hytaleUserAdd.error.maxReached{/lang}</p>
			<a href="{link controller='HytaleUserList'}{/link}">{lang}wcf.page.redirect.url{/lang}</a>
		</div>
	{/if}
	{if $showNoUnknownUsers}
		<div class="error">
			<p>{lang}wcf.form.hytaleUserAdd.error.noUnknownUsers{/lang}</p>
			<a href="{link controller='HytaleUserList'}{/link}">{lang}wcf.page.redirect.url{/lang}</a>
		</div>
	{/if}
{/if}

{if !$showMaxReached && !$showNoUnknownUsers}
	{@$form->getHtml()}
{/if}

<footer class="contentFooter">
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file='footer'}
