{assign var='method' value=$method|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
		
		{$view->element('messages')}

		<ul class="menuleft insidecol">
			
			<li>{$tr->link('Add new item', '/multimedia/view')}</li>	
					
		</ul>

{$view->element('export')}

	{if (!empty($view->action)) && $view->action eq "index"}
	<div class="insidecol publishingtree">

		{$view->element('tree')}

	</div>
	{/if}

{$view->element('user_module_perms')}

</div>




