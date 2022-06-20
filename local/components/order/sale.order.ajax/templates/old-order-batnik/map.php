<?
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/sale.order.ajax/lang/'.LANGUAGE_ID.'/map.php');

CModule::IncludeModule('sale');
CModule::IncludeModule('catalog');
$location = "";
$arStore = array();
$arStoreId = array();

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

	if (count($arDelivery) > 0 && strlen($arDelivery["STORE"]) > 0)
	{
		$arStoreInfo = unserialize($arDelivery["STORE"]);
		foreach ($arStoreInfo as $val)
			$arStoreId[$val] = $val;
	}

	$arStoreLocation = array("yandex_scale" => 11, "PLACEMARKS" => array());

	$siteId = substr($_REQUEST["siteId"], 0, 2);

	$dbList = CCatalogStore::GetList(
		array("SORT" => "DESC", "ID" => "ASC"),
		array("ACTIVE" => "Y", "ID" => $arStoreId, "ISSUING_CENTER" => "Y", "+SITE_ID" => $siteId),
		false,
		false,
		array("ID", "SORT", "TITLE", "ADDRESS", "DESCRIPTION", "IMAGE_ID", "PHONE", "SCHEDULE", "GPS_N", "GPS_S", "SITE_ID", "ISSUING_CENTER", "EMAIL")
	);
	while ($arStoreTmp = $dbList->Fetch())
	{
		$arStore[$arStoreTmp["ID"]] = $arStoreTmp;

		if (intval($arStoreTmp["IMAGE_ID"]) > 0)
		{
			$arImage = CFile::GetFileArray($arStoreTmp["IMAGE_ID"]);
			$imgValue = CFile::ShowImage($arImage, 115, 115, "border=0", "", false);
			$arStore[$arStoreTmp["ID"]]["IMAGE"] = $imgValue;
			$arStore[$arStoreTmp["ID"]]["IMAGE_URL"] = $arImage["SRC"];
		}

		if (floatval($arStoreLocation["yandex_lat"]) <= 0)
			$arStoreLocation["yandex_lat"] = $arStoreTmp["GPS_N"];

		if (floatval($arStoreLocation["yandex_lon"]) <= 0)
			$arStoreLocation["yandex_lon"] = $arStoreTmp["GPS_S"];

		$arLocationTmp = array();
		$arLocationTmp["ID"] = $arStoreTmp["ID"];
		if (strlen($arStoreTmp["GPS_N"]) > 0)
			$arLocationTmp["LAT"] = $arStoreTmp["GPS_N"];
		if (strlen($arStoreTmp["GPS_S"]) > 0)
			$arLocationTmp["LON"] = $arStoreTmp["GPS_S"];
		if (strlen($arStoreTmp["TITLE"]) > 0)
			$arLocationTmp["TEXT"] = $arStoreTmp["TITLE"]."\r\n".$arStoreTmp["DESCRIPTION"];

		$arStoreLocation["PLACEMARKS"][] = $arLocationTmp;
	}

	$location = serialize($arStoreLocation);
}

$showImages = (isset($_REQUEST["showImages"]) && $_REQUEST["showImages"] == "Y") ? true : false;
?>
<html>
<head>
<?$APPLICATION->ShowHead();?>
<title><?$APPLICATION->ShowTitle()?></title>
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
	min-width: 400px;
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
.data .name {
	font-weight: bold;
	font-size: 12px;
}
.data .image_cell, .image {
	width: 120px;
	text-align: center !important;
	vertical-align: middle;
}
#store_table .store_row {
	font-size: 10px;
	cursor:pointer;
	display: block;
	padding: 2px 8px;
	border-radius: 3px;
	margin: 1px;
	border: 2px solid #F5F9F9;
}
#store_table .store_row:hover,
#store_table .checked {
	border: 2px solid rgb(45, 115, 157);
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
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#FFFFFF">

<?
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
			<table id="store_table">
			<?
			$i = 1;
			$countCount = count($arStore);
			$arDefaultStore = array_shift(array_values($arStore));

			foreach ($arStore as $val)
			{
				$checked = ($val["ID"] == $arDefaultStore["ID"]) ? "checked" : "";
				?>
				<tr class="store_row <?=$checked?>" id="row_<?=$val["ID"]?>" onclick="setChangeStore(<?=$val["ID"]?>);">
					<?
					if ($showImages)
					{
						?>
						<td class="image_cell">
							<div class="image">
								<?
								if (intval($val["IMAGE_ID"]) > 0):
								?>
									<a href="<?=$val["IMAGE_URL"]?>" target="_blank"><?=$val["IMAGE"]?></a>
								<?
								else:
								?>
									<img src="/bitrix/components/bitrix/sale.order.ajax/templates/visual/images/no_store.png" />
								<?
								endif;
								?>
							</div>
						</td>
						<?
					}
					?>
					<td class="<?=($countCount != $i)?"lilne":"last"?>">
						<label for="store_<?=$val["ID"]?>">
							<div class="name"><?=htmlspecialcharsbx($val["TITLE"])?></div>
							<div class="phone"><?=GetMessage('MAP_PHONE');?>: <?=htmlspecialcharsbx($val["PHONE"])?></div>
							<div class="email"><?=GetMessage('MAP_EMAIL');?>: <a href="mailto:<?=htmlspecialcharsbx($val["EMAIL"])?>"><?=htmlspecialcharsbx($val["EMAIL"])?></a></div>
							<div class="adres"><?=GetMessage('MAP_ADRES');?>: <?=htmlspecialcharsbx($val["ADDRESS"])?></div>
							<div class="shud"><?=GetMessage('MAP_WORK');?>: <?=htmlspecialcharsbx($val["SCHEDULE"])?></div>
						</label>
						<div class="desc"><?=GetMessage('MAP_DESC');?>: <?=htmlspecialcharsbx($val["DESCRIPTION"])?></div>
					</td>
				</tr>
				<?
			$i++;
			}
			?>
			</table>
		</div>
	</td>
</tr>
</table>
<input type="hidden" name="POPUP_STORE_ID" id="POPUP_STORE_ID" value="<?=$arDefaultStore["ID"]?>" >
<input type="hidden" name="POPUP_STORE_NAME" id="POPUP_STORE_NAME" value="<?=$arDefaultStore["TITLE"]?>" >

<script type="text/javascript">
	function setChangeStore(id)
	{
		var store = arStore[id];

		BX('POPUP_STORE_ID').value = id;
		BX('POPUP_STORE_NAME').value = BX.util.htmlspecialchars(store["TITLE"]);

		var tbl = BX('store_table');
		for (var i = 0; i < tbl.getElementsByTagName('tr').length; i++)
			BX.removeClass(BX(tbl.getElementsByTagName('tr')[i].id), 'checked');

		BX.addClass(BX('row_' + id), 'checked');

		if(window.GLOBAL_arMapObjects[objName])
			window.GLOBAL_arMapObjects[objName].panTo([parseFloat(store["GPS_N"]), parseFloat(store["GPS_S"])], {flying: 1});
	}

	if (BX('BUYER_STORE') && parseInt(BX('BUYER_STORE').value) > 0)
	{
		BX('POPUP_STORE_ID').value = BX('BUYER_STORE').value;
		BX('POPUP_STORE_NAME').value =  BX.util.htmlspecialchars(arStore[BX('BUYER_STORE').value]["TITLE"]);
	}
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
</body>
</html>