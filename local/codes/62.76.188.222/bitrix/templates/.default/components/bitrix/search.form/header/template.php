<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="b-search">
<form action="<?=$arResult["FORM_ACTION"]?>">
	<?if($arParams["USE_SUGGEST"] === "Y"):?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:search.suggest.input",
			"",
			array(
				"NAME" => "q",
				"VALUE" => "",
				"INPUT_SIZE" => 15,
				"DROPDOWN_SIZE" => 10,
			),
			$component, array("HIDE_ICONS" => "Y")
		);?>
	<?else:?>
		<?if(isset($_REQUEST["q"])):
			$q = trim($_REQUEST["q"]);
			$q = htmlspecialcharsex($q);
		else:	
			$q = "Поиск по сайту";
		endif;?>
		<input type="text" class="b-search__text" name="q" value="<?=$q?>" placeholder="<?=$q?>" />
	<?endif;?>
	<input name="s" class="b-search__submit" type="submit" value="" />
</form>
</div>