{$javascript->link("jquery/jquery.tooltip.min")}
	
{if $editors|@count > 1}

	{literal}

	<script type="text/javascript">
		if ((autoSaveTimer !== false) && (autoSaveTimer != undefined))
			switchAutosave("off", false);
		else
			switchAutosave("off");
		
		$(".secondacolonna .modules label:not(.concurrentuser)")
		.addClass("concurrentuser")
		.attr("title","Warning! More users are editing this document")
		.tooltip({
			extraClass: "tip",
			fixPNG: true,
			top: 10,
			left: -90

		});
	</script>

	{/literal}

	{t}Warning{/t}.<br/>
	{t}Concurrent editors:{/t}

	<!-- <img src="{$html->url('/')}img/iconConcurrentuser.png" style="float:left; vertical-align:middle; width:20px; margin-right:10px;" /> -->

	<ul id="editorsList" style="margin-bottom:10px">
	{foreach from=$editors item="item"}
		<li rel="{$item.User.id}" style="border-bottom:1px solid gray">
			<b>{$item.User.realname|default:$item.User.userid}</b>
		</li>
	{/foreach}
	</ul>
	
{else}

	<script type="text/javascript">
	{literal}
	if (autoSaveTimer === false) {
		var newStatus = $("input[name=data\\[status\\]]:checked").attr('value');
		if ((status != 'on') && (status == newStatus))
			switchAutosave("on");
	}
	$(".secondacolonna .modules label").removeClass("concurrentuser").tooltip({
			delay: 0
	});
	{/literal}
	</script>
{/if}