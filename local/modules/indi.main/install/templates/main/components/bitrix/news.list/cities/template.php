<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!$arResult['ITEMS']) {
	return;
}

foreach($arResult["ITEMS"] as $arItem)
{
	if($arItem["ID"] == $arParams["CURRENT_CITY_ID"])
		$arCurrentCityElement = $arItem;
}
?>
<div class="city-selector">
	Ваш город: <a class="fake current-city" href="#" data-toggle="modal" data-target="#cities-modal"><?=$arCurrentCityElement["NAME"]?></a>
	<div class="modal fade" id="cities-modal" tabindex="-1" role="dialog" aria-labelledby="cities-modal-label" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Выберите город:</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-6">
							<?foreach($arResult["ITEMS"] as $key => $arItem):?>
								<?if(($key + 1) > intval(count($arResult["ITEMS"]) / 2)):?>
									</div>
									<div class="col-xs-6">
								<?endif?>
								<div class="item"><a href="#" class="fake<?if($arItem["ID"] == $arParams["CURRENT_CITY_ID"]):?> selected<?endif?>" data-id="<?=$arItem["ID"]?>"><?=$arItem["NAME"]?></a></div>
							<?endforeach?>
						</div>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
</div>