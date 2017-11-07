<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/sale.order.ajax/lang/'.LANGUAGE_ID.'/map.php');

$obJSPopup = new CJSPopup('',
	array(
		'TITLE' => GetMessage('MYMV_SET_POPUP_TITLE'),
		'SUFFIX' => 'yandex_map',
		'ARGS' => ''
	)
);

CModule::IncludeModule('sale');
CModule::IncludeModule('catalog');
$location = "";
if ($_REQUEST["delivery"])
{
	$deliveryId = IntVal($_REQUEST["delivery"]);

	$dbDelivery = CSaleDelivery::GetList(
			array("SORT"=>"ASC"),
			array("ID" => $deliveryId),
			false,
			false,
			array("ID", "STORE")
			);
	$arDelivery = $dbDelivery->Fetch();

	$arStoreId = array();
	if (count($arDelivery) > 0 && strlen($arDelivery["STORE"]) > 0)
	{
		$arStore = unserialize($arDelivery["STORE"]);
		foreach ($arStore as $val)
			$arStoreId[$val] = $val;
	}

	$arStore = array();
	$arStoreLocation = array("yandex_scale" => 11, "PLACEMARKS" => array());

	$dbList = CCatalogStore::GetList(
			array("ID" => "DESC"),
			array("ACTIVE" => "Y", "ID" => $arStoreId),
			false,
			false,
			array("ID", "TITLE", "ADDRESS", "DESCRIPTION", "IMAGE_ID", "PHONE", "SCHEDULE", "GPS_N", "GPS_S")
		);
	while ($arStoreTmp = $dbList->Fetch())
	{
		$arStore[$arStoreTmp["ID"]] = $arStoreTmp;

		if (floatval($arStoreLocation["yandex_lat"]) <= 0)
			$arStoreLocation["yandex_lat"] = $arStoreTmp["GPS_N"];

		if (floatval($arStoreLocation["yandex_lon"]) <= 0)
			$arStoreLocation["yandex_lon"] = $arStoreTmp["GPS_S"];

		$arLocationTmp = array();
		$arLocationTmp["ID"] = $arStoreTmp["ID"];
		if ($arStoreTmp["GPS_N"] > 0)
			$arLocationTmp["LAT"] = $arStoreTmp["GPS_N"];
		if ($arStoreTmp["GPS_S"] > 0)
			$arLocationTmp["LON"] = $arStoreTmp["GPS_S"];
		if (strlen($arStoreTmp["TITLE"]) > 0)
			$arLocationTmp["TEXT"] = $arStoreTmp["TITLE"]."\r\n".$arStoreTmp["DESCRIPTION"];
		
		$arStoreLocation["PLACEMARKS"][] = $arLocationTmp;
	}

	$location = serialize($arStoreLocation);
}
?>

<style>
.data {
	width: 100%;
	border-collapse: collapse;
	border-spacing: 0;
}
.data td {
	text-align: left;
	vertical-align: top;
	
	width: 50%;
}
.data .map {
	width: 40%;
	padding: 0 25px 0 0;
}
.data .store {
	width: 60%;
	padding: 0;
}
.data .store table td {
	border-bottom: 1px solid #ccc;
}
.data .store table td.last {
	border-bottom: none;
}
.data table td {
	width: auto;
	padding: 10px 3px 10px;
}
.data label {
	font-size: 10px;
	cursor:pointer;
	display: block;
	padding: 10px;
	border-radius: 3px;
}
.data label:hover,
.data input[type=radio]:checked + label {
	border:2px solid rgb(45, 115, 157);
	padding: 8px;
}
.data .name {
	font-weight: bold;
	font-size: 12px;
}
.data input[type=radio] {
	display: none;
}
.data .view_map {
	box-shadow: 3px 3px 4px rgba(180,188,191,0.5);
}
.data .ora-storelist {
	height: 400px;
	overflow: auto;
}
</style>

<?
$obJSPopup->StartContent();
$rnd = "or".randString(4);
?>
<table class="data">
<tr>
	<td class="map">
		<div class="view_map">
		<?$APPLICATION->IncludeComponent(
			"bitrix:map.yandex.view",
			".default",
			Array(
				"INIT_MAP_TYPE" => "MAP",
				"MAP_DATA" => $location,
				"MAP_WIDTH" => "400",
				"MAP_HEIGHT" => "400",
				"CONTROLS" => array(0=>"TYPECONTROL",),
				"OPTIONS" => array(0=>"ENABLE_SCROLL_ZOOM",1=>"ENABLE_DRAGGING",),
				"MAP_ID" => $rnd,
			)
		);?>
		</div>
	</td>
	<td class="ora-store">
		<script>
			var arStore = <?=CUtil::PhpToJSObject($arStore);?>;
			var objName = '<?=$rnd?>';
		</script>
		<div class="ora-storelist">
			<table>
			<?
			$countCount = count($arStore);
			$i = 1;
			?>
			<?foreach ($arStore as $val):?>
			<tr>
				<td class="<?=($countCount != $i)?"lilne":"last"?>">
					<input type="radio" name="store" id="store_<?=$val["ID"]?>" value="<?=$val["ID"]?>" onClick="setChangeStore(this);" />
					<label for="store_<?=$val["ID"]?>">
						<div class="name"><?=htmlspecialcharsbx($val["TITLE"])?></div>
						<div class="phone"><?=GetMessage('MAP_PHONE');?>: <?=htmlspecialcharsbx($val["PHONE"])?></div>
						<div class="adres"><?=GetMessage('MAP_ADRES');?>: <?=htmlspecialcharsbx($val["ADDRESS"])?></div>
						<div class="shud"><?=GetMessage('MAP_WORK');?>: <?=htmlspecialcharsbx($val["SCHEDULE"])?></div>
					</label>
				</td>
			</tr>
			<?
			$i++;
			endforeach;
			?>
			</table>
		</div>
	</td>
</tr>
</table>
<input type="hidden" name="POPUP_STORE_ID" id="POPUP_STORE_ID" value="" >
<input type="hidden" name="POPUP_STORE_NAME" id="POPUP_STORE_NAME" value="" >

<script type="text/javascript">
	function setChangeStore(el)
	{
		BX('POPUP_STORE_ID').value = el.value;
		BX('POPUP_STORE_NAME').value = BX.util.htmlspecialchars(arStore[el.value]["TITLE"]);
		
		if(window.GLOBAL_arMapObjects[objName])
			window.GLOBAL_arMapObjects[objName].panTo([parseFloat(arStore[el.value]["GPS_N"]), parseFloat(arStore[el.value]["GPS_S"])], {flying: 1});
	}

	if (BX('BUYER_STORE') && parseInt(BX('BUYER_STORE').value) > 0)
	{
		BX('store_' + BX('BUYER_STORE').value).checked = true;
		BX('POPUP_STORE_ID').value = BX('BUYER_STORE').value;
		BX('POPUP_STORE_NAME').value =  BX.util.htmlspecialchars(arStore[BX('BUYER_STORE').value]["TITLE"]);
	}
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");?>