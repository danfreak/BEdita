{if !empty($section.currentContent.relations)}
<h3>&nbsp;</h3>

			{assign var="attach" value=$section.currentContent.relations.attach|default:''}
			{if !empty($attach)}
			{assign_associative var="paramsBig" width=680 mode="fill" upscale=false URLonly=true}
			{assign_associative var="params" width=220 mode="fill" upscale=false}
			{assign_associative var="paramsHtmlAudio" width="220"}
			{assign_associative var="paramsHtml" height=165 width=220}		

			{section name=i loop=$attach}
			<div class="related">
				{if $attach[i].object_type_id == $conf->objectTypes.image.id}
					<a class="thickbox" href="{$beEmbedMedia->object($attach[i],$paramsBig)}" 
					title="{$attach[i].description}" rel="gallery">
					{$beEmbedMedia->object($attach[i],$params)}
					</a>
				{elseif $attach[i].object_type_id == $conf->objectTypes.audio.id}
					{$beEmbedMedia->object($attach[i],null, $paramsHtmlAudio)}
				{else}
					{$beEmbedMedia->object($attach[i],null, $paramsHtml)}
				{/if}
				
				<p class="dida">
				<span style="font-style: normal; font-weight: bold;">{$attach[i].title}</span><br/>
				{$attach[i].description}</p>
			</div>
			{/section}
			{/if}
			
			{assign var="seealso" value=$beFront->seealso()}
			{if !empty($seealso)}
			<div class="related">
			<h2>See also:</h2>
			<ul>
			{section name="i" loop=$seealso}
				<li>
					<a title="{$seealso[i].title}" href="{$html->url('/')}{$seealso[i].nickname}">
						{$seealso[i].title}
					</a>
				</li>
			{/section}
			</ul>
			</div>
			{/if}
			
			{assign var="links" value=$section.currentContent.relations.link|default:''}
			{if !empty($links)}
			<div class="related">
			<h2>Links:</h2>
			<ul>
			{section name="i" loop=$links}
				<li>
					<a title="{$links[i].title}" href="{$links[i].url}" target="{$links[i].target|default:'_blank'}">
						{$links[i].title}
					</a>
				</li>
			{/section}
			</ul>
			</div>
			{/if}
			
			{assign var="downloads" value=$section.currentContent.relations.download|default:''}
			{if !empty($downloads)}
			<div class="related">
			<h2>Download:</h2>
			<ul>
			{section name="i" loop=$downloads}
				<li>
					<a title="{$downloads[i].title}" href="{$html->url('/')}download/{$downloads[i].nickname}">
						{$downloads[i].title|default:$downloads[i].nickname}
					</a>
				</li>
			{/section}
			</ul>
			</div>
			{/if}
			
{/if}
