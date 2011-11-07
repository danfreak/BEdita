<div class="tab"><h2>{t}BEdita editors activity statistics{/t}</h2></div>
	<div id="statseditors">	

		<h2>{t}Objects production by editor{/t}</h2>

		<table class="graph sortableTable">
			<thead>
				<th style="text-align:right"><b>editor</b></th>
				<th><b>contents</b></th>
			</thead>
			<tbody>
			{foreach from=$objectsForUser key="user_id" item="user"}
			<tr>
				<td class="label" onclick="window.location.href='/admin/viewUser/{$user.id}'" style="cursor:pointer; border-right:1px solid gray; border-bottom:0px solid gray">
					<a href="{$html->url('/')}/admin/viewUser/{$user.id}">{$user.realname|default:$user.userid}</a>
				</td>
			{strip}
			<td style="text-align:right; text-align:center; border-right:1px solid gray; border-bottom:0px solid gray"> {$totalObjectsForUser[$user_id]} </td>
				<td style="white-space:nowrap;">
				{foreach from=$user.objects key="objectType" item="num"}
					{if isset($conf->objectTypes[$objectType].module_name)}
					{math assign="pixel" equation="(x/y)*350" x=$num y=$maxObjectsForUser}
					<div title="{$objectType}" style="width:{$pixel|format_number}px;" class="bar {$objectType}"></div>
					{/if}
				{/foreach}
					{*<span class="value">{$totalObjectsForUser[$user_id]}</span>*}
				</td>
			{/strip}
			</tr>
			{/foreach}
			</tbody>
		</table>


	</div>