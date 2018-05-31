<?
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

$module_id = "reaspekt.geobase";

if (!CModule::IncludeModule($module_id)) {
    ShowError("Error! Module no install");
	return;
}

$request = Application::getInstance()->getContext()->getRequest();

$strCityName = htmlspecialchars(trim($request->getPost("city_name")));

if(strlen($strCityName) >= 2):
    $arCity = ReaspGeoIP::SelectQueryCity($strCityName);
    
    //ќпределилс¤ город по умолчанию
    $arData = ReaspGeoIP::GetAddr();
?>

	<?if(!empty($arCity)):?>
		<?
        $cell = 1;
		
		foreach($arCity as $valCity):?>
        <div class="reaspektSearchCity">
            <?if($arData["CITY"] == $valCity["CITY"]):?>
            <strong><?=$valCity["CITY"]?></strong>
            <?else:?>
            <a onclick="objJCReaspektGeobase.onClickReaspektGeobase('<?=$valCity["CITY"]?>'); return false;" id="reaspekt_geobase_list_<?=$cell?>" title="<?=$valCity["CITY"]?>" href="javascript:void(0);">
            <?=ReaspGeoIP::StrReplaceStrongSearchCity($strCityName, $strCityName, $valCity["CITY"]);?>, <?=$valCity["REGION"]?></a>
            <?endif;?>
        </div>
		<?
            $cell++;
        endforeach;?>
	<?else:?>
        <div class="reaspektNotFound"><?=Loc::getMessage("REASPEKT_RESULT_CITY_NOT_FOUND");?></div>
	<?endif?>
<?endif;?>