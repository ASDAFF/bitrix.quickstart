<?
/**
 * Copyright (c) 3/12/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	CModule::IncludeModule('iblock');

	define("OPTION_IB", '#OPTION_IBLOCK_ID#');

	$win_cnt = substr_count($_REQUEST["ACDC"], "O");  // кол-во окон
	$door_cnt = substr_count($_REQUEST["ACDC"], "D");  // кол-во окон
	if($win_cnt > 0)
	{
		$fP = $_REQUEST['height'] + $_REQUEST['width'];
		$fP += $_REQUEST['height'] * ($win_cnt - 1);    // рама между окнами
		$fP *= (0.002/$win_cnt);   // периметр одного окна
		$fS = ($_REQUEST['height'] * $_REQUEST['width']) / (1000000 * $win_cnt);    // площадь одного окна
	}

	if($door_cnt > 0)
	{
		$fPd = $_REQUEST['d_height'] + $_REQUEST['d_width'];
		$fPd *= 0.0025;     //  0.005  - на пвх  в двери
		$fSd = ($_REQUEST['d_height'] * $_REQUEST['d_width'] / 1000000) * 0.8;  // часть окна не остеклена, будем считать 80% (точнее 70% стекло + 30% пвх, что по стоимости равно 80% стекла в сумме)
	}
	//панель
	$fTypePrice = 1;
	switch ($_REQUEST['type'])
	{
		case "1":
			break;
		case "2":
			//кирпич
			$fTypePrice = 1.1;
			break;
		default:
			$fTypePrice = 1;
	}

	function _getOptions($CODE = '', $OptionsID = array()){
		$OPTIONS = array();
		$UNITS = array();
		$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>OPTION_IB, "CODE"=>"UNIT"));
		while($enum_fields = $property_enums->GetNext())
		{
			$UNITS[$enum_fields["ID"]] = $enum_fields["XML_ID"];
		}
		$OtionsDirFilter =  array("IBLOCK_ID" => OPTION_IB);
		if ($CODE)  $OtionsDirFilter["CODE"] = $CODE;
		$dbOtionsDir = CIBlockSection::GetList(array(), $OtionsDirFilter, false, array("ID", "CODE"));
		while($arOtionsDir = $dbOtionsDir->GetNext()){

			$OtionsFilter =  array("IBLOCK_ID" => OPTION_IB, "SECTION_ID" => $arOtionsDir["ID"]);
			if($arOtionsDir["CODE"] == "options")
				if ($OptionsID)  $OtionsFilter["ID"] = $OptionsID; else continue;
			$dbOptions = CIBlockElement::GetList(array("SORT" => "ASC", "ID" => "ASC"), $OtionsFilter, false, false, array("ID", "CODE", "PROPERTY_PRICE", "PROPERTY_UNIT"));
			while($arOption = $dbOptions->GetNext()){
				$OPTIONS[$arOtionsDir["CODE"]][$arOption["CODE"]]["PRICE"] = $arOption["PROPERTY_PRICE_VALUE"];
				$OPTIONS[$arOtionsDir["CODE"]][$arOption["CODE"]]["UNIT"] = $UNITS[$arOption["PROPERTY_UNIT_ENUM_ID"]];
				$OPTIONS["UNITS"][$UNITS[$arOption["PROPERTY_UNIT_ENUM_ID"]]][$arOption["CODE"]] =  $arOption["PROPERTY_PRICE_VALUE"];
			}
		}
		return $OPTIONS;
	}


	$OPTIONS = _getOptions(false, $_REQUEST['service']);

	$arWinMap = array(
		'OA' => 'Одностворчатое глухое окно',
		'OB' => 'Одностворчатое поворотное окно',
		'OC' => 'Одностворчатое поворотно-откидное окно',
		'DA' => 'Одностворчатая глухая дверь',
		'DB' => 'Одностворчатая поворотная дверь',
		'DC' => 'Одностворчатая поворотно-откидная дверь'
	);

	$arACDC = explode("-", $_REQUEST["ACDC"]);

	$arWinPrice = array();


	foreach ($arACDC as $sKey => $sValue)
	{
		$arWinPrice[$sKey] = 0;
		$_P = $_S = 0;

		switch ($sValue[0])
		{
			case "O":
				$_P = $fP;
				$_S = $fS;
				break;
			case "D":
				$_P = $fPd;
				$_S = $fSd;
				break;
			default:
				$_P = 0;
				$_S = 0;
				break;

		}

		switch ($sValue[1])
		{
			case "C":
				$arWinPrice[$sKey] += $OPTIONS["mechanism"]["folding"]["PRICE"];
			case "B":
				$arWinPrice[$sKey] += $OPTIONS["mechanism"]["swivel"]["PRICE"];
			case "A":
				foreach($OPTIONS["UNITS"] as $unit => $option)
				{
					switch ($unit)
					{
						case "square":
							foreach($option as $price)
								$arWinPrice[$sKey] += $_S * $price;
							break;
						case "perimeter":
							foreach($option as $price)
								$arWinPrice[$sKey] += $_P * $price;
							break;
						case "additional":
							foreach($option as $code => $price)   {
								if(($code != "swivel") && ($code != "folding"))
									$arWinPrice[$sKey] += $price;
							}

							break;
						default;

					}
				}
				if (count($OPTIONS["UNITS"]["cost"]))
				{
					foreach($OPTIONS["UNITS"]["cost"] as $option => $price)
					{
						$arWinPrice[$sKey] +=  $arWinPrice[$sKey] * $price / 100;
					}
				}

				$arWinPrice[$sKey] *=  $fTypePrice; // зависимость от типа дома
				break;
			default:
		}
	}
?>

<div id="d_result" style="">
	<table id="price">
		<tbody>
			<tr><th>Наименование</th><th>Цена</th></tr>
			<? $fSum = 0;?>
			<? foreach ($arWinPrice as $sKey => $fValue):?>
				<? $fSum += $fValue;?>
				<tr><td><?=$arWinMap[$arACDC[$sKey]]?></td><td class="td-price"><?=ceil($fValue/10).'0'?> руб</td></tr>
				<? endforeach;?>
		</tbody>
	</table>

	<h2><?=ceil($fSum/10).'0'?> руб*</h2>
	<p>* Данная цена является ориентировочной. Более конкретную стоимость можно уточнить у <a href="/contacts/">менеджера</a>.</p>
	<br clear="all">
	<a class="next btn btn-lg" href="/calc/">Рассчитать новое окно</a>
	<br clear="all">
</div>
<? exit;?>