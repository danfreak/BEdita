{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="primacolonna">


		<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>


	{$view->element('messages')}
	
{*
		<ul class="menuleft insidecol">
			<li><a href="{$html->url('/translations/view')}">{t}New Translation{/t}</a></li>
		</ul>
*}

	{$view->element('user_module_perms')}


</div>
