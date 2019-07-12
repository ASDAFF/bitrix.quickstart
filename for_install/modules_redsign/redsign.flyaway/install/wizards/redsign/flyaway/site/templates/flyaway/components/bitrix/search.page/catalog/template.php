<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="search-page">
<form class="form" action="" method="get">
	<div class="row">
		<div class="col col-sm-9 col-md-7 col-lg-6">
			<div class="input-group form">
				<input class="form-item form-control" type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" size="40" />
				<span class="input-group-btn">
					<input class="btn btn-default btn2" type="submit" value="<?=GetMessage("SEARCH_GO")?>" />
				</span>
			</div>
		</div>
	</div>
	<input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />
</form><br />

<?if(isset($arResult["REQUEST"]["ORIGINAL_QUERY"])):
	?>
	<div class="search-language-guess">
		<?echo GetMessage("CT_BSP_KEYBOARD_WARNING", array("#query#"=>'<a href="'.$arResult["ORIGINAL_QUERY_URL"].'">'.$arResult["REQUEST"]["ORIGINAL_QUERY"].'</a>'))?>
	</div><br /><?
endif;?>
</div>
