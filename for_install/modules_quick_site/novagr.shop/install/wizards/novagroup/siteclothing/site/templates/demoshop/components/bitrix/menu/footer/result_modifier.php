<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//deb($arResult);
if (!empty($arResult)):

	$top_key = -1;
	foreach($arResult as $key => $arItem):
		// ������ � ���������� MAIN_MENU - ��������  � ��������� ������ � ����������� � ����� ������
		// ��� ������
		if ($arItem["DEPTH_LEVEL"] == 1 && $arItem["PARAMS"]["MAIN_MENU"] == 1) {
			$arFormatted["END"][] = $arItem;
		} elseif ($arItem["DEPTH_LEVEL"] == 1 && $arItem["PARAMS"]["MAIN_MENU"] != 1) {
		//deb($arItem);
			//if ($arItem["TEXT"] != '���������') {
				continue;
		
			//}
	
			$top_key++;
			$arFormatted["TOP"][$top_key] = $arItem;
		} elseif ($arItem['PERMISSION'] > 'D') {
			$arFormatted["TOP"][$top_key]["ITEMS"][] = $arItem;
		}
	endforeach;
	
	/*foreach($arFormatted["TOP"] as $key => $arTopItem):
		
		if (count($arTopItem["ITEMS"]) > 12)
			$arFormatted["TOP"][$key]["LARGE"] = true;
		else
			$arFormatted["TOP"][$key]["LARGE"] = false;
	endforeach;*/
	
endif;
//deb("===========");
//deb($arFormatted["END"]);
//$j=0;
// ������������ ����� ����������� � �������� 2 ������(������� ������, �����)
if(is_array($arFormatted["TOP"]))
foreach($arFormatted["TOP"] as $key => $arTopItem) {
	//deb($arTopItem);
	
	$current = 0;
	$previous = '';
	foreach($arTopItem["ITEMS"] as $k => $val) {
		//deb($k);
		//deb($val);
		
		if ($val["DEPTH_LEVEL"] == 2 ) {
			
			if ($current>0) $arFormatted["TOP"][$key]["ITEMS"][$previous]["COUNT_CHILDS"] = $current;
			
			
			//$j++;
			/*if ($j>3) {
				deb($current);
				deb($previous);
				die('hhh');
			}*/
			$previous = $k;
			$current = 0;
			//$previousIndex = $k;
			//$previous
			//$current = 
		}
		
		if ($val["DEPTH_LEVEL"] == 3) {
			$current++;
		}
		
		
		//$previous
	}
	// ��� ���������� �������� 2�� ������( �����������)
	if ($current>0) {
		// ����������� ����� ����� � �������� - � ������ 4 ��������
		//$current
		//$rounded = round($current/4);
		$countTd = 3;
		$ceiled = ceil($current/$countTd);
		// ����� ������ <td>
		$pureTd = 0;
		if ($current>=$countTd) {
			$pureTd = $countTd-($current % $countTd);
		}
		//die('$current ' .$current . ' $$ceiled ' . $ceiled . ' $pureTd ' . $pureTd);
		
		
		$arFormatted["TOP"][$key]["ITEMS"][$previous]["PURE_TD"] = $pureTd;
		$arFormatted["TOP"][$key]["ITEMS"][$previous]["COUNT_CHILDS"] = $current;
		$arFormatted["TOP"][$key]["ITEMS"][$previous]["ROWS"] = $ceiled;
	}
}
//deb($arResult);
$arResult = $arFormatted["TOP"];

$arResult["END"] = $arFormatted["END"];
//deb("=============================");
//echo '<pre>'; print_r($arResult); echo '</pre>';
?>