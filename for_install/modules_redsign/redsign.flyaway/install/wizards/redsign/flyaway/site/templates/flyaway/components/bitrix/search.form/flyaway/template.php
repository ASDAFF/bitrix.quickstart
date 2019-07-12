<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><!-- Search Block --><?
	?><form action="<?=$arResult["FORM_ACTION"]?>"><?
		?><div class="input-group form"><?
			?><input type="text" class="form-item form-control" name="q" placeholder="<?=GetMessage('RS.MONOPOLY.PLACEHOLDER')?>"><?
			?><div class="input-group-btn"><?
				?><input class="btn btn-default btn2" name="s" type="submit" value="<?=GetMessage('RS.MONOPOLY.BTN')?>" /><?
			?></div><?
		?></div><?
	?></form><?
?><!-- End Search Block --> 