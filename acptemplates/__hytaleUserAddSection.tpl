{if $action == 'edit' && $__wcf->getSession()->getPermission('admin.hytaleLinker.canManage') && HYTALE_LINKER_ENABLED && HYTALE_LINKER_IDENTITY}

	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.acp.page.userAddSection.hytale.sectionTitle{/lang}</h2>
		{if !$userID|empty}
			<a href="{link controller='HytaleUserAdd' id=$userID}{/link}" class="button">
				{icon size=16 name='plus' type='solid'} {lang}wcf.acp.page.userAddSection.hytale.add{/lang}
			</a>
		{/if}

		{if $hytaleUsers|isset && !$hytaleUsers|empty}
			<div class="tabularBox">
				<table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\user\hytale\HytaleUserAction">
					<thead>
						<tr>
							<th></th>
							<th>{lang}wcf.global.objectID{/lang}</th>
							<th>{lang}wcf.global.title{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.hytale.hytaleUUID{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.hytale.hytaleName{/lang}</th>
							<th>{lang}wcf.acp.page.userAddSection.hytale.connectedSince{/lang}</th>
						</tr>
					</thead>
					<tbody class="jsReloadPageWhenEmpty">
						{foreach from=$hytaleUsers item=hytaleUser}
							<tr class="jsObjectActionObject" data-object-id="{@$hytaleUser->getObjectID()}">
								<td>
									<a href="{link controller='HytaleUserEdit' id=$hytaleUser->getObjectID()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
										{icon size=16 name='pencil' type='solid'}
									</a>
									{objectAction action="delete" objectTitle=$hytaleUser->getTitle()}
									{event name='rowButtons'}
								</td>
								<td class="columnID">{#$hytaleUser->getObjectID()}</td>
								<td class="columnTitle">{$hytaleUser->getTitle()}</td>
								<td class="columnText">{$hytaleUser->getHytaleUUID()}</td>
								<td class="columnText">{$hytaleUser->getHytaleName()}</td>
								<td class="columnDate">{@$hytaleUser->getCreatdDate()|time}</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		{else}
			<p class="info">{lang}wcf.global.noItems{/lang}</p>
		{/if}
	</section>
{/if}