<?if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();

if(!empty($arParams["TEMPLATE_AJAX_ID"])) {
    ?><div id="<?=$arParams['TEMPLATE_AJAX_ID']?>_sorter"><?
}

$this->SetViewTarget($arParams['TEMPLATE_AJAX_ID'].'_sorter');

?><div class="catalogsorter clearfix <?=$arParams["USE_AJAX"]=="Y"? "js-sorterajax" : ""?>"<?
    ?>id="composite_sorter"<?
    ?>data-catalog-template="<?=$arParams["TEMPLATE_AJAX_ID"]?>"><?
	$frame = $this->createFrame('composite_sorter', false)->begin();
	$frame->setBrowserStorage(true);

	if($arParams['USE_FILTER']=='Y'){
		?><div class="visible-xs visible-sm pull-left filterbtn dropdown"><?
			?><button class="btn btn-default dropdown-toggle showfilter" type="button"><?
				?><i class="fa"></i><?
			?></button><?
		?></div><?
	}

	if($arParams['ALFA_OUTPUT_OF_SHOW']=='Y') {
		?><div class="pull-left output"><?
			?><span class="title hidden-xs hidden-sm"><?=GetMessage('RS.MONOPOLY.OUTPUT_TITLE')?></span><?
			?><div class="dropdown"><?
				?><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuOutput" data-toggle="dropdown" aria-expanded="true"><?
					?><i class="fa visible-xs-inline fileicon"></i><?=$arResult['USING']['COUTPUT']['ARRAY']['VALUE']?><i class="fa hidden-xs value arrowright"></i><?
				?></button><?
				?><ul class="dropdown-menu list-unstyled" role="menu" aria-labelledby="dropdownMenuOutput"><?
					foreach($arResult['COUTPUT'] as $output) {
						?><li><a href="<?=$output['URL']?>"><?=$output['VALUE']?></a></li><?
					}
				?></ul><?
			?></div><?
		?></div><?
	}

	if($arParams['ALFA_SORT_BY_SHOW']=='Y') {
		?><div class="pull-left sortby dropdown"><?
			?><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuSortBy" data-toggle="dropdown" aria-expanded="true"><?
				?><span class="hidden-xs hidden-sm"><?=GetMessage('RS.MONOPOLY.SORT_VARIABLE.'.strtolower($arResult['USING']['CSORTING']['ARRAY']['VALUE']))?><i class="fa arrowright"></i></span><?
				?><span class="visible-xs visible-sm"><?=GetMessage('RS.MONOPOLY.SORT_VARIABLE_ICON.'.strtolower($arResult['USING']['CSORTING']['ARRAY']['VALUE']))?></span><?
			?></button><?
			?><ul class="dropdown-menu list-unstyled" role="menu" aria-labelledby="dropdownMenuSortBy"><?
				foreach($arResult['CSORTING'] as $sort) {
					?><li><a href="<?=$sort['URL']?>"><?
						?><span class="hidden-xs hidden-sm"><?=GetMessage('RS.MONOPOLY.SORT_VARIABLE.'.strtolower($sort['VALUE']))?></span><?
						?><span class="visible-xs visible-sm"><?=GetMessage('RS.MONOPOLY.SORT_VARIABLE_ICON.'.strtolower($sort['VALUE']))?></span><?
					?></a></li><?	
				}
			?></ul><?
		?></div><?
	}

	if($arParams['ALFA_CHOSE_TEMPLATES_SHOW']=='Y') {
		?><div class="pull-right hidden-xs template"><?
			foreach($arResult['CTEMPLATE'] as $template) {
				?><a<?if($template['USING']=='Y'):?> class="selected"<?endif;?> href="<?=$template['URL']?>"><i class="fa <?=$template['VALUE']?>"></i></a><?
			}
		?></div><?
		?><div class="pull-right visible-xs templateDrop dropdown"><?
			?><button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuTemplate" data-toggle="dropdown" aria-expanded="true"><?
				?><i class="fa <?=$template['VALUE']?>"></i><?
			?></button><?
			?><ul class="dropdown-menu list-unstyled" role="menu" aria-labelledby="dropdownMenuTemplate"><?
				foreach($arResult['CTEMPLATE'] as $template) {
					?><li><a href="<?=$template['URL']?>"><?
						?><i class="fa <?=$template['VALUE']?>"></i><?
					?></a></li><?	
				}
			?></ul><?
		?></div><?
	}

	?><div class="comparising"></div><?

	$frame->end();
?></div><?

?><script>
if( $('.comparelist').length>0 ){
	$('.comparising').append($('.comparelist').clone());
}
</script><?

$this->EndViewTarget();
    
echo $APPLICATION->GetViewContent($arParams['TEMPLATE_AJAX_ID'].'_sorter');
if(!empty($arParams["TEMPLATE_AJAX_ID"])) {
    ?></div><?
}