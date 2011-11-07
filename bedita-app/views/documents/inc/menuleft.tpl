{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}
<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	{$view->element('messages')}
	
	<ul class="menuleft insidecol">
		<li {if $view->action eq 'index'}class="on"{/if}>{$tr->link('Documents', '/documents')}</li>
		<li {if $view->action eq 'categories'}class="on"{/if}>{$tr->link('Categories', '/documents/categories')}</li>
		{if $view->viewVars.module_modify eq '1'}
		<li><a href="{$html->url('/')}{$currentModule.url}/view">{t}Create new document{/t}</a></li>
		{/if}
	</ul>

{$view->element('export')}

	{if (!empty($view->action)) && $view->action eq "index"}
	<div class="insidecol publishingtree">

		{$view->element('tree')}
	
	</div>
	{/if}

{$view->element('user_module_perms')}
	
	
</div>