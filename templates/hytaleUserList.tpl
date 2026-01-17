{include file='userMenuSidebar'}

{capture assign='contentHeaderNavigation'}
	{if HYTALE_MAX_UUIDS == 0 || $objects|count < HYTALE_MAX_UUIDS}
		<li>
			<a href="{link controller='HytaleUserAdd'}{/link}" class="button">
				{icon size=16 name='plus' type='solid'} {lang}wcf.page.hytaleUserList.add{/lang}
			</a>
		</li>
	{/if}
{/capture}

{include file='header' __sidebarLeftHasMenu=true}

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign='pagesLinks' controller='HytaleUserList' link="pageNo=%d"}
		{/content}
	</div>
{/hascontent}

{if $objects|count > 0}
	<div class="section tabularBox">
		<table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\user\hytale\HytaleUserAction">
			<thead>
				<tr>
					<th></th>
					<th>{lang}wcf.global.objectID{/lang}</th>
					{if HYTALE_MAX_UUIDS > 1}
						<th>{lang}wcf.global.title{/lang}</th>
					{/if}
					<th>{lang}wcf.page.hytaleUserList.table.hytaleUUID{/lang}</th>
					<th>{lang}wcf.page.hytaleUserList.table.hytaleName{/lang}</th>
					<th>{lang}wcf.page.hytaleUserList.table.createdDate{/lang}</th>
				</tr>
			</thead>
			<tbody class="jsReloadPageWhenEmpty">
				{foreach from=$objects item=object}
					<tr class="jsObjectActionObject" data-object-id="{@$object->getObjectID()}">
						<td class="columnIcon">
							{if HYTALE_MAX_UUIDS > 1}
								<a href="{link controller='HytaleUserEdit' id=$object->getObjectID()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
									{icon size=16 name='pencil' type='solid'}
								</a>
							{/if}
							{objectAction action="delete" objectTitle=$object->getTitle()}
						</td>
						<td class="columnID">{#$object->getObjectID()}</td>
						{if HYTALE_MAX_UUIDS > 1}
							<td class="columnText">{$object->getTitle()}</td>
						{/if}
						<td class="columnText">{$object->getHytaleUUID()}</td>
						<td class="columnText">{$object->getHytaleName()}</td>
						<td class="columnDate">{@$object->getCreatdDate()|time}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
	{/hascontent}

	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}{event name='contentFooterNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

{include file='footer'}
