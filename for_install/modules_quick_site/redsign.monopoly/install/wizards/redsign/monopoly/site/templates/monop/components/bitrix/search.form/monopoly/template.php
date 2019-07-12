<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><!-- Search Block --><?
?><ul class="nav navbar-nav navbar-border-bottom navbar-right list-unstyled hidden-xs hidden-sm"><?
	?><li><?
		?><i class="search fa search-btn lupa"></i><?
		?><div class="search-open"><?
			?><form action="<?=$arResult["FORM_ACTION"]?>"><?
				?><div class="input-group animated fadeInDown"><?
					?><input type="text" name="q" class="form-control" placeholder="<?=GetMessage('RS.MONOPOLY.PLACEHOLDER')?>"><?
					?><span class="input-group-btn"><?
						?><button class="btn btn-primary" name="s" type="submit"><?=GetMessage('RS.MONOPOLY.BTN')?></button><?
					?></span><?
				?></div><?
			?></form><?
		?></div><?
	?></li><?
?></ul><?
?><!-- End Search Block -->