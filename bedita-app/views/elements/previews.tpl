
{if !empty($previews)}
	<ul class="menuleft insidecol">
		<li>
			<a href="javascript:void(0)" onclick="$('#previews').slideToggle();">{t}Previews{/t}</a>
			<ul id="previews" style="display:none;">
			{foreach from=$previews item="preview"}
				<li><a title="{$preview.kurl}: {$preview.url}" href="{$preview.url}" target="_blank">{$preview.desc}</a></li>
			{/foreach}
			</ul>
		</li>
	</ul>
{/if}
