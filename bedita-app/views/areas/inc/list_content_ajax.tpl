{$javascript->link("jquery/jquery.disable.text.select", true)}

<script type="text/javascript">
<!--
var urlAddObjToAss = "{$html->url('/pages/loadObjectToAssoc')}/{$object.id|default:0}/leafs/areas.inc.list_contents_for_section";
var priorityOrder = "{$priorityOrder|default:'asc'}";

{literal}

function addObjToAssoc(url, postdata) {
	$.post(url, postdata, function(html){
		if(priorityOrder == 'asc') {
			var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
			$("#areacontent tr:last").after(html);
		} else {
			var startPriority = parseInt($("#areacontent").find("input[name*='[priority]']:first").val());
			var beforeInsert = parseInt($("#areacontent tr").size());
			$("#areacontent tr:first").before(html);
			var afterInsert = parseInt($("#areacontent tr").size());
			startPriority = startPriority + (afterInsert - beforeInsert);
		}

		if ($("#noContents"))
			$("#noContents").remove();
		$("#areacontent").fixItemsPriority(startPriority);
		$("#areacontent").sortable("refresh");
		$("#areacontent table").find("tbody").sortable("refresh");
		setRemoveActions();
	});
}

function setRemoveActions() {
	$("#areacontent").find("input[name='remove']").click(function() {
		var contentField = $("#contentsToRemove").val() + $(this).parents().parents().find("input[name*='[id]']").val() + ",";
		$("#contentsToRemove").val(contentField);
		var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
		
		if (priorityOrder == "desc" && $(this) != $("#areacontent").find("input[name*='[priority]']:first")) {
			startPriority--;
		}
		
		$(this).parents().parents("tr").remove();
		

		$("#areacontent").fixItemsPriority(startPriority);
	});
}

$(document).ready(function() {

	if ($("#areacontent").find("input[name*='[priority]']:first"))
		var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
	else
		var startPriority = 1;
		
	var urlC = ajaxContentsUrl + "/{/literal}{$selectedId|default:''}{literal}";

	//$("#areacontent").sortable ({
	$("#areacontent table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: function() {
					if (priorityOrder == 'desc' && startPriority < $("#areacontent").find("input[name*='[priority]']:first").val()) {
						startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
					}
					$(this).fixItemsPriority(startPriority);
				}
	}).css("cursor","move");

	$("#contents_nav_leafs a").click(function() {
		$("#loading").show();
		$("#areacontentC").load(urlC, 
				{
					page:$(this).attr("rel"),
					dim:$("#dimContentsPage").val()
				}
				, function() {
			$("#loading").hide();
		});
	});
	
	$("#dimContentsPage").change(function() {
		$("#loading").show();
		$("#areacontentC").load(urlC, {dim:$(this).val()}, function() {
			$("#loading").hide();
		});
	});
	
	setRemoveActions();
	
	$(".modalbutton").click(function () {
		$(this).BEmodal();
	});
	
});


    $(function() {
        $('.disableSelection').disableTextSelect();
    });

{/literal}
//-->
</script>



<div style="min-height:120px; margin-top:10px;">

	<div id="areacontent">

	<table class="indexlist" style="width:100%; margin-bottom:10px;">
		<tbody class="disableSelection">
			<input type="hidden" name="contentsToRemove" id="contentsToRemove" value=""/>
			{include file="inc/list_contents_for_section.tpl" objsRelated=$contents.items}
			<tr class="obj">
				
			</tr>
		</tbody>
	</table>
	
	</div>
	

{if !empty($contents.items)}
	<div id="contents_nav_leafs" style="margin-top:10px;">
	{t}show{/t} 
	<select name="dimContentsPage" id="dimContentsPage" class="ignore">
		<option value="5"{if $dim == 5} selected{/if}>5</option>
		<option value="10"{if $dim == 10} selected{/if}>10</option>
		<option value="20"{if $dim == 20} selected{/if}>20</option>
		<option value="50"{if $dim == 50} selected{/if}>50</option>
		<option value="100"{if $dim == 100} selected{/if}>100</option>
		<option value="1000000"{if $dim == 1000000} selected{/if}>{t}all{/t}</option>
	</select>
	{t}item(s){/t} 
	
		<div class="toolbar" style="text-align:right; padding-left:150px; float:right;">
		{if $contents.toolbar.prev > 0}
			<a href="javascript:void(0);" rel="{$contents.toolbar.prev}" class="" style="color:#000; font-size:1.5em">‹ prev</a>
		{/if}
		&nbsp;&nbsp;
		{if $contents.toolbar.next > 0}
			<a href="javascript:void(0);" rel="{$contents.toolbar.next}" class="" style="color:#000; font-size:1.5em">next ›</a>
		{/if}
		</div>
	</div>
{/if}



	<br />
	<input style="width:220px" type="button" rel="{$html->url('/pages/showObjects/')}{$object.id|default:0}/0/0/leafs" class="modalbutton" value=" {t}add contents{/t} " />

{literal}
<script>
	$(".newcontenthere").submit(function(){
		var urltogo = $('.newcontenthere :selected').attr("value");
		window.location.href = urltogo;
		return false;
	});	
</script>
{/literal}

	<form action="#" style="margin-top:10px;" class="newcontenthere">
	{t}create new{/t} &nbsp;
	<select class="ignore">
	{assign var=leafs value=$conf->objectTypes.leafs}
		{foreach from=$conf->objectTypes item=type key=key}	
			{if ( in_array($type.id,$leafs.id) && is_numeric($key) )}
			<option value="{$html->url('/')}{$type.module_name}/view/branch:{$html->params.pass.0}" {if ($type.model=="Document")} selected{/if}>	
				{t}{$type.model}{/t}
			</option>
			{/if}
		{/foreach}
	</select>
	 &nbsp;
	{t}here{/t} ({$html->params.pass.0}) &nbsp;
	<input type="submit" value="GO" />
	</form>
	
	<hr />
</div>	
	