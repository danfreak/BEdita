{if ($conf->mce|default:true)}
	{$javascript->link("tiny_mce/tiny_mce", false)}
{/if}
<script type="text/javascript">
{literal}
$(document).ready(function(){
	var v = $().jquery;
	$("#jquery-version").text(v);
	if(tinymce != undefined) {
		v = tinymce.majorVersion + "." + tinymce.minorVersion;
		$("#tinymce-version").text(v);
	}
});
{/literal}
//-->
</script>	


<div class="tab"><h2>{t}System info{/t}</h2></div>

<fieldset id="system_info">
	
<table class="indexlist">
	<tr>
		<th colspan="2" style="text-transform:uppercase">{t}Software{/t}</th>
	</tr>
	<tr>
		<td><label>BEdita</label></td>
		<td>{$conf->Bedita.version}</td>
	</tr>
	<tr>
		<td><label>CakePHP</label></td>
		<td>{$conf->version()}</td>
	</tr>
	<tr>
		<td><label>PHP</label></td>
		<td>{$sys.phpVersion}</td>
	<tr>
		<td><label>PHP extensions</label></td>
		<td style="width:480px">{foreach from=$sys.phpExtensions item="ext"} {$ext}{/foreach}</td>
	</tr>
	<tr>
		<td><label>{$sys.db}</label></td>
		<td>server: {$sys.dbServer} - client: {$sys.dbClient} - host: {$sys.dbHost} - db: {$sys.dbName}</td>
	</tr>
	<tr>
		<td><label>Smarty</label></td>
		<td>{$smarty.version}</td>
	<tr>
	<tr>
		<td><label>JQuery</label></td>
		<td><p id="jquery-version"></p></td>
	</tr>
{if ($conf->mce|default:true)}
	<tr>
		<td><label>TinyMCE</label></td>
		<td><p id="tinymce-version"></p></td>
	</tr>
{/if}	
	<tr>
		<td><label>Operating System</label></td>
		<td>{$sys.osVersion}</td>
	</tr>

	<tr>
		<th colspan="2" style="text-transform:uppercase">{t}URLs and paths{/t}</th>
	</tr>
	<tr>
		<td><label>Media files URL</label></td>
		<td><a href="{$conf->mediaUrl}" target="_blank">{$conf->mediaUrl}</a></td>
	</tr>
	<tr>
		<td><label>Media files root path</label></td>
		<td>{$conf->mediaRoot}</td>
	</tr>
	<tr>
		<td><label>BEdita URL</label></td>
		<td><a href="{$conf->beditaUrl}" target="_blank">{$conf->beditaUrl}</a></td>
	</tr>
	<tr>
		<td><label>BEdita app path</label></td>
		<td>{$sys.beditaPath}</td>
	</tr>
	<tr>
		<td><label>CakePHP path</label></td>
		<td>{$sys.cakePath}</td>
	</tr>
</table>

</fieldset>