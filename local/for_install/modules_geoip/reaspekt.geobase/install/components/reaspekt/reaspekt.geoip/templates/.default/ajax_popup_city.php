<?
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$module_id = "reaspekt.geobase";

if (!CModule::IncludeModule($module_id)) {
    ShowError("Error! Module no install");
	return;
}

$arData = ReaspGeoIP::GetAddr();

$arResult["DEFAULT_CITY"] = ReaspGeoIP::DefaultCityList();
?>
<div class="reaspektGeobaseWrapperPopup">
	<div class="reaspektGeobaseFind">
		<input type="text" onkeyup="objJCReaspektGeobase.inpKeyReaspektGeobase(event);" autocomplete="off" placeholder="<?=Loc::getMessage("REASPEKT_INPUT_ENTER_CITY");?>" name="reaspekt_geobase_search" id="reaspektGeobaseSearch">
	</div>
	
	<div class="reaspektGeobaseTitle"><?=Loc::getMessage("REASPEKT_TITLE_ENTER_CITY");?>:</div>				
	<div class="reaspektGeobaseCities reaspekt_clearfix">
		<div class="reaspekt_row">
		<?
        if ($arResult["DEFAULT_CITY"]) :
			$LINE_ELEMENT_COUNT = ceil(count($arResult["DEFAULT_CITY"])/3);
			$cellCol = 0;
            $cell = 1;
			
			foreach($arResult["DEFAULT_CITY"] as $arCity):?>
				<?if($cellCol%$LINE_ELEMENT_COUNT == 0 || $cellCol > $LINE_ELEMENT_COUNT):?>
					<div class="reaspekt_col-sm-4">
					<?$cellCol = 0;?>
				<?endif;?>
			
				<div class="reaspektGeobaseAct">
					<?if($arData["CITY"] == $arCity["CITY"]):?>
					<strong><?=$arCity["CITY"]?></strong>
					<?else:?>
					<a onclick="objJCReaspektGeobase.onClickReaspektGeobase('<?=$arCity["CITY"]?>'); return false;" id="reaspekt_geobase_list_<?=$cell?>" title="<?=$arCity["CITY"]?>" href="javascript:void(0);"><?=$arCity["CITY"]?></a>
					<?endif;?>
				</div>
				<?
				$cellCol++;
				if($cellCol%$LINE_ELEMENT_COUNT == 0):?>
					</div>
				<?endif;?>
			<?
                $cell++;
            endforeach;?>
			<?if($cellCol%$LINE_ELEMENT_COUNT != 0):?>
				</div>
			<?endif?>
        <?endif;?>
		</div>
	</div>
</div>