{*
** media item in the object container's detail page
*}

{assign var="thumbWidth" 		value = 130}
{assign var="thumbHeight" 		value = 98}
{assign var="filePath"			value = $item.uri}
{assign var="fileName"			value = $item.filename|default:$item.name}
{assign var="fileTitle"			value = $item.title}
{assign var="newPriority"		value = $item.priority+1|default:$priority}
{assign var="mediaPath"         value = $conf->mediaRoot}
{assign var="mediaUrl"          value = $conf->mediaUrl}

{assign_concat var="linkUrl"            0=$html->url('/multimedia/view/') 1=$item.id}

{assign_concat var="imageAltAttribute"	0="alt='"  1=$item.title 2="'"}

{assign_associative var="params" presentation="thumb" width=$thumbWidth height=$thumbHeight longside=false mode="fill" modeparam="000000" type=null upscale=false}
{assign_associative var="htmlAttr" alt=$item.title title=$item.name}

{literal}
<script type="text/javascript">
$(document).ready(function(){
	$(".info_file_item").change(function() {
		$(this).parents(".multimediaitem").css("background-color","gold").find(".mod").val(1);
	})
});
</script>
{/literal}


{strip}
	
	<input type="hidden" class="media_nickname" value="{$item.nickname}" />

	<input type="hidden" name="data[RelatedObject][{$relation}][{$item.id}][id]" value="{$item.id}" />
	
	<input type="hidden" class="mod" name="data[RelatedObject][{$relation}][{$item.id}][modified]" value="0" />
	
	<div style="width:{$thumbWidth}px; height:{$thumbHeight}px" class="imagebox">
	{if strtolower($item.ObjectType.name) == "image"}
		{if empty($item.file_size)}
			{if ($thumbHeight < $item.height) || ($thumbWidth < $item.width)}
				{array_add var="htmlAttr" width=$thumbWidth height=$thumbHeight}
			{/if}
		{/if}

		{$beEmbedMedia->object($item,$params,$htmlAttr)}
		
	{elseif ($item.provider|default:false)}
	
		{assign_concat var="myStyle" 0="width:" 1=$conf->media.video.thumbWidth 2="px; " 3="height:" 4=$conf->media.video.thumbHeight 5="px;"}
		{assign_associative var="attributes" style=$myStyle}
		{$beEmbedMedia->object($item,$params,$attributes)}
	
	{elseif strtolower($item.ObjectType.name) == "audio"}
	
		<a href="{$linkUrl}"><img src="{$session->webroot}img/iconset/88px/audio.png" /></a>	
	
	{else}

		{$beEmbedMedia->object($item, $params)}
	
	{/if}
	
	</div>
	

	
	<label class="evidence">
		<input type="text" class="priority" style="text-align:left; margin-left:0px;"
		name="data[RelatedObject][{$relation}][{$item.id}][priority]" value="{$item.priority|default:$priority}" size="3" maxlength="3"/>
	</label>


	<ul class="info_file_item">
		<li>
			<input class="info_file_item" style="border:0px;" type="text" value="{$item.title|escape:'htmlall':'UTF-8'|default:""}" 
			name="data[RelatedObject][{$relation}][{$item.id}][title]" />
		</li>
		<li>
			<textarea class="info_file_item" style="width:100%; border:0px; border-bottom:0px solid silver;" 
			name="data[RelatedObject][{$relation}][{$item.id}][description]">{$item.description|default:""}</textarea>
			<br />
			<table style="width:100%; margin-top:5px" class="ultracondensed">
				<tr>
					<td><a title="info" rel="{$linkUrl} .multimediaiteminside" style="padding:2px 6px 2px 6px !important" class="BEbutton modalbutton">{t}info{/t}</a></td>
					<td><a title="edit" href="{$linkUrl}" style="padding:2px 6px 2px 6px !important" class="BEbutton" target="_blank">{t}edit{/t}</a></td>		
					<td><a title="remove" href="javascript: void(0);" style="padding:2px 6px 2px 6px !important" class="BEbutton" onclick="removeItem('item_{$item.id}')">{t}x{/t}</a></td>
				</tr>
			</table>
		</li>
	</ul>


{/strip}
