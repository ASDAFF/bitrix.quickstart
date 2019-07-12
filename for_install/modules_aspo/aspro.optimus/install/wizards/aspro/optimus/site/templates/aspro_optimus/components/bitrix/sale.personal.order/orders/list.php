<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
global $APPLICATION, $USER;
$curPage = $APPLICATION->GetCurPage(false);
$userID = $USER->GetID();

// get orders years
$cache = new CPHPCache();
$cache_time = $arParams['CACHE_TIME'];
$cache_path = 'optimus_order_year';
$cache_id = 'optimus_order_year_'.SITE_DIR.'_'.$userID;
if($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)){
	$res = $cache->GetVars();
	$arYear = $res["arYear"];
}
else{	
	$arYear = array();
	$rsOrder = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), array("USER_ID" => $userID));
	while($arOrder = $rsOrder->GetNext()){
		$date = explode(' ', $arOrder["DATE_INSERT"]);
		$year = explode('.', $date[0]);
		$arYear[] = $year[2];
	}
	$cache->StartDataCache($cache_time, $cache_id, $cache_path);
	$cache->EndDataCache(array("arYear" => $arYear));
}

// set year filter
$yearFiltered = false;
$yearFilter = '';
if(isset($_REQUEST["filter_date_from"]) && strlen($_REQUEST["filter_date_from"])){
	$aryearFiltered = explode('.', $_REQUEST["filter_date_from"]);
	$yearFiltered = $aryearFiltered[2];
	$yearFilter = 'filter_date_from=01.01.'.$yearFiltered.'&filter_date_to=31.12.'.$yearFiltered.'&filter=Y';
}

$arResult["FILTER_YEAR"] = array(
	'ALL' => array(
		"NAME" => GetMessage("ALL_YEARS"),
		"FILTER" => '',
		"ACTIVE" => ($yearFiltered ? false : true),
	),
);

if($arYear && is_array($arYear)){
	foreach($arYear as $year){
		$arResult["FILTER_YEAR"][$year] = array(
			"NAME" => $year,
			"FILTER" => 'filter_date_from=01.01.'.$year.'&filter_date_to=31.12.'.$year.'&filter=Y',
			"ACTIVE" => $year == $yearFiltered,
		);
	}
}

// set status filter
$statusFilter = '';
$arResult["FILTER"] = array(
	"ALL" => array(
		"NAME" => GetMessage("ALL_ORDERS"),
		"FILTER" => '',
		"ACTIVE" => false,
	),
	"CURRENT" => array(
		"NAME" => GetMessage("CURRENT_ORDERS"),
		"FILTER" => 'filter_history=N&filter_canceled=N',
		"ACTIVE" => false,
	),
	"FINISH" => array(
		"NAME" => GetMessage("FINISH_ORDERS"),
		"FILTER" => 'filter_status=F&filter_history=Y',
		"ACTIVE" => false,
	),
	"CANCEL" => array(
		"NAME" => GetMessage("CANCELED_ORDERS"),
		"FILTER" => 'filter_canceled=Y&filter_history=Y',
		"ACTIVE" => false,
	),
);

if($_REQUEST["filter_canceled"] == "Y" && $_REQUEST["filter_history"] == "Y"){
	$arResult["FILTER"]["CANCEL"]["ACTIVE"] = true;
	$statusFilter = $arResult["FILTER"]["CANCEL"]['FILTER'];
}
elseif($_REQUEST["filter_status"] == "F" && $_REQUEST["filter_history"] == "Y"){
	$arResult["FILTER"]["FINISH"]["ACTIVE"] = true;
	$statusFilter = $arResult["FILTER"]["FINISH"]['FILTER'];
}
elseif($_REQUEST["filter_canceled"] == "N"){
	$arResult["FILTER"]["CURRENT"]["ACTIVE"] = true;
	$statusFilter = $arResult["FILTER"]["CURRENT"]['FILTER'];
}
else{
	$arResult["FILTER"]["ALL"]["ACTIVE"] = true;
	$statusFilter = $arResult["FILTER"]["ALL"]['FILTER'];
}

if(!strlen($statusFilter)){
	$_REQUEST["show_all"] = "Y";
}
else{
	$_REQUEST["show_all"] = "N";
}
if(!strlen($statusFilter) && !strlen($yearFilter)){
	$_REQUEST["del_filter"] = "Y";
}
?>

<div class="module-order-history">
	<div class="filter_block border_block">
		<ul>
			<?foreach($arResult['FILTER'] as $item):?>
				<li class="prop <?=($item['ACTIVE'] ? 'active' : '' );?>">
					<?if($item['ACTIVE']):?>
						<span><?=$item['NAME']?></span>
					<?else:?>
						<?$filter = $item['FILTER'].(strlen($item['FILTER']) && strlen($yearFilter) ? '&'.$yearFilter : $yearFilter);?>
						<a href="<?=$curPage.(strlen($filter) ? '?'.htmlspecialcharsbx($filter): '')?>"><?=$item['NAME']?></a>
					<?endif;?>
				</li>
			<?endforeach;?>
		</ul>
		<?/*
		// unrem when bitrix fix bug with d7 orders getlist
		<?if($arResult["FILTER_YEAR"]):?>
			<div class="filter_year">
				<select name="">
					<?foreach($arResult["FILTER_YEAR"] as $i => $item):?>
						<?$filter = $item['FILTER'].(strlen($item['FILTER']) && strlen($statusFilter) ? '&'.$statusFilter : $statusFilter);?>
						<option value="<?=$i?>" <?=($item['ACTIVE'] ? 'selected' : '' )?> data-href="<?=$curPage.(strlen($filter) ? '?'.$filter : '')?>"><?=$item['NAME']?></option>
					<?endforeach;?>
				</select>
				<script>
				$('.filter_year select').change(function() {
					var id = $(this).val();
					var option = $(this).find('option[value=' + id + ']');
					location.href = option.attr('data-href');
				});
				</script>
			</div>
		<?endif;?>
		*/?>
		<div class="cls"></div>
	</div>
	<?$APPLICATION->IncludeComponent(
		"bitrix:sale.personal.order.list",
		"orders",
		array(
			"PATH_TO_PAYMENT" => $arParams["PATH_TO_PAYMENT"],
			"PATH_TO_DETAIL" => $arResult["PATH_TO_DETAIL"],
			"PATH_TO_CANCEL" => $arResult["PATH_TO_CANCEL"],
			"PATH_TO_COPY" => $arResult["PATH_TO_LIST"].'?ID=#ID#',
			"PATH_TO_BASKET" => $arParams["PATH_TO_BASKET"],
			"SAVE_IN_SESSION" => $arParams["SAVE_IN_SESSION"],
			"ORDERS_PER_PAGE" => $arParams["ORDERS_PER_PAGE"],
			"SET_TITLE" =>$arParams["SET_TITLE"],
			"ID" => $arResult["VARIABLES"]["ID"],
			"NAV_TEMPLATE" => $arParams["NAV_TEMPLATE"],
			//"filter_date_from" => $_REQUEST["filter_date_from"], // unrem when bitrix fix bug with d7 orders getlist
			//"filter_date_to" => $_REQUEST["filter_date_to"], // unrem when bitrix fix bug with d7 orders getlist
			"HISTORIC_STATUSES" => $arParams["HISTORIC_STATUSES"],
		),
		$component
	);?>
</div>