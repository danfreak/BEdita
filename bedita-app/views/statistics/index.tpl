
{$javascript->link("form", false)}

{$javascript->link("jquery/jquery.tablesorter.min",false)}

{literal}
<script type="text/javascript">
    $(document).ready(function(){
		
		openAtStart("#objects");
		
	
		$(".sortableTable").tablesorter( {sortList: [[1,1]]} ); 


    });
</script>
{/literal}



{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index"}

<div class="head">
	
	<div class="toolbar" style="white-space:nowrap">
	{if !empty($sectionSel)} 
		<h2>{$moduleName} for “ <span style="color:white" class="evidence">{$sectionSel.title}</span> ”</h2>
	{/if}
	</div>

</div>

<div class="mainfull">

{include file="inc/statsobjects.tpl"}

{include file="inc/statseditors.tpl"}

{include file="inc/statsusers.tpl"}

</div>